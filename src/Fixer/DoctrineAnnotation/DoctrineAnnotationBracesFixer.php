<?php

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

use Doctrine\Common\Annotations\DocLexer;
use PhpCsFixer\AbstractDoctrineAnnotationFixer;
use PhpCsFixer\Doctrine\Annotation\Token;
use PhpCsFixer\Doctrine\Annotation\Tokens;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;

/**
 * Adds braces to Doctrine annotations when missing.
 */
final class DoctrineAnnotationBracesFixer extends AbstractDoctrineAnnotationFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Doctrine annotations without arguments must use the configured syntax.',
            array(
                new CodeSample(
                    "<?php\n/**\n * @Foo()\n */\nclass Bar {}",
                    array('syntax' => 'without_braces')
                ),
                new CodeSample(
                    "<?php\n/**\n * @Foo\n */\nclass Bar {}",
                    array('syntax' => 'with_braces')
                ),
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition()
    {
        $options = parent::createConfigurationDefinition()->getOptions();

        $syntax = new FixerOptionBuilder('syntax', 'Whether to add or remove braces.');
        $options[] = $syntax
            ->setAllowedValues(array('with_braces', 'without_braces'))
            ->setDefault('without_braces')
            ->getOption()
        ;

        return new FixerConfigurationResolver($options);
    }

    /**
     * {@inheritdoc}
     */
    protected function fixAnnotations(Tokens $tokens)
    {
        if ('without_braces' === $this->configuration['syntax']) {
            $this->removesBracesFromAnnotations($tokens);
        } else {
            $this->addBracesToAnnotations($tokens);
        }
    }

    /**
     * @param Tokens $tokens
     */
    private function addBracesToAnnotations(Tokens $tokens)
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

    /**
     * @param Tokens $tokens
     */
    private function removesBracesFromAnnotations(Tokens $tokens)
    {
        for ($index = 0, $max = count($tokens); $index < $max; ++$index) {
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
