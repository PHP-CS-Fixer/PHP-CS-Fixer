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

namespace PhpCsFixer\Tests;

use PhpCsFixer\WhitespacesFixerConfig;
use PHPUnit\Framework\TestCase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\WhitespacesFixerConfig
 */
final class WhitespacesFixerConfigTest extends TestCase
{
    /**
     * @param string      $indent
     * @param string      $lineEnding
     * @param null|string $exceptionRegExp
     *
     * @dataProvider provideTestCases
     */
    public function testCases($indent, $lineEnding, $exceptionRegExp = null)
    {
        if (null !== $exceptionRegExp) {
            $this->setExpectedExceptionRegExp(
                'InvalidArgumentException',
                '%^'.preg_quote($exceptionRegExp, '%').'$%'
            );
        }

        $config = new WhitespacesFixerConfig($indent, $lineEnding);

        $this->assertSame($indent, $config->getIndent());
        $this->assertSame($lineEnding, $config->getLineEnding());
    }

    public function provideTestCases()
    {
        return array(
            array('    ', "\n"),
            array("\t", "\n"),
            array('    ', "\r\n"),
            array("\t", "\r\n"),
            array('    ', 'asd', 'Invalid "lineEnding" param, expected "\n" or "\r\n".'),
            array('    ', array(), 'Invalid "lineEnding" param, expected "\n" or "\r\n".'),
            array('std', "\n", 'Invalid "indent" param, expected tab or two or four spaces.'),
            array(array(), "\n", 'Invalid "indent" param, expected tab or two or four spaces.'),
        );
    }
}
