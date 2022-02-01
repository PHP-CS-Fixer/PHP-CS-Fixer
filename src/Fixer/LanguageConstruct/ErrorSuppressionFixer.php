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

namespace PhpCsFixer\Fixer\LanguageConstruct;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Analyzer\FunctionsAnalyzer;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Jules Pietri <jules@heahprod.com>
 * @author Kuba Werłos <werlos@gmail.com>
 */
final class ErrorSuppressionFixer extends AbstractFixer implements ConfigurableFixerInterface
{
    /**
     * @internal
     */
    public const OPTION_MUTE_DEPRECATION_ERROR = 'mute_deprecation_error';

    /**
     * @internal
     */
    public const OPTION_NOISE_REMAINING_USAGES = 'noise_remaining_usages';

    /**
     * @internal
     */
    public const OPTION_NOISE_REMAINING_USAGES_EXCLUDE = 'noise_remaining_usages_exclude';

    /**
     * {@inheritdoc}
     */
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Error control operator should be added to deprecation notices and/or removed from other cases.',
            [
                new CodeSample("<?php\ntrigger_error('Warning.', E_USER_DEPRECATED);\n"),
                new CodeSample(
                    "<?php\n@mkdir(\$dir);\n@unlink(\$path);\n",
                    [self::OPTION_NOISE_REMAINING_USAGES => true]
                ),
                new CodeSample(
                    "<?php\n@mkdir(\$dir);\n@unlink(\$path);\n",
                    [
                        self::OPTION_NOISE_REMAINING_USAGES => true,
                        self::OPTION_NOISE_REMAINING_USAGES_EXCLUDE => ['unlink'],
                    ]
                ),
            ],
            null,
            'Risky because adding/removing `@` might cause changes to code behaviour or if `trigger_error` function is overridden.'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_STRING);
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
    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder(self::OPTION_MUTE_DEPRECATION_ERROR, 'Whether to add `@` in deprecation notices.'))
                ->setAllowedTypes(['bool'])
                ->setDefault(true)
                ->getOption(),
            (new FixerOptionBuilder(self::OPTION_NOISE_REMAINING_USAGES, 'Whether to remove `@` in remaining usages.'))
                ->setAllowedTypes(['bool'])
                ->setDefault(false)
                ->getOption(),
            (new FixerOptionBuilder(self::OPTION_NOISE_REMAINING_USAGES_EXCLUDE, 'List of global functions to exclude from removing `@`'))
                ->setAllowedTypes(['array'])
                ->setDefault([])
                ->getOption(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        $functionsAnalyzer = new FunctionsAnalyzer();
        $excludedFunctions = array_map(static function (string $function): string {
            return strtolower($function);
        }, $this->configuration[self::OPTION_NOISE_REMAINING_USAGES_EXCLUDE]);

        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            $token = $tokens[$index];

            if (true === $this->configuration[self::OPTION_NOISE_REMAINING_USAGES] && $token->equals('@')) {
                $tokens->clearAt($index);

                continue;
            }

            if (!$functionsAnalyzer->isGlobalFunctionCall($tokens, $index)) {
                continue;
            }

            $functionIndex = $index;
            $startIndex = $index;
            $prevIndex = $tokens->getPrevMeaningfulToken($index);

            if ($tokens[$prevIndex]->isGivenKind(T_NS_SEPARATOR)) {
                $startIndex = $prevIndex;
                $prevIndex = $tokens->getPrevMeaningfulToken($startIndex);
            }

            $index = $prevIndex;

            if ($this->isDeprecationErrorCall($tokens, $functionIndex)) {
                if (false === $this->configuration[self::OPTION_MUTE_DEPRECATION_ERROR]) {
                    continue;
                }

                if ($tokens[$prevIndex]->equals('@')) {
                    continue;
                }

                $tokens->insertAt($startIndex, new Token('@'));

                continue;
            }

            if (!$tokens[$prevIndex]->equals('@')) {
                continue;
            }

            if (true === $this->configuration[self::OPTION_NOISE_REMAINING_USAGES] && !\in_array($tokens[$functionIndex]->getContent(), $excludedFunctions, true)) {
                $tokens->clearAt($index);
            }
        }
    }

    private function isDeprecationErrorCall(Tokens $tokens, int $index): bool
    {
        if ('trigger_error' !== strtolower($tokens[$index]->getContent())) {
            return false;
        }

        $endBraceIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $tokens->getNextTokenOfKind($index, [T_STRING, '(']));
        $prevIndex = $tokens->getPrevMeaningfulToken($endBraceIndex);

        if ($tokens[$prevIndex]->equals(',')) {
            $prevIndex = $tokens->getPrevMeaningfulToken($prevIndex);
        }

        return $tokens[$prevIndex]->equals([T_STRING, 'E_USER_DEPRECATED']);
    }
}
