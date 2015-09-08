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

namespace PhpCsFixer\Fixer\ClassNotation;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symfony\Component\OptionsResolver\Options;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author SpacePossum
 */
final class FinalInternalClassFixer extends AbstractFixer implements ConfigurationDefinitionFixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Internal classes should be `final`.',
            [
                new CodeSample("<?php\n/**\n * @internal\n */\nclass Sample\n{\n}\n"),
                new CodeSample(
                    "<?php\n/** @CUSTOM */class A{}\n",
                    [
                        'annotation-white-list' => ['@Custom'],
                    ]
                ),
            ],
            null,
            'Changing classes to `final` might cause code execution to break.'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isAllTokenKindsFound([T_CLASS, T_DOC_COMMENT]);
    }

    /**
     * {@inheritdoc}
     */
    public function isRisky()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        for ($index = $tokens->count() - 1; 0 <= $index; --$index) {
            if (!$tokens[$index]->isGivenKind(T_CLASS) || !$this->isClassCandidate($tokens, $index)) {
                continue;
            }

            // make class final
            $tokens->insertAt(
                $index,
                [
                    new Token([T_FINAL, 'final']),
                    new Token([T_WHITESPACE, ' ']),
                ]
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition()
    {
        $annotationsAsserts = [static function (array $values) {
            foreach ($values as $value) {
                if (!is_string($value) || strlen($value) < 2 || '@' !== $value[0]) {
                    return false;
                }
            }

            return true;
        }];

        $annotationsNormalizer = static function (Options $options, array $value) {
            $newValue = [];
            foreach ($value as $key) {
                $newValue[strtolower(substr($key, 1))] = true;
            }

            return $newValue;
        };

        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('annotation-white-list', 'Class level annotations tags that must be set in order to fix the class. (case in sensitive)'))
                ->setAllowedTypes(['array'])
                ->setAllowedValues($annotationsAsserts)
                ->setDefault(['@internal'])
                ->setNormalizer($annotationsNormalizer)
                ->getOption(),
            (new FixerOptionBuilder('annotation-black-list', 'Class level annotations tags that must be omitted to fix the class, even if others are in white list. (case in sensitive)'))
                ->setAllowedTypes(['array'])
                ->setAllowedValues($annotationsAsserts)
                ->setDefault(['@final', '@Entity', '@ORM'])
                ->setNormalizer($annotationsNormalizer)
                ->getOption(),
        ]);
    }

    /**
     * @param Tokens $tokens
     * @param int    $index  T_CLASS index
     *
     * @return bool
     */
    private function isClassCandidate(Tokens $tokens, $index)
    {
        if ($tokens[$tokens->getPrevMeaningfulToken($index)]->isGivenKind([T_ABSTRACT, T_FINAL])) {
            return false; // ignore class; it is abstract or already final
        }

        $docToken = $tokens[$tokens->getPrevNonWhitespace($index)];

        if (!$docToken->isGivenKind(T_DOC_COMMENT)) {
            return false; // ignore class; it has no class-level PHPDoc
        }

        $doc = new DocBlock($docToken->getContent());
        $tags = [];

        foreach ($doc->getAnnotations() as $annotation) {
            $tag = strtolower($annotation->getTag()->getName());
            if (isset($this->configuration['annotation-black-list'][$tag])) {
                return false; // ignore class: class-level PHPDoc contains tag that has been black listed through configuration
            }

            $tags[$tag] = true;
        }

        foreach ($this->configuration['annotation-white-list'] as $tag => $true) {
            if (!isset($tags[$tag])) {
                return false; // ignore class: class-level PHPDoc does not contain all tags that has been white listed through configuration
            }
        }

        return true;
    }
}
