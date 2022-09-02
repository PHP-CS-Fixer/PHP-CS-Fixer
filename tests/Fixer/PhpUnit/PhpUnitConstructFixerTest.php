<?php

declare(strict_types=1);

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

use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
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
     * @dataProvider provideTestFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
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
                null !== $input && str_contains($input, $method) ? $input : null
            );
        }
    }

    public function provideTestFixCases(): array
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
            static function (&$case): void {
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

    public function testInvalidConfig(): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageMatches('/^\[php_unit_construct\] Invalid configuration: The option "assertions" .*\.$/');

        $this->fixer->configure(['assertions' => ['__TEST__']]);
    }

    /**
     * @dataProvider provideFix73Cases
     */
    public function testFix73(string $expected, string $input): void
    {
        $this->doTest($expected, $input);
    }

    public function provideFix73Cases(): array
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

    public function testEmptyAssertions(): void
    {
        $this->fixer->configure(['assertions' => []]);
        $this->doTest(self::generateTest('$this->assertSame(null, $a);'));
    }

    /**
     * @dataProvider provideFix81Cases
     *
     * @requires PHP 8.1
     */
    public function testFix81(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function provideFix81Cases(): iterable
    {
        yield [
            self::generateTest('$this->assertEquals(...);'),
        ];
    }

    /**
     * @return list<array{string, string}>
     */
    private function generateCases(string $expectedTemplate, string $inputTemplate): array
    {
        $functionTypes = ['Same' => true, 'NotSame' => false, 'Equals' => true, 'NotEquals' => false];
        $cases = [];

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

    private static function generateTest(string $content): string
    {
        return "<?php final class FooTest extends \\PHPUnit_Framework_TestCase {\n    public function testSomething() {\n        ".$content."\n    }\n}\n";
    }
}
