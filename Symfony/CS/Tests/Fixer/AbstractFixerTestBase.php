<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer;

use Symfony\CS\FixerInterface;
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

    protected function makeTest($expected, $input = null, \SplFileInfo $file = null, FixerInterface $fixer = null)
    {
        if ($expected === $input) {
            throw new \InvalidArgumentException('Input parameter must not be equal to expected parameter.');
        }

        $fixer = $fixer ?: $this->getFixer();
        $file = $file ?: $this->getTestFile();
        $fileIsSupported = $fixer->supports($file);

        if (null !== $input) {
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

        $tokensReflection = new \ReflectionClass($expectedTokens);
        $propertyReflection = $tokensReflection->getProperty('foundTokenKinds');
        $propertyReflection->setAccessible(true);
        $foundTokenKinds = array_keys($propertyReflection->getValue($expectedTokens));

        foreach ($foundTokenKinds as $tokenKind) {
            $this->assertTrue(
                $inputTokens->isTokenKindFound($tokenKind),
                sprintf('The token kind %s must be found in fixed tokens collection.', $tokenKind)
            );
        }
    }
}
