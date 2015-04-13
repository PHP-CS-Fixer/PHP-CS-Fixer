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

use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
abstract class AbstractFixerTestBase extends \PHPUnit_Framework_TestCase
{
    protected function getFixer()
    {
        $fixerName = 'Symfony\CS\Fixer'.substr(get_called_class(), strlen(__NAMESPACE__), -strlen('Test'));

        return new $fixerName();
    }

    protected function getTestFile($filename = __FILE__)
    {
        static $files = array();

        if (!isset($files[$filename])) {
            $files[$filename] = new \SplFileInfo($filename);
        }

        return $files[$filename];
    }

    protected function makeTest($expected, $input = null, \SplFileInfo $file = null)
    {
        if ($expected === $input) {
            throw new \InvalidArgumentException('Input parameter must not be equal to expected parameter.');
        }

        $fixer = $this->getFixer();
        $file = $file ?: $this->getTestFile();
        $fileIsSupported = $fixer->supports($file);

        if (null !== $input) {
            $fixedCode = $fileIsSupported ? $fixer->fix($file, $input) : $input;

            $this->assertSame($expected, $fixedCode);

            $tokens = Tokens::fromCode($fixedCode); // Load cached collection (used by the fixer)
            Tokens::clearCache();
            $expectedTokens = Tokens::fromCode($fixedCode); // Load the expected collection based on PHP parsing
            $this->assertTokens($expectedTokens, $tokens);
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

        $this->assertEquals($expectedTokens->count(), $tokens->count(), 'The collection should have the same length than the expected one');
    }
}
