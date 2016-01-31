<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Test;

use PhpCsFixer\FixerFactory;
use PhpCsFixer\FixerInterface;
use PhpCsFixer\Linter\Linter;
use PhpCsFixer\RuleSet;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Utils;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
abstract class AbstractFixerTestCase extends \PHPUnit_Framework_TestCase
{
    private $fixer;

    /**
     * Create fixer factory with all needed fixers registered.
     *
     * @return FixerFactory
     */
    protected function createFixerFactory()
    {
        return FixerFactory::create()->registerBuiltInFixers();
    }

    /**
     * @return FixerInterface
     */
    protected function getFixer()
    {
        if (null !== $this->fixer) {
            return $this->fixer;
        }

        $name = $this->getFixerName();
        $configuration = $this->getFixerConfiguration();

        try {
            $fixers = $this->createFixerFactory()
                ->useRuleSet(new RuleSet(array($name => $configuration)))
                ->getFixers()
            ;
        } catch (\UnexpectedValueException $e) {
            throw new \UnexpectedValueException('Cannot determine fixer class, perhaps you forget to override `getFixerName` or `createFixerFactory` method?');
        }

        $this->fixer = $fixers[0];

        return $this->fixer;
    }

    /**
     * @return bool|array
     */
    protected function getFixerConfiguration()
    {
        return true;
    }

    /**
     * @return string
     */
    protected function getFixerName()
    {
        $reflection = new \ReflectionClass($this);

        $name = preg_replace('/FixerTest$/', '', $reflection->getShortName());

        return Utils::camelCaseToUnderscore($name);
    }

    /**
     * @return \SplFileInfo
     */
    protected function getTestFile($filename = __FILE__)
    {
        static $files = array();

        if (!isset($files[$filename])) {
            $files[$filename] = new \SplFileInfo($filename);
        }

        return $files[$filename];
    }

    /**
     * Tests if a fixer fixes a given string to match the expected result.
     *
     * It is used both if you want to test if something is fixed or if it is not touched by the fixer.
     * It also makes sure that the expected output does not change when run through the fixer. That means that you
     * do not need two test cases like [$expected] and [$expected, $input] (where $expected is the same in both cases)
     * as the latter covers both of them.
     * This method throws an exception if $expected and $input are equal to prevent test cases that accidentally do
     * not test anything.
     *
     * @param string              $expected The expected fixer output.
     * @param string|null         $input    The fixer input, or null if it should intentionally be equal to the output.
     * @param \SplFileInfo|null   $file     The file to fix, or null if unneeded.
     * @param FixerInterface|null $fixer    The fixer to be used, or null if it should be inferred from the test name.
     */
    protected function doTest($expected, $input = null, \SplFileInfo $file = null, FixerInterface $fixer = null)
    {
        if ($expected === $input) {
            throw new \InvalidArgumentException('Input parameter must not be equal to expected parameter.');
        }

        $linter = null;
        if (getenv('LINT_TEST_CASES')) {
            $linter = new Linter();
        }

        $fixer = $fixer ?: $this->getFixer();
        $file = $file ?: $this->getTestFile();
        $fileIsSupported = $fixer->supports($file);

        if (null !== $input) {
            if ($linter) {
                $linter->lintSource($input);
            }

            Tokens::clearCache();
            $tokens = Tokens::fromCode($input);

            if ($fileIsSupported) {
                $this->assertTrue($fixer->isCandidate($tokens), 'Fixer must be a candidate for input code.');
                $fixResult = $fixer->fix($file, $tokens);
                $this->assertNull($fixResult, '->fix method must return null.');
            }

            $this->assertTrue($tokens->isChanged(), 'Tokens collection built on input code must be marked as changed after fixing.');
            $this->assertSame($expected, $tokens->generateCode(), 'Code build on input code must match expected code.');

            Tokens::clearCache();
            $expectedTokens = Tokens::fromCode($expected);
            $tokens->clearEmptyTokens();
            $this->assertTokens($expectedTokens, $tokens);
        }

        if ($linter) {
            $linter->lintSource($input);
        }

        Tokens::clearCache();
        $tokens = Tokens::fromCode($expected);

        if ($fileIsSupported) {
            $fixResult = $fixer->fix($file, $tokens);
            $this->assertNull($fixResult, '->fix method must return null.');
        }

        $this->assertFalse($tokens->isChanged(), 'Tokens collection built on expected code must not be marked as changed after fixing.');
        $this->assertSame($expected, $tokens->generateCode(), 'Code build on expected code must not change.');
    }

    private function assertTokens(Tokens $expectedTokens, Tokens $inputTokens)
    {
        foreach ($expectedTokens as $index => $expectedToken) {
            $inputToken = $inputTokens[$index];

            $this->assertTrue(
                $expectedToken->equals($inputToken),
                sprintf('The token at index %d must be %s, got %s', $index, $expectedToken->toJson(), $inputToken->toJson())
            );
        }

        $this->assertSame($expectedTokens->count(), $inputTokens->count(), 'The collection must have the same length than the expected one.');

        $foundTokenKinds = array_keys(AccessibleObject::create($expectedTokens)->foundTokenKinds);

        foreach ($foundTokenKinds as $tokenKind) {
            $this->assertTrue(
                $inputTokens->isTokenKindFound($tokenKind),
                sprintf('The token kind %s must be found in fixed tokens collection.', $tokenKind)
            );
        }
    }
}
