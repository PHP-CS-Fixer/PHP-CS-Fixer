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
use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\DocBlock\Line;
use PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Indicator\PhpUnitTestCaseIndicator;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;

/**
 * @author Gert de Pagter
 */
final class PhpUnitTestAnnotationFixer extends AbstractFixer implements ConfigurationDefinitionFixerInterface, WhitespacesAwareFixerInterface
{
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
    public function getDefinition()
    {
        return new FixerDefinition(
            'Adds or removes @test annotations from tests, following configuration.',
            [
                new CodeSample('<?php
class Test extends \\PhpUnit\\FrameWork\\TestCase
{
    /**
     * @test
     */
    public function itDoesSomething() {} }'.$this->whitespacesConfig->getLineEnding()),
                new CodeSample('<?php
class Test extends \\PhpUnit\\FrameWork\\TestCase
{
public function testItDoesSomething() {}}'.$this->whitespacesConfig->getLineEnding(), ['style' => 'annotation']),
                ],
            null,
            'This fixer may change the name of your tests, and could cause incompatibility with'.
            ' abstract classes or interfaces.'
        );
    }

    public function getPriority()
    {
        // must be run before the PhpdocSeparationFixer and PhpdocOrderFixer
        return 10;
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isAllTokenKindsFound([T_CLASS, T_FUNCTION]);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        foreach (array_reverse($this->findPhpUnitClasses($tokens)) as $indexes) {
            if ('annotation' === $this->configuration['style']) {
                $this->applyTestAnnotation($tokens, $indexes[0], $indexes[1]);
            } else {
                $this->removeTestAnnotation($tokens, $indexes[0], $indexes[1]);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition()
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('style', 'Whether to use the @test annotation or not.'))
                ->setAllowedValues(['prefix', 'annotation'])
                ->setDefault('prefix')
                ->getOption(),
            (new FixerOptionBuilder('case', 'Whether to camel or snake case when adding the test prefix'))
                ->setAllowedValues(['camel', 'snake'])
                ->setDefault('camel')
                ->getOption(),
        ]);
    }

    /**
     * @param Tokens $tokens
     *
     * @return int[][] array of [start, end] indexes from sooner to later classes
     */
    private function findPhpUnitClasses(Tokens $tokens)
    {
        $phpUnitTestCaseIndicator = new PhpUnitTestCaseIndicator();
        $phpunitClasses = [];

        for ($index = 0, $limit = $tokens->count() - 1; $index < $limit; ++$index) {
            if ($tokens[$index]->isGivenKind(T_CLASS) && $phpUnitTestCaseIndicator->isPhpUnitClass($tokens, $index)) {
                $index = $tokens->getNextTokenOfKind($index, ['{']);
                $endIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $index);
                $phpunitClasses[] = [$index, $endIndex];
                $index = $endIndex;
            }
        }

        return $phpunitClasses;
    }

    /**
     * @param Tokens $tokens
     * @param int    $startIndex
     * @param int    $endIndex
     */
    private function applyTestAnnotation(Tokens $tokens, $startIndex, $endIndex)
    {
        for ($i = $endIndex - 1; $i > $startIndex; --$i) {
            if (!$this->isFunctionTest($tokens, $i)) {
                continue;
            }

            $functionNameIndex = $tokens->getNextMeaningfulToken($i);
            $functionName = $tokens[$functionNameIndex]->getContent();

            //if the function name stats with test, we remove that
            if ($this->startsWith('test', $functionName)) {
                $newFunctionName = $this->removeTestFromFunctionName($functionName);
                $tokens[$functionNameIndex] = new Token([T_STRING, $newFunctionName]);
            }

            $docBlockIndex = $this->getDockBlockIndex($tokens, $i);

            //Create a new docblock if it didn't have one before;
            if (!$this->doesFunctionHaveDocBlock($tokens, $i)) {
                $this->createDocBlock($tokens, $docBlockIndex);

                continue;
            }
            $lines = $this->updateDocBlock($tokens, $docBlockIndex);

            $lines = $this->addTestToDocBlockIfNeeded($lines, $tokens, $docBlockIndex);

            $lines = implode($lines);
            $tokens[$docBlockIndex] = new Token([T_DOC_COMMENT, $lines]);
        }
    }

