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
            $this->fixFunction = static fn (string $content): string => strtolower($content);
        }

        if ('upper' === $this->configuration['case']) {
            $this->fixFunction = static fn (string $content): string => strtoupper($content);
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
        static $forbiddenPrevKinds = null;
        if (null === $forbiddenPrevKinds) {
            $forbiddenPrevKinds = [
                T_EXTENDS,
                T_IMPLEMENTS,
                T_INSTANCEOF,
                T_NAMESPACE,
                T_NEW,
                T_NS_SEPARATOR,
                ...Token::getObjectOperatorKinds(),
            ];
        }

        foreach ($tokens as $index => $token) {
            if (!$token->equalsAny([[T_STRING, 'true'], [T_STRING, 'false'], [T_STRING, 'null']], false)) {
                continue;
            }

            $prevIndex = $tokens->getPrevMeaningfulToken($index);
            if ($tokens[$prevIndex]->isGivenKind($forbiddenPrevKinds)) {
                continue;
            }

            $nextIndex = $tokens->getNextMeaningfulToken($index);
            if ($tokens[$nextIndex]->isGivenKind(T_PAAMAYIM_NEKUDOTAYIM) || $tokens[$nextIndex]->equalsAny(['='], false)) {
                continue;
            }

            if ($tokens[$prevIndex]->isGivenKind(T_CASE) && $tokens[$nextIndex]->equals(';')) {
                continue;
            }

            $tokens[$index] = new Token([$token->getId(), ($this->fixFunction)($token->getContent())]);
        }
    }
}
