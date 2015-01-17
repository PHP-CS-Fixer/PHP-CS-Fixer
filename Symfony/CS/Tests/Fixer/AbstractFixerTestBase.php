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

    protected function makeTest($expected, $input = null, \SplFileInfo $file = null, FixerInterface $fixer = null)
    {
        if ($expected === $input) {
            throw new \InvalidArgumentException('Input parameter must not be equal to expected parameter.');
        }

        $fixer = $fixer ?: $this->getFixer();
        $file = $file ?: $this->getTestFile();

        if (null !== $input) {
            Tokens::clearCache();
            $tokens = Tokens::fromCode($input);

            $fixResult = $fixer->fix($file, $tokens);

            $this->assertNull($fixResult, '->fix method should return null.');
            $this->assertSame($expected, $tokens->generateCode());
            $this->assertTrue($tokens->isChanged(), 'Tokens collection built on input code should be marked as changed after fixing.');
        }

        Tokens::clearCache();
        $tokens = Tokens::fromCode($expected);

        $fixResult = $fixer->fix($file, $tokens);

        $this->assertNull($fixResult, '->fix method should return null.');
        $this->assertSame($expected, $tokens->generateCode());
        $this->assertFalse($tokens->isChanged(), 'Tokens collection built on expected code should not be marked as changed after fixing.');
    }
}