    /**
     * @param Tokens $tokens
     * @param int    $startIndex
     * @param int    $endIndex
     */
    private function removeTestAnnotation(Tokens $tokens, $startIndex, $endIndex)
    {
        for ($i = $endIndex - 1; $i > $startIndex; --$i) {
            // We explicitly check again if the function has a doc block to save some time.
            if (!$this->isFunctionTest($tokens, $i) || !$this->doesFunctionHaveDocBlock($tokens, $i)) {
                continue;
            }

            $docBlockIndex = $this->getDockBlockIndex($tokens, $i);

            $lines = $this->updateDocBlock($tokens, $docBlockIndex);

            $lines = implode($lines);
            $tokens[$docBlockIndex] = new Token([T_DOC_COMMENT, $lines]);

            $functionNameIndex = $tokens->getNextMeaningfulToken($i);
            $functionName = $tokens[$functionNameIndex]->getContent();

            //if the function already starts with test were done
            if ($this->startsWith('test', $functionName)) {
                continue;
            }

            $newFunctionName = $this->addTestToFunctionName($functionName);
            $tokens[$functionNameIndex] = new Token([T_STRING, $newFunctionName]);
        }
    }

    /**
     * @param Tokens $tokens
     * @param int$index
     *
     * @return bool
     */
    private function isFunctionTest(Tokens $tokens, $index)
    {
        // Check if we are dealing with a (non abstract, non lambda) function
        if (!$this->isFunction($tokens, $index)) {
            return false;
        }

        // if the function name starts with test its a test
        $functionNameIndex = $tokens->getNextMeaningfulToken($index);
        $functionName = $tokens[$functionNameIndex]->getContent();

        if ($this->startsWith('test', $functionName)) {
            return true;
        }
        // If the function doesn't have test in its name, and no doc block, its not a test
        if (!$this->doesFunctionHaveDocBlock($tokens, $index)) {
            return false;
        }

        $docBlockIndex = $this->getDockBlockIndex($tokens, $index);
        $doc = $tokens[$docBlockIndex]->getContent();
        if (false === strpos($doc, '@test')) {
            return false;
        }

        return true;
    }

    /**
     * @param Tokens $tokens
     * @param int    $index
     *
     * @return bool
     */
    private function isFunction(Tokens $tokens, $index)
    {
        $tokensAnalyzer = new TokensAnalyzer($tokens);

        return $tokens[$index]->isGivenKind(T_FUNCTION) && !$tokensAnalyzer->isLambda($index);
    }

    /**
     * @param string $needle
     * @param string $haystack
     *
     * @return bool
     */
    private function startsWith($needle, $haystack)
    {
        $len = strlen($needle);

        return substr($haystack, 0, $len) === $needle;
    }

    /**
     * @param Tokens $tokens
     * @param int    $index
     *
     * @return bool
     */
    private function doesFunctionHaveDocBlock(Tokens $tokens, $index)
    {
        $docBlockIndex = $this->getDockBlockIndex($tokens, $index);

        return $tokens[$docBlockIndex]->isGivenKind(T_DOC_COMMENT);
    }

    /**
     * @param Tokens $tokens
     * @param int    $index
     *
     * @return int
     */
    private function getDockBlockIndex(Tokens $tokens, $index)
    {
        do {
            $index = $tokens->getPrevNonWhitespace($index);
        } while ($tokens[$index]->isGivenKind([T_PUBLIC, T_PROTECTED, T_PRIVATE, T_FINAL, T_ABSTRACT, T_COMMENT]));

        return $index;
    }

    /**
     * @param string $functionName
     *
     * @return string
     */
    private function removeTestFromFunctionName($functionName)
    {
        if ($this->startsWith('test_', $functionName)) {
            if (is_numeric(substr($functionName, 6, 1))) {
                return $functionName;
            }

            return  substr($functionName, 5);
        }
        if (is_numeric(substr($functionName, 5, 1))) {
            return $functionName;
        }
        $functionName = substr($functionName, 4);

        return lcfirst($functionName);
    }

