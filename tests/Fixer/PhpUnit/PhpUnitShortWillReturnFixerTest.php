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

namespace PhpCsFixer\Tests\Fixer\PhpUnit;

use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Michał Adamski <michal.adamski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\PhpUnit\PhpUnitShortWillReturnFixer
 */
final class PhpUnitShortWillReturnFixerTest extends AbstractFixerTestCase
{
    /**
     * @var ConfigurableFixerInterface
     */
    protected $fixer;

    /**
     * @param string        $expected
     * @param string | null $input
     * @param array         $config
     *
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input = null, $config = [])
    {
        $this->fixer->configure($config);
        $this->doTest($expected, $input);
    }

    /**
     * @return array
     */
    public function provideFixCases()
    {
        return [
            'will return with integer' => [
                '<?php $someMock->method(\'someMethod\')->willReturn(10);',
                '<?php $someMock->method(\'someMethod\')->will($this->returnValue(10));',
            ],
            'will return turned off' => [
                '<?php $someMock->method(\'someMethod\')->will($this->returnValue(10));',
                null,
                ['returnValue' => false],
            ],
            'will return with negative integer' => [
                '<?php $someMock->method(\'someMethod\')->willReturn(-10);',
                '<?php $someMock->method(\'someMethod\')->will($this->returnValue(-10));',
            ],
            'will return with float' => [
                '<?php $someMock->method(\'someMethod\')->willReturn(10.10);',
                '<?php $someMock->method(\'someMethod\')->will($this->returnValue(10.10));',
            ],
            'will return with negative float' => [
                '<?php $someMock->method(\'someMethod\')->willReturn(-10.10);',
                '<?php $someMock->method(\'someMethod\')->will($this->returnValue(-10.10));',
            ],
            'will return with string' => [
                '<?php $someMock->method(\'someMethod\')->willReturn(\'myValue\');',
                '<?php $someMock->method(\'someMethod\')->will($this->returnValue(\'myValue\'));',
            ],
            'will return with variable' => [
                '<?php $someMock->method(\'someMethod\')->willReturn($myValue);',
                '<?php $someMock->method(\'someMethod\')->will($this->returnValue($myValue));',
            ],
            'will return with const' => [
                '<?php $testMock->method("test_method")->willReturn(DEFAULT_VALUE);',
                '<?php $testMock->method("test_method")->will($this->returnValue(DEFAULT_VALUE));',
            ],
            'will return with class const' => [
                '<?php $testMock->method("test_method")->willReturn(self::DEFAULT_VALUE);',
                '<?php $testMock->method("test_method")->will($this->returnValue(self::DEFAULT_VALUE));',
            ],
            'will return with array short' => [
                '<?php $someMock->method(\'someMethod\')->willReturn([]);',
                '<?php $someMock->method(\'someMethod\')->will($this->returnValue([]));',
            ],
            'will return with nested array short' => [
                '<?php $someMock->method(\'someMethod\')->willReturn([[]]);',
                '<?php $someMock->method(\'someMethod\')->will($this->returnValue([[]]));',
            ],
            'will return with array traditional' => [
                '<?php $someMock->method(\'someMethod\')->willReturn(array());',
                '<?php $someMock->method(\'someMethod\')->will($this->returnValue(array()));',
            ],
            'will return with object' => [
                '<?php $someMock->method(\'someMethod\')->willReturn(new stdClass());',
                '<?php $someMock->method(\'someMethod\')->will($this->returnValue(new stdClass()));',
            ],
            'will return with datetime' => [
                '<?php $someMock->method(\'someMethod\')->willReturn(new \DateTime());',
                '<?php $someMock->method(\'someMethod\')->will($this->returnValue(new \DateTime()));',
            ],
            'will return self' => [
                '<?php $someMock->method(\'someMethod\')->willReturnSelf();',
                '<?php $someMock->method(\'someMethod\')->will($this->returnSelf());',
            ],
            'will return self turned off' => [
                '<?php $someMock->method(\'someMethod\')->will($this->returnSelf());',
                null,
                ['returnSelf' => false],
            ],
            'will return argument' => [
                '<?php $someMock->method(\'someMethod\')->willReturnArgument(2);',
                '<?php $someMock->method(\'someMethod\')->will($this->returnArgument(2));',
            ],
            'will return argument turned off' => [
                '<?php $someMock->method(\'someMethod\')->will($this->returnArgument(2));',
                null,
                ['returnArgument' => false],
            ],
            'will return callback without params' => [
                '<?php $someMock->method(\'someMethod\')->willReturnCallback(\'str_rot13\');',
                '<?php $someMock->method(\'someMethod\')->will($this->returnCallback(\'str_rot13\'));',
            ],
            'will return callback turned off' => [
                '<?php $someMock->method(\'someMethod\')->will($this->returnCallback(\'str_rot13\'));',
                null,
                ['returnCallback' => false],
            ],
            'will return value map' => [
                '<?php $someMock->method(\'someMethod\')->willReturnMap([\'a\', \'b\', \'c\', \'d\']);',
                '<?php $someMock->method(\'someMethod\')->will($this->returnValueMap([\'a\', \'b\', \'c\', \'d\']));',
            ],
            'will return value map turned off' => [
                '<?php $someMock->method(\'someMethod\')->will($this->returnValueMap([\'a\', \'b\', \'c\', \'d\']));',
                null,
                ['returnValueMap' => false],
            ],
            'will return multiple occurrences with default configuration fixed all by default' => [
                '<?php
                $someMock->method(\'someMethod\')->willReturn(10);
                $someMock->method(\'someMethod\')->willReturnSelf();
                $someMock->method(\'someMethod\')->willReturnArgument(2);
                $someMock->method(\'someMethod\')->willReturnCallback(\'str_rot13\');
                $someMock->method(\'someMethod\')->willReturnMap([\'a\', \'b\', \'c\', \'d\']);
                ',
                '<?php
                $someMock->method(\'someMethod\')->will($this->returnValue(10));
                $someMock->method(\'someMethod\')->will($this->returnSelf());
                $someMock->method(\'someMethod\')->will($this->returnArgument(2));
                $someMock->method(\'someMethod\')->will($this->returnCallback(\'str_rot13\'));
                $someMock->method(\'someMethod\')->will($this->returnValueMap([\'a\', \'b\', \'c\', \'d\']));
                ',
                [],
            ],
            'will return multiple occurrences with mixed configuration' => [
                '<?php
                $someMock->method(\'someMethod\')->will($this->returnValue(10));
                $someMock->method(\'someMethod\')->willReturnSelf();
                $someMock->method(\'someMethod\')->will($this->returnArgument(2));
                $someMock->method(\'someMethod\')->willReturnCallback(\'str_rot13\');
                $someMock->method(\'someMethod\')->will($this->returnValueMap([\'a\', \'b\', \'c\', \'d\']));
                ',
                '<?php
                $someMock->method(\'someMethod\')->will($this->returnValue(10));
                $someMock->method(\'someMethod\')->will($this->returnSelf());
                $someMock->method(\'someMethod\')->will($this->returnArgument(2));
                $someMock->method(\'someMethod\')->will($this->returnCallback(\'str_rot13\'));
                $someMock->method(\'someMethod\')->will($this->returnValueMap([\'a\', \'b\', \'c\', \'d\']));
                ',
                [
                    'returnValue' => false,
                    'returnSelf' => true,
                    'returnArgument' => false,
                    'returnCallback' => true,
                    'returnValueMap' => false,
                ],
            ],
        ];
    }
}
