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

namespace PhpCsFixer\Fixer\Phpdoc;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use PhpCsFixer\FixerConfiguration\AllowedValueSubset;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Filippo Tessarotto <zoeslam@gmail.com>
 * @author Andreas Möller <am@localheinz.com>
 */
final class PhpdocOrderByValueFixer extends AbstractFixer implements ConfigurationDefinitionFixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Order phpdoc tags by value.',
            [
                new CodeSample(
                    '<?php
/**
 * @covers Foo
 * @covers Bar
 */
final class MyTest extends \PHPUnit_Framework_TestCase
{}
'
                ),
                new CodeSample(
                    '<?php
/**
 * @author Bob
 * @author Alice
 */
final class MyTest extends \PHPUnit_Framework_TestCase
{}
',
                    [
                        'annotations' => [
                            'author',
                        ],
                    ]
                ),
            ]
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run before PhpdocAlignFixer.
     * Must run after CommentToPhpdocFixer, PhpUnitFqcnAnnotationFixer, PhpdocIndentFixer, PhpdocScalarFixer, PhpdocToCommentFixer, PhpdocTypesFixer.
     */
    public function getPriority()
    {
        return -10;
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
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        if ([] === $this->configuration['annotations']) {
            return;
        }

        for ($index = $tokens->count() - 1; $index > 0; --$index) {
            foreach ($this->configuration['annotations'] as $type) {
                $findPattern = sprintf(
                    '/@%s\s.+@%s\s/s',
                    $type,
                    $type
                );

                if (
                    !$tokens[$index]->isGivenKind(T_DOC_COMMENT)
                    || 0 === Preg::match($findPattern, $tokens[$index]->getContent())
                ) {
                    continue;
                }

                $docBlock = new DocBlock($tokens[$index]->getContent());

                $annotations = $docBlock->getAnnotationsOfType($type);

                $annotationMap = [];

                $replacePattern = sprintf(
                    '/\*\s*@%s\s+(.+)/',
                    $type
                );

                foreach ($annotations as $annotation) {
                    $rawContent = $annotation->getContent();

                    $comparableContent = Preg::replace(
                        $replacePattern,
                        '\1',
                        strtolower(trim($rawContent))
                    );

                    $annotationMap[$comparableContent] = $rawContent;
                }

                $orderedAnnotationMap = $annotationMap;

                ksort($orderedAnnotationMap, SORT_STRING);

                if ($orderedAnnotationMap === $annotationMap) {
                    continue;
                }

                $lines = $docBlock->getLines();

                foreach (array_reverse($annotations) as $annotation) {
                    array_splice(
                        $lines,
                        $annotation->getStart(),
                        $annotation->getEnd() - $annotation->getStart() + 1,
                        array_pop($orderedAnnotationMap)
                    );
                }

                $tokens[$index] = new Token([T_DOC_COMMENT, implode('', $lines)]);
            }
        }
    }

    protected function createConfigurationDefinition()
    {
        $allowedValues = [
            'author',
            'covers',
            'coversNothing',
            'dataProvider',
            'depends',
            'group',
            'internal',
            'requires',
            'throws',
            'uses',
        ];

        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('annotations', 'List of annotations to order, e.g. `["covers"]`.'))
                ->setAllowedTypes([
                    'array',
                ])
                ->setAllowedValues([
                    new AllowedValueSubset($allowedValues),
                ])
                ->setDefault([
                    'covers',
                ])
                ->getOption(),
        ]);
    }
}
