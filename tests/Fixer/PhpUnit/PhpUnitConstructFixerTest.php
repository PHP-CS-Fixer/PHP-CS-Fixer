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

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\PhpUnit\PhpUnitConstructFixer
 */
final class PhpUnitConstructFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @group legacy
     * @dataProvider provideTestFixCases
     */
    public function testLegacyFix($expected, $input = null)
    {
        $this->expectDeprecation('Passing "assertions" at the root of the configuration for rule "php_unit_construct" is deprecated and will not be supported in 3.0, use "assertions" => array(...) option instead.');
        $this->fixer->configure([
            'assertEquals',
            'assertSame',
            'assertNotEquals',
            'assertNotSame',
        ]);
        $this->doTest($expected, $input);

        foreach (['assertSame', 'assertEquals', 'assertNotEquals', 'assertNotSame'] as $method) {
            $this->fixer->configure([$method]);
            $this->doTest(
                $expected,
                $input && false !== strpos($input, $method) ? $input : null
            );
        }
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideTestFixCases
     */
    public function testFix($expected, $input = null)
    {
        $this->fixer->configure(['assertions' => [
            'assertEquals',
            'assertSame',
            'assertNotEquals',
            'assertNotSame',
        ]]);
        $this->doTest($expected, $input);

        foreach (['assertSame', 'assertEquals', 'assertNotEquals', 'assertNotSame'] as $method) {
            $this->fixer->configure(['assertions' => [$method]]);
            $this->doTest(
                $expected,
                $input && false !== strpos($input, $method) ? $input : null
            );
        }
    }

    public function provideTestFixCases()
    {
        $cases = [
            ['$sth->assertSame(true, $foo);'],
            ['$this->assertSame($b, null);'],
            [
                '$this->assertNull(/*bar*/ $a);',
                '$this->assertSame(null /*foo*/, /*bar*/ $a);',
            ],
            [
                '$this->assertSame(null === $eventException ? $exception : $eventException, $event->getException());',
            ],
            [
                '$this->assertSame(null /*comment*/ === $eventException ? $exception : $eventException, $event->getException());',
            ],
            [
                '
    $this->assertTrue(
        $a,
        "foo" . $bar
    );',
                '
    $this->assertSame(
        true,
        $a,
        "foo" . $bar
    );',
            ],
            [
                '
    $this->assertTrue(#
        #
        $a,#
        "foo" . $bar#
    );',
                '
    $this->assertSame(#
        true,#
        $a,#
        "foo" . $bar#
    );',
            ],
            [
                '$this->assertSame("a", $a); $this->assertTrue($b);',
                '$this->assertSame("a", $a); $this->assertSame(true, $b);',
            ],
            [
                '$this->assertSame(true || $a, $b); $this->assertTrue($c);',
                '$this->assertSame(true || $a, $b); $this->assertSame(true, $c);',
            ],
            [
                '$this->assertFalse($foo);',
                '$this->assertEquals(FALSE, $foo);',
            ],
            [
                '$this->assertTrue($foo);',
                '$this->assertEquals(TruE, $foo);',
            ],
            [
                '$this->assertNull($foo);',
                '$this->assertEquals(NULL, $foo);',
            ],
        ];

        array_walk(
            $cases,
            static function (&$case) {
                $case[0] = static::generateTest($case[0]);

                if (isset($case[1])) {
                    $case[1] = static::generateTest($case[1]);
                }
            }
        );

        return array_merge(
            $cases,
            [
                'not in a class' => ['<?php $this->assertEquals(NULL, $foo);'],
                'not phpunit class' => ['<?php class Foo { public function testFoo(){ $this->assertEquals(NULL, $foo); }}'],
                'multiple candidates in multiple classes ' => [
                    '<?php
                        class FooTest1 extends PHPUnit_Framework_TestCase { public function testFoo(){ $this->assertNull($foo); }}
                        class FooTest2 extends PHPUnit_Framework_TestCase { public function testFoo(){ $this->assertNull($foo); }}
                        class FooTest3 extends PHPUnit_Framework_TestCase { public function testFoo(){ $this->assertNull($foo); }}
                    ',
                    '<?php
                        class FooTest1 extends PHPUnit_Framework_TestCase { public function testFoo(){ $this->assertEquals(NULL, $foo); }}
                        class FooTest2 extends PHPUnit_Framework_TestCase { public function testFoo(){ $this->assertEquals(NULL, $foo); }}
                        class FooTest3 extends PHPUnit_Framework_TestCase { public function testFoo(){ $this->assertEquals(NULL, $foo); }}
                    ',
                ],
            ],
            $this->generateCases('$this->assert%s%s($a); //%s %s', '$this->assert%s(%s, $a); //%s %s'),
            $this->generateCases('$this->assert%s%s($a, "%s", "%s");', '$this->assert%s(%s, $a, "%s", "%s");'),
            $this->generateCases('static::assert%s%s($a); //%s %s', 'static::assert%s(%s, $a); //%s %s'),
            $this->generateCases('STATIC::assert%s%s($a); //%s %s', 'STATIC::assert%s(%s, $a); //%s %s'),
            $this->generateCases('self::assert%s%s($a); //%s %s', 'self::assert%s(%s, $a); //%s %s')
        );
    }

    public function testInvalidConfig()
    {
        $this->expectException(\PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageMatches('/^\[php_unit_construct\] Invalid configuration: The option "assertions" .*\.$/');

        $this->fixer->configure(['assertions' => ['__TEST__']]);
    }

    /**
     * @param string $expected
     * @param string $input
     *
     * @requires PHP 7.3
     * @dataProvider provideFix73Cases
     */
    public function testFix73($expected, $input)
    {
        $this->doTest($expected, $input);
    }

    public function provideFix73Cases()
    {
        return [
            [
                static::generateTest('$this->assertTrue($a, );'),
                static::generateTest('$this->assertSame(true, $a, );'),
            ],
            [
                static::generateTest('$this->assertTrue($a, $message , );'),
                static::generateTest('$this->assertSame(true, $a, $message , );'),
            ],
        ];
    }

    public function testEmptyAssertions()
    {
        $this->fixer->configure(['assertions' => []]);
        $this->doTest(self::generateTest('$this->assertSame(null, $a);'));
    }

    private function generateCases($expectedTemplate, $inputTemplate)
    {
        $cases = [];
        $functionTypes = ['Same' => true, 'NotSame' => false, 'Equals' => true, 'NotEquals' => false];
        foreach (['true', 'false', 'null'] as $type) {
            foreach ($functionTypes as $method => $positive) {
                $cases[] = [
                    self::generateTest(sprintf($expectedTemplate, $positive ? '' : 'Not', ucfirst($type), $method, $type)),
                    self::generateTest(sprintf($inputTemplate, $method, $type, $method, $type)),
                ];
            }
        }

        return $cases;
    }

    /**
     * @param string $content
     *
     * @return string
     */
    private static function generateTest($content)
    {
        return "<?php final class FooTest extends \\PHPUnit_Framework_TestCase {\n    public function testSomething() {\n        ".$content."\n    }\n}\n";
    }
}