    /**
     * @param string $functionName
     *
     * @return string
     */
    private function addTestToFunctionName($functionName)
    {
        if ('camel' !== $this->configuration['case']) {
            return 'test_'.$functionName;
        }

        return'test'.ucfirst($functionName);
    }

    /**
     * @param Tokens $tokens
     * @param int    $index
     *
     * @return string
     */
    private function detectIndent(Tokens $tokens, $index)
    {
        if (!$tokens[$index - 1]->isWhitespace()) {
            return ''; // cannot detect indent
        }

        $explodedContent = explode($this->whitespacesConfig->getLineEnding(), $tokens[$index - 1]->getContent());

        return end($explodedContent);
    }

    /**
     * @param Tokens $tokens
     * @param int    $docBlockIndex
     */
    private function createDocBlock(Tokens $tokens, $docBlockIndex)
    {
        $lineEnd = $this->whitespacesConfig->getLineEnding();
        $originalIndent = $this->detectIndent($tokens, $tokens->getNextNonWhitespace($docBlockIndex));
        $toInsert = [
            new Token([T_DOC_COMMENT, '/**'.$lineEnd."${originalIndent} * @test".$lineEnd."${originalIndent} */"]),
            new Token([T_WHITESPACE, $lineEnd.$originalIndent]),
        ];
        $index = $tokens->getNextMeaningfulToken($docBlockIndex);
        $tokens->insertAt($index, $toInsert);
    }

    /**
     * @param Tokens $tokens
     * @param int    $docBlockIndex
     *
     * @return Line[]
     */
    private function updateDocBlock(Tokens $tokens, $docBlockIndex)
    {
        $doc = new DocBlock($tokens[$docBlockIndex]->getContent());
        $lines = $doc->getLines();

        return $this->updateLines($lines, $tokens, $docBlockIndex);
    }

    /**
     * @param Line[] $lines
     * @param Tokens $tokens
     * @param int    $docBlockIndex
     *
     * @return Line[]
     */
    private function updateLines($lines, Tokens $tokens, $docBlockIndex)
    {
        $needsAnnotation = 'annotation' === $this->configuration['style'];

        $doc = new DocBlock($tokens[$docBlockIndex]->getContent());
        for ($i = 0; $i < \count($lines); ++$i) {
            //If we need to add test annotation and it is a single line comment we need to deal with that separately
            if ($needsAnnotation && ($lines[$i]->isTheStart() && $lines[$i]->isTheEnd())) {
                if (!$this->doesDocBlockContainTest($doc)) {
                    $lines = $this->splitUpDocBlock($lines, $tokens, $docBlockIndex);

                    return $this->updateLines($lines, $tokens, $docBlockIndex);
                }
                //One we split it up, we run the function again, so we deal with other things in a proper way
            }

            if (!$needsAnnotation &&
                false !== \strpos($lines[$i]->getContent(), ' @test') &&
                false === \strpos($lines[$i]->getContent(), '@testWith') &&
                false === \strpos($lines[$i]->getContent(), '@testdox')
            ) {
                //We remove @test from the doc block
                $lines[$i] = new Line(str_replace(' @test', '', $lines[$i]->getContent()));
            }
            //ignore the line if it isnt @depends
            if (false === strpos($lines[$i], '@depends')) {
                continue;
            }

            $lines[$i] = $this->updateDepends($lines[$i]);
        }

        return $lines;
    }

    /**
     * Take a one line doc block, and turn it into a multi line doc block.
     *
     * @param Line[] $lines
     * @param Tokens $tokens
     * @param int    $docBlockIndex
     *
     * @return Line[]
     */
    private function splitUpDocBlock($lines, Tokens $tokens, $docBlockIndex)
    {
        $lineContent = $this->getSingleLineDocBlockEntry($lines);
        $lineEnd = $this->whitespacesConfig->getLineEnding();
        $originalIndent = $this->detectIndent($tokens, $tokens->getNextNonWhitespace($docBlockIndex));
        $lines = [
            new Line('/**'.$lineEnd),
            new Line($originalIndent.' * '.$lineContent.$lineEnd),
            new Line($originalIndent.' */'),
        ];

        return $lines;
    }

