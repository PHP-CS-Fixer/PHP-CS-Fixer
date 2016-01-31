<?php

/*
 * This file is part of the PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer;

use Symfony\CS\FixerInterface;
use Symfony\CS\LintManager;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
abstract class AbstractFixerTestBase extends \PHPUnit_Framework_TestCase
{
    protected function getFixer()
    {
        $name = 'Symfony\CS\Fixer'.substr(get_called_class(), strlen(__NAMESPACE__), -strlen('Test'));

        return new $name();
    }

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
    protected function makeTest($expected, $input = null, \SplFileInfo $file = null, FixerInterface $fixer = null)
    {
        if ($expected === $input) {
            throw new \InvalidArgumentException('Input parameter must not be equal to expected parameter.');
        }

        $linter = null;
        if (getenv('LINT_TEST_CASES')) {
            $linter = new LintManager();
        }

        $fixer = $fixer ?: $this->getFixer();
        $file = $file ?: $this->getTestFile();
        $fileIsSupported = $fixer->supports($file);

        if (null !== $input) {
            if ($linter) {
                $lintProcess = $linter->createProcessForSource($input);
                $this->assertTrue($lintProcess->isSuccessful(), $lintProcess->getOutput());
            }

            $fixedCode = $fileIsSupported ? $fixer->fix($file, $input) : $input;

            $this->assertSame($expected, $fixedCode);

            $tokens = Tokens::fromCode($fixedCode); // Load cached collection (used by the fixer)
            Tokens::clearCache();
            $expectedTokens = Tokens::fromCode($fixedCode); // Load the expected collection based on PHP parsing
            $this->assertTokens($expectedTokens, $tokens);
        }

        if ($linter) {
            $lintProcess = $linter->createProcessForSource($expected);
            $this->assertTrue($lintProcess->isSuccessful(), $lintProcess->getOutput());
        }

        $this->assertSame($expected, $fileIsSupported ? $fixer->fix($file, $expected) : $expected);
    }

    private function assertTokens(Tokens $expectedTokens, Tokens $tokens)
    {
        foreach ($expectedTokens as $index => $expectedToken) {
            $token = $tokens[$index];

            $expectedPrototype = $expectedToken->getPrototype();
            if (is_array($expectedPrototype)) {
                unset($expectedPrototype[2]); // don't compare token lines as our token mutations don't deal with line numbers
            }

            $this->assertTrue($token->equals($expectedPrototype), sprintf('The token at index %d should be %s, got %s', $index, json_encode($expectedPrototype), $token->toJson()));
        }

        $this->assertSame($expectedTokens->count(), $tokens->count(), 'The collection should have the same length than the expected one');
    }
}
