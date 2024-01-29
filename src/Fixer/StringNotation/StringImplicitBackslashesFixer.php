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

namespace PhpCsFixer\Fixer\StringNotation;

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
 * @author Filippo Tessarotto <zoeslam@gmail.com>
 * @author Michael Vorisek <https://github.com/mvorisek>
 */
final class StringImplicitBackslashesFixer extends AbstractFixer implements ConfigurableFixerInterface
{
    public function getDefinition(): FixerDefinitionInterface
    {
        $codeSample = <<<'EOF'
            <?php

            $singleQuoted = 'String with \" and My\Prefix\\';

            $doubleQuoted = "Interpret my \n but not my \a";

            $hereDoc = <<<HEREDOC
            Interpret my \100 but not my \999
            HEREDOC;

            EOF;

        return new FixerDefinition(
            'Handles implicit backslashes in strings and heredocs. Depending on the chosen strategy, it can escape implicit backslashes to ease the understanding of which are special chars interpreted by PHP and which not (`escape`), or it can remove these additional backslashes if you find them superfluous (`unescape`). You can also leave them as-is using `ignore` strategy.',
            [
                new CodeSample($codeSample),
                new CodeSample(
                    $codeSample,
                    ['single_quoted' => 'escape']
                ),
                new CodeSample(
                    $codeSample,
                    ['double_quoted' => 'unescape']
                ),
                new CodeSample(
                    $codeSample,
                    ['heredoc' => 'unescape']
                ),
            ],
            'In PHP double-quoted strings and heredocs some chars like `n`, `$` or `u` have special meanings if preceded by a backslash '
            .'(and some are special only if followed by other special chars), while a backslash preceding other chars are interpreted like a plain '
            .'backslash. The precise list of those special chars is hard to remember and to identify quickly: this fixer escapes backslashes '
            ."that do not start a special interpretation with the char after them.\n"
            .'It is possible to fix also single-quoted strings: in this case there is no special chars apart from single-quote and backslash '
            .'itself, so the fixer simply ensure that all backslashes are escaped. Both single and double backslashes are allowed in single-quoted '
            .'strings, so the purpose in this context is mainly to have a uniformed way to have them written all over the codebase.'
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAnyTokenKindsFound([T_ENCAPSED_AND_WHITESPACE, T_CONSTANT_ENCAPSED_STRING]);
    }

    /**
     * {@inheritdoc}
     *
     * Must run before HeredocToNowdocFixer, SingleQuoteFixer.
     * Must run after BacktickToShellExecFixer, MultilineStringToHeredocFixer.
     */
    public function getPriority(): int
    {
        return 15;
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        $singleQuotedReservedRegex = '[\'\\\]';
        $doubleQuotedReservedRegex = '(?:[efnrtv$"\\\0-7]|x[0-9A-Fa-f]|u{|$)';
        $heredocSyntaxReservedRegex = '(?:[efnrtv$\\\0-7]|x[0-9A-Fa-f]|u{|$)';

        $doubleQuoteOpened = false;
        foreach ($tokens as $index => $token) {
            if ($token->equalsAny(['"', 'b"', 'B"'])) {
                $doubleQuoteOpened = !$doubleQuoteOpened;
            }

            if (!$token->isGivenKind([T_ENCAPSED_AND_WHITESPACE, T_CONSTANT_ENCAPSED_STRING])) {
                continue;
            }

            $content = $token->getContent();
            if (!str_contains($content, '\\')) {
                continue;
            }

            // nowdoc syntax
            if ($token->isGivenKind(T_ENCAPSED_AND_WHITESPACE) && '\'' === substr(rtrim($tokens[$index - 1]->getContent()), -1)) {
                continue;
            }

            $firstTwoCharacters = strtolower(substr($content, 0, 2));
            $isSingleQuotedString = $token->isGivenKind(T_CONSTANT_ENCAPSED_STRING) && ('\'' === $content[0] || 'b\'' === $firstTwoCharacters);
            $isDoubleQuotedString =
                ($token->isGivenKind(T_CONSTANT_ENCAPSED_STRING) && ('"' === $content[0] || 'b"' === $firstTwoCharacters))
                || ($token->isGivenKind(T_ENCAPSED_AND_WHITESPACE) && $doubleQuoteOpened);

            if ($isSingleQuotedString
                ? 'ignore' === $this->configuration['single_quoted']
                : ($isDoubleQuotedString
                    ? 'ignore' === $this->configuration['double_quoted']
                    : 'ignore' === $this->configuration['heredoc'])
            ) {
                continue;
            }

            $escapeBackslashes = $isSingleQuotedString
                ? 'escape' === $this->configuration['single_quoted']
                : ($isDoubleQuotedString
                    ? 'escape' === $this->configuration['double_quoted']
                    : 'escape' === $this->configuration['heredoc']);

            $reservedRegex = $isSingleQuotedString
                ? $singleQuotedReservedRegex
                : ($isDoubleQuotedString
                    ? $doubleQuotedReservedRegex
                    : $heredocSyntaxReservedRegex);

            if ($escapeBackslashes) {
                $regex = '/(?<!\\\)\\\((?:\\\\\\\)*)(?!'.$reservedRegex.')/';
                $newContent = Preg::replace($regex, '\\\\\\\$1', $content);
            } else {
                $regex = '/(?<!\\\)\\\\\\\((?:\\\\\\\)*)(?!'.$reservedRegex.')/';
                $newContent = Preg::replace($regex, '\\\$1', $content);
            }

            if ($newContent !== $content) {
                $tokens[$index] = new Token([$token->getId(), $newContent]);
            }
        }
    }

    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('single_quoted', 'Whether to escape backslashes in single-quoted strings.'))
                ->setAllowedValues(['escape', 'unescape', 'ignore'])
                ->setDefault('unescape')
                ->getOption(),
            (new FixerOptionBuilder('double_quoted', 'Whether to escape backslashes in double-quoted strings.'))
                ->setAllowedValues(['escape', 'unescape', 'ignore'])
                ->setDefault('escape')
                ->getOption(),
            (new FixerOptionBuilder('heredoc', 'Whether to escape backslashes in heredoc syntax.'))
                ->setAllowedValues(['escape', 'unescape', 'ignore'])
                ->setDefault('escape')
                ->getOption(),
        ]);
    }
}