    /**
     * @param Line []$line
     *
     * @return string
     */
    private function getSingleLineDocBlockEntry($line)
    {
        $line = $line[0];
        $line = \str_replace('*/', '', $line);
        $line = trim($line);
        $line = \str_split($line);
        $i = \count($line);
        do {
            --$i;
        } while ('*' !== $line[$i] && '*' !== $line[$i - 1] && '/' !== $line[$i - 2]);
        if (' ' === $line[$i]) {
            ++$i;
        }
        $line = array_slice($line, $i);
        $line = implode($line);

        return $line;
    }

    /**
     * Updates the depends tag on the current doc block.
     *
     * @param Line $line
     *
     * @return Line
     */
    private function updateDepends(Line $line)
    {
        if ('annotation' === $this->configuration['style']) {
            return $this->removeTestFromDepends($line);
        }

        return $this->addtestToDepends($line);
    }

    /**
     * @param Line $line
     *
     * @return Line
     */
    private function removeTestFromDepends(Line $line)
    {
        $line = \str_split($line->getContent());

        $dependsIndex = $this->findWhereDependsFunctionNameStarts($line);
        $dependsFunctionName = implode(array_slice($line, $dependsIndex));

        if ($this->startsWith('test', $dependsFunctionName)) {
            $dependsFunctionName = $this->removeTestFromFunctionName($dependsFunctionName);
        }
        array_splice($line, $dependsIndex);

        return new Line(implode($line).$dependsFunctionName);
    }

    /**
     * @param Line $line
     *
     * @return Line
     */
    private function addTestToDepends(Line $line)
    {
        $line = \str_split($line->getContent());
        $dependsIndex = $this->findWhereDependsFunctionNameStarts($line);
        $dependsFunctionName = implode(array_slice($line, $dependsIndex));

        if (!$this->startsWith('test', $dependsFunctionName)) {
            $dependsFunctionName = $this->addTestToFunctionName($dependsFunctionName);
        }

        array_splice($line, $dependsIndex);

        return new Line(implode($line).$dependsFunctionName);
    }

    /**
     * Helps to find where the function name in the doc block starts.
     *
     * @param array $line
     *
     * @return int
     */
    private function findWhereDependsFunctionNameStarts(array $line)
    {
        $counter = \count($line);

        do {
            --$counter;
        } while (' ' !== $line[$counter]);

        return $counter + 1;
    }

    /**
     * @param Line[] $lines
     * @param Tokens $tokens
     * @param int    $docBlockIndex
     *
     * @return Line[]
     */
    private function addTestToDocBlockIfNeeded($lines, Tokens $tokens, $docBlockIndex)
    {
        $doc = new DocBlock($tokens[$docBlockIndex]->getContent());

        if (!$this->doesDocBlockContainTest($doc)) {
            $originalIndent = $this->detectIndent($tokens, $docBlockIndex);
            $lineEnd = $this->whitespacesConfig->getLineEnding();

            array_splice($lines, 1, 0, $originalIndent.' * @test'.$lineEnd);
        }

        return $lines;
    }

    /**
     * @param DocBlock $doc
     *
     * @return bool
     */
    private function doesDocBlockContainTest(DocBlock $doc)
    {
        //If it doesnt have @test at all its not a test
        if (false === strpos($doc->getContent(), '@test')) {
            return false;
        }
        // if it contains @test and then a new line, its a test
        if (false !== strpos($doc->getContent(), '@test'.$this->whitespacesConfig->getLineEnding())) {
            return true;
        }
        //If it contains @test and then a space its a new line
        if (false !== strpos($doc->getContent(), '@test ')) {
            return true;
        }
        //If it contains @test and then a tab its a new line
        if (false !== strpos($doc->getContent(), '@test\t')) {
            return true;
        }

        return false;
    }
}
