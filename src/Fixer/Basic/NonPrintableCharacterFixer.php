<?php

declare(strict_types=1);

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Fixer\Basic;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * Removes Zero-width space (ZWSP), Non-breaking space (NBSP) and other invisible unicode symbols.
 *
 * @author Ivan Boprzenkov <ivan.borzenkov@gmail.com>
 */
final class NonPrintableCharacterFixer extends AbstractFixer implements ConfigurableFixerInterface
{
    /**
     * @var array<string, string[]>
     */
    private $symbolsReplace;

    /**
     * @var int[]
     */
    private static $tokens = [
        T_STRING_VARNAME,
        T_INLINE_HTML,
        T_VARIABLE,
        T_COMMENT,
        T_ENCAPSED_AND_WHITESPACE,
        T_CONSTANT_ENCAPSED_STRING,
        T_DOC_COMMENT,
    ];

    public function __construct()
    {
        parent::__construct();

        $this->symbolsReplace = [
            pack('H*', 'e2808b') => ['', '200b'], // ZWSP U+200B
            pack('H*', 'e28087') => [' ', '2007'], // FIGURE SPACE U+2007
            pack('H*', 'e280af') => [' ', '202f'], // NBSP U+202F
            pack('H*', 'e281a0') => ['', '2060'], // WORD JOINER U+2060
            pack('H*', 'c2a0') => [' ', 'a0'], // NO-BREAK SPACE U+A0
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Remove Zero-width space (ZWSP), Non-breaking space (NBSP) and other invisible unicode symbols.',
            [
                new CodeSample(
                    '<?php echo "'.pack('H*', 'e2808b').'Hello'.pack('H*', 'e28087').'World'.pack('H*', 'c2a0')."!\";\n"
                ),
                new CodeSample(
                    '<?php echo "'.pack('H*', 'e2808b').'Hello'.pack('H*', 'e28087').'World'.pack('H*', 'c2a0')."!\";\n",
                    ['use_escape_sequences_in_strings' => false]
                ),
            ],
            null,
            'Risky when strings contain intended invisible characters.'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isRisky(): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAnyTokenKindsFound(self::$tokens);
    }

    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('use_escape_sequences_in_strings', 'Whether characters should be replaced with escape sequences in strings.'))
                ->setAllowedTypes(['bool'])
                ->setDefault(true)
                ->getOption(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        $replacements = [];
        $escapeSequences = [];

        foreach ($this->symbolsReplace as $character => [$replacement, $codepoint]) {
            $replacements[$character] = $replacement;
            $escapeSequences[$character] = '\u{'.$codepoint.'}';
        }

        foreach ($tokens as $index => $token) {
            $content = $token->getContent();

            if (
                $this->configuration['use_escape_sequences_in_strings']
                && $token->isGivenKind([T_CONSTANT_ENCAPSED_STRING, T_ENCAPSED_AND_WHITESPACE])
            ) {
                if (!Preg::match('/'.implode('|', array_keys($escapeSequences)).'/', $content)) {
                    continue;
                }

                $previousToken = $tokens[$index - 1];
                $stringTypeChanged = false;
                $swapQuotes = false;

                if ($previousToken->isGivenKind(T_START_HEREDOC)) {
                    $previousTokenContent = $previousToken->getContent();

                    if (str_contains($previousTokenContent, '\'')) {
                        $tokens[$index - 1] = new Token([T_START_HEREDOC, str_replace('\'', '', $previousTokenContent)]);
                        $stringTypeChanged = true;
                    }
                } elseif (str_starts_with($content, "'")) {
                    $stringTypeChanged = true;
                    $swapQuotes = true;
                }

                if ($swapQuotes) {
                    $content = str_replace("\\'", "'", $content);
                }

                if ($stringTypeChanged) {
                    $content = Preg::replace('/(\\\\{1,2})/', '\\\\\\\\', $content);
                    $content = str_replace('$', '\$', $content);
                }

                if ($swapQuotes) {
                    $content = str_replace('"', '\"', $content);
                    $content = Preg::replace('/^\'(.*)\'$/s', '"$1"', $content);
                }

                $tokens[$index] = new Token([$token->getId(), strtr($content, $escapeSequences)]);

                continue;
            }

            if ($token->isGivenKind(self::$tokens)) {
                $newContent = strtr($content, $replacements);

                // variable name cannot contain space
                if ($token->isGivenKind([T_STRING_VARNAME, T_VARIABLE]) && str_contains($newContent, ' ')) {
                    continue;
                }

                // multiline comment must have "*/" only at the end
                if ($token->isGivenKind([T_COMMENT, T_DOC_COMMENT]) && str_starts_with($newContent, '/*') && strpos($newContent, '*/') !== \strlen($newContent) - 2) {
                    continue;
                }

                $tokens[$index] = new Token([$token->getId(), $newContent]);
            }
        }
    }
}
