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

namespace PhpCsFixer\Fixer\Casing;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * Fixer for constants case.
 *
 * @author Pol Dellaiera <pol.dellaiera@protonmail.com>
 */
final class ConstantCaseFixer extends AbstractFixer implements ConfigurableFixerInterface
{
    /**
     * Hold the function that will be used to convert the constants.
     *
     * @var callable
     */
    private $fixFunction;

    public function configure(array $configuration): void
    {
        parent::configure($configuration);

        if ('lower' === $this->configuration['case']) {
            $this->fixFunction = static function (string $content): string {
                return strtolower($content);
            };
        }

        if ('upper' === $this->configuration['case']) {
            $this->fixFunction = static function (string $content): string {
                return strtoupper($content);
            };
        }
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'The PHP constants `true`, `false`, and `null` MUST be written using the correct casing.',
            [
                new CodeSample("<?php\n\$a = FALSE;\n\$b = True;\n\$c = nuLL;\n"),
                new CodeSample("<?php\n\$a = FALSE;\n\$b = True;\n\$c = nuLL;\n", ['case' => 'upper']),
            ]
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_STRING);
    }

    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('case', 'Whether to use the `upper` or `lower` case syntax.'))
                ->setAllowedValues(['upper', 'lower'])
                ->setDefault('lower')
                ->getOption(),
        ]);
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        $fixFunction = $this->fixFunction;

        foreach ($tokens as $index => $token) {
            if (!$token->isNativeConstant()) {
                continue;
            }

            if (
                $this->isNeighbourAccepted($tokens, $tokens->getPrevMeaningfulToken($index))
                && $this->isNeighbourAccepted($tokens, $tokens->getNextMeaningfulToken($index))
                && !$this->isEnumCaseName($tokens, $index)
            ) {
                $tokens[$index] = new Token([$token->getId(), $fixFunction($token->getContent())]);
            }
        }
    }

    private function isNeighbourAccepted(Tokens $tokens, int $index): bool
    {
        static $forbiddenTokens = null;

        if (null === $forbiddenTokens) {
            $forbiddenTokens = array_merge(
                [
                    T_AS,
                    T_CLASS,
                    T_CONST,
                    T_EXTENDS,
                    T_IMPLEMENTS,
                    T_INSTANCEOF,
                    T_INSTEADOF,
                    T_INTERFACE,
                    T_NEW,
                    T_NS_SEPARATOR,
                    T_PAAMAYIM_NEKUDOTAYIM,
                    T_TRAIT,
                    T_USE,
                    CT::T_USE_TRAIT,
                    CT::T_USE_LAMBDA,
                ],
                Token::getObjectOperatorKinds()
            );
        }

        $token = $tokens[$index];

        if ($token->equalsAny(['{', '}'])) {
            return false;
        }

        return !$token->isGivenKind($forbiddenTokens);
    }

    private function isEnumCaseName(Tokens $tokens, int $index): bool
    {
        if (!\defined('T_ENUM') || !$tokens->isTokenKindFound(T_ENUM)) { // @TODO: drop condition when PHP 8.1+ is required
            return false;
        }

        $prevIndex = $tokens->getPrevMeaningfulToken($index);

        if (null === $prevIndex || !$tokens[$prevIndex]->isGivenKind(T_CASE)) {
            return false;
        }

        if (!$tokens->isTokenKindFound(T_SWITCH)) {
            return true;
        }

        $prevIndex = $tokens->getPrevTokenOfKind($prevIndex, [[T_ENUM], [T_SWITCH]]);

        return null !== $prevIndex && $tokens[$prevIndex]->isGivenKind(T_ENUM);
    }
}
