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

namespace PhpCsFixer\Fixer\DoctrineAnnotation;

use PhpCsFixer\AbstractDoctrineAnnotationFixer;
use PhpCsFixer\Doctrine\Annotation\DocLexer;
use PhpCsFixer\Doctrine\Annotation\Token;
use PhpCsFixer\Doctrine\Annotation\Tokens;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;

/**
 * Adds braces to Doctrine annotations when missing.
 */
final class DoctrineAnnotationBracesFixer extends AbstractDoctrineAnnotationFixer
{
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Doctrine annotations without arguments must use the configured syntax.',
            [
                new CodeSample(
                    "<?php\n/**\n * @Foo()\n */\nclass Bar {}\n"
                ),
                new CodeSample(
                    "<?php\n/**\n * @Foo\n */\nclass Bar {}\n",
                    ['syntax' => 'with_braces']
                ),
            ]
        );
    }

    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            ...parent::createConfigurationDefinition()->getOptions(),
            (new FixerOptionBuilder('syntax', 'Whether to add or remove braces.'))
                ->setAllowedValues(['with_braces', 'without_braces'])
                ->setDefault('without_braces')
                ->getOption(),
        ]);
    }

    protected function fixAnnotations(Tokens $doctrineAnnotationTokens): void
    {
        if ('without_braces' === $this->configuration['syntax']) {
            $this->removesBracesFromAnnotations($doctrineAnnotationTokens);
        } else {
            $this->addBracesToAnnotations($doctrineAnnotationTokens);
        }
    }

    private function addBracesToAnnotations(Tokens $tokens): void
    {
        foreach ($tokens as $index => $token) {
            if (!$tokens[$index]->isType(DocLexer::T_AT)) {
                continue;
            }

            $braceIndex = $tokens->getNextMeaningfulToken($index + 1);
            if (null !== $braceIndex && $tokens[$braceIndex]->isType(DocLexer::T_OPEN_PARENTHESIS)) {
                continue;
            }

            $tokens->insertAt($index + 2, new Token(DocLexer::T_OPEN_PARENTHESIS, '('));
            $tokens->insertAt($index + 3, new Token(DocLexer::T_CLOSE_PARENTHESIS, ')'));
        }
    }

    private function removesBracesFromAnnotations(Tokens $tokens): void
    {
        for ($index = 0, $max = \count($tokens); $index < $max; ++$index) {
            if (!$tokens[$index]->isType(DocLexer::T_AT)) {
                continue;
            }

            $openBraceIndex = $tokens->getNextMeaningfulToken($index + 1);
            if (null === $openBraceIndex) {
                continue;
            }

            if (!$tokens[$openBraceIndex]->isType(DocLexer::T_OPEN_PARENTHESIS)) {
                continue;
            }

            $closeBraceIndex = $tokens->getNextMeaningfulToken($openBraceIndex);
            if (null === $closeBraceIndex) {
                continue;
            }

            if (!$tokens[$closeBraceIndex]->isType(DocLexer::T_CLOSE_PARENTHESIS)) {
                continue;
            }

            for ($currentIndex = $index + 2; $currentIndex <= $closeBraceIndex; ++$currentIndex) {
                $tokens[$currentIndex]->clear();
            }
        }
    }
}
