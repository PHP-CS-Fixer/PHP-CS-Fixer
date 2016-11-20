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

namespace PhpCsFixer\Test;

use PhpCsFixer\FixerFactory;
use PhpCsFixer\FixerInterface;
use PhpCsFixer\Linter\Linter;
use PhpCsFixer\Linter\LinterInterface;
use PhpCsFixer\RuleSet;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Utils;
use Prophecy\Argument;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
abstract class AbstractFixerTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LinterInterface
     */
    protected $linter;

    /**
     * @var null|FixerInterface
     */
    protected $fixer;

    /**
     * @var null|string
     */
    private $fixerClassName;

    protected function setUp()
    {
        $this->linter = $this->getLinter();
        $this->fixer = $this->createFixer();
    }

    /**
     * @return FixerInterface
     */
    protected function createFixer()
    {
        $fixerClassName = $this->getFixerClassName();
        $fixer = new $fixerClassName();

        return $fixer;
    }

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
     * @return string
     */
    protected function getFixerName()
    {
        $reflection = new \ReflectionClass($this);

        $name = preg_replace('/FixerTest$/', '', $reflection->getShortName());

        return Utils::camelCaseToUnderscore($name);
    }

    /**
     * @param string $filename
     *
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
     * @param string            $expected The expected fixer output
     * @param string|null       $input    The fixer input, or null if it should intentionally be equal to the output
     * @param \SplFileInfo|null $file     The file to fix, or null if unneeded
     */
    protected function doTest($expected, $input = null, \SplFileInfo $file = null)
    {
        if ($expected === $input) {
            throw new \InvalidArgumentException('Input parameter must not be equal to expected parameter.');
        }

        $file = $file ?: $this->getTestFile();
        $fileIsSupported = $this->fixer->supports($file);

        if (null !== $input) {
            $this->assertNull($this->lintSource($input));

            Tokens::clearCache();
            $tokens = Tokens::fromCode($input);

            if ($fileIsSupported) {
                $this->assertTrue($this->fixer->isCandidate($tokens), 'Fixer must be a candidate for input code.');
                $fixResult = $this->fixer->fix($file, $tokens);
                $this->assertNull($fixResult, '->fix method must return null.');
            }

            $this->assertSame($expected, $tokens->generateCode(), 'Code build on input code must match expected code.');
            $this->assertTrue($tokens->isChanged(), 'Tokens collection built on input code must be marked as changed after fixing.');

            $tokens->clearEmptyTokens();

            $this->assertSame(
                count($tokens),
                count(array_unique(array_map(function (Token $token) {
                    return spl_object_hash($token);
                }, $tokens->toArray()))),
                'Token items inside Tokens collection must be unique.'
            );

            Tokens::clearCache();
            $expectedTokens = Tokens::fromCode($expected);
            $this->assertTokens($expectedTokens, $tokens);
        }

        $this->assertNull($this->lintSource($expected));

        Tokens::clearCache();
        $tokens = Tokens::fromCode($expected);

        $isCandidate = $this->fixer->isCandidate($tokens);
        $this->assertFalse($tokens->isChanged(), 'Fixer should not touch Tokens on candidate check.');

        if (!$isCandidate) {
            return;
        }

        if ($fileIsSupported) {
            $fixResult = $this->fixer->fix($file, $tokens);
            $this->assertNull($fixResult, '->fix method must return null.');
        }

        $this->assertSame($expected, $tokens->generateCode(), 'Code build on expected code must not change.');
        $this->assertFalse($tokens->isChanged(), 'Tokens collection built on expected code must not be marked as changed after fixing.');
    }

    /**
     * @param string $source
     *
     * @return string|null
     */
    protected function lintSource($source)
    {
        try {
            $this->linter->lintSource($source)->check();
        } catch (\Exception $e) {
            return $e->getMessage()."\n\nSource:\n$source";
        }
    }

    private function assertTokens(Tokens $expectedTokens, Tokens $inputTokens)
    {
        foreach ($expectedTokens as $index => $expectedToken) {
            $inputToken = $inputTokens[$index];
            $option = array('JSON_PRETTY_PRINT');
            $this->assertTrue(
                $expectedToken->equals($inputToken),
                sprintf("The token at index %d must be:\n%s,\ngot:\n%s.", $index, $expectedToken->toJson($option), $inputToken->toJson($option))
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

    /**
     * @return LinterInterface
     */
    private function getLinter()
    {
        static $linter = null;

        if (null === $linter) {
            if (getenv('SKIP_LINT_TEST_CASES')) {
                $linterProphecy = $this->prophesize('PhpCsFixer\Linter\LinterInterface');
                $linterProphecy
                    ->lintSource(Argument::type('string'))
                    ->willReturn($this->prophesize('PhpCsFixer\Linter\LintingResultInterface')->reveal());

                $linter = $linterProphecy->reveal();
            } else {
                $linter = new Linter();
            }
        }

        return $linter;
    }

    /**
     * @return string
     */
    private function getFixerClassName()
    {
        if (null !== $this->fixerClassName) {
            return $this->fixerClassName;
        }

        try {
            $fixers = $this->createFixerFactory()
                ->useRuleSet(new RuleSet(array($this->getFixerName() => true)))
                ->getFixers()
            ;
        } catch (\UnexpectedValueException $e) {
            throw new \UnexpectedValueException('Cannot determine fixer class, perhaps you forget to override `getFixerName` or `createFixerFactory` method?');
        }

        if (1 !== count($fixers)) {
            throw new \UnexpectedValueException(sprintf('Determine fixer class should result in one fixer, got "%d". Perhaps you configured the fixer to "false" ?', count($fixers)));
        }

        $this->fixerClassName = get_class($fixers[0]);

        return $this->fixerClassName;
    }
}
