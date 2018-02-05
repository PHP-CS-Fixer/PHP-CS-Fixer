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

namespace PhpCsFixer\Fixer\PhpUnit;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\DocBlock\Annotation;
use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Filippo Tessarotto <zoeslam@gmail.com>
 */
final class PhpUnitOrderedCoversFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Order `@covers` annotation of PHPUnit tests.',
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
            ]
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
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        for ($index = $tokens->count() - 1; $index > 0; --$index) {
            if (!$tokens[$index]->isGivenKind(T_DOC_COMMENT) || false === strpos($tokens[$index]->getContent(), '@covers')) {
                continue;
            }

            $docBlock = new DocBlock($tokens[$index]->getContent());

            $coversMap = [];
            $linesToUpdate = [];
            foreach ($docBlock->getLines() as $line) {
                $rawContent = $line->getContent();
                if (false === strpos($rawContent, '@covers')) {
                    continue;
                }

                $linesToUpdate[] = $line;
                $comparableContent = preg_replace('/\*\s*@covers\s+(.+)/', '\1', trim($rawContent));
                $coversMap[$comparableContent] = $rawContent;
            }
            ksort($coversMap, SORT_STRING);

            foreach ($linesToUpdate as $line) {
                $newContent = array_shift($coversMap);
                $line->setContent($newContent);
            }

            $tokens[$index] = new Token([T_DOC_COMMENT, $docBlock->getContent()]);
        }
    }
}
