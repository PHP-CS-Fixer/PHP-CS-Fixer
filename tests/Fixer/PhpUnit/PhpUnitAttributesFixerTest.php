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

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\PhpUnit\PhpUnitAttributesFixer
 */
final class PhpUnitAttributesFixerTest extends AbstractFixerTestCase
{
    /**
     * @requires PHP 8.0
     *
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<array{0: string, 1?: string}>
     */
    public static function provideFixCases(): iterable
    {
        yield 'do not fix with wrong values' => [<<<'PHP'
            <?php
            /**
             * @requires
             * @uses
             */
            class FooTest extends \PHPUnit\Framework\TestCase {
                /**
                 * @backupGlobals
                 * @backupStaticAttributes
                 * @covers
                 * @dataProvider
                 * @depends
                 * @group
                 * @preserveGlobalState
                 * @testDox
                 * @testWith
                 * @ticket
                 */
                public function testFoo() { self::assertTrue(true); }
            }
            PHP];

        yield 'do not fix with wrong casing' => [<<<'PHP'
            <?php
            /**
             * @COVERS \Foo
             */
            class FooTest extends \PHPUnit\Framework\TestCase {
                /**
                 * @dataPROVIDER provideFooCases
                 * @requires php 8.3
                 */
                public function testFoo() { self::assertTrue(true); }
            }
            PHP];

        yield 'do not fix when not supported by attributes' => [<<<'PHP'
            <?php
            /**
             * @covers FooClass::FooMethod
             * @uses ClassName::methodName
             */
            class FooTest extends \PHPUnit\Framework\TestCase {
                public function testFoo() { self::assertTrue(true); }
            }
            PHP];

        yield 'fix multiple annotations' => [
            <<<'PHP'
                <?php
                class FooTest extends \PHPUnit\Framework\TestCase {
                    /**
                     * @copyright ACME Corporation
                     */
                    #[\PHPUnit\Framework\Attributes\DataProvider('provideFooCases')]
                    #[\PHPUnit\Framework\Attributes\RequiresPhp('^8.2')]
                    #[\PHPUnit\Framework\Attributes\RequiresOperatingSystem('Linux|Darwin')]
                    public function testFoo($x) { self::assertTrue($x); }
                    public static function provideFooCases() { yield [true]; yield [false]; }
                }
                PHP,
            <<<'PHP'
                <?php
                class FooTest extends \PHPUnit\Framework\TestCase {
                    /**
                     * @copyright ACME Corporation
                     * @dataProvider provideFooCases
                     * @requires PHP ^8.2
                     * @requires OS Linux|Darwin
                     */
                    public function testFoo($x) { self::assertTrue($x); }
                    public static function provideFooCases() { yield [true]; yield [false]; }
                }
                PHP,
        ];

        yield 'fix with multiple spaces' => [
            <<<'PHP'
                <?php
                class FooTest extends \PHPUnit\Framework\TestCase {
                    /**
                     */
                    #[\PHPUnit\Framework\Attributes\RequiresPhp('^7.4|^8.1')]
                    public function testFoo() { self::assertTrue(true); }
                }
                PHP,
            <<<'PHP'
                <?php
                class FooTest extends \PHPUnit\Framework\TestCase {
                    /**
                     * @requires    PHP        ^7.4|^8.1
                     */
                    public function testFoo() { self::assertTrue(true); }
                }
                PHP,
        ];

        yield 'fix with trailing spaces' => self::createCase(
            ['class'],
            '#[CoversClass(Foo::class)]',
            '@covers Foo    ',
        );

        $byte224 = \chr(224);

        yield 'fix with non-alphanumeric characters' => [
            <<<PHP
                <?php
                class FooTest extends \\PHPUnit\\Framework\\TestCase {
                    /**
                     */
                    #[\\PHPUnit\\Framework\\Attributes\\TestDox('a\\'b"c')]
                    public function testFoo() { self::assertTrue(true); }
                    /**
                     */
                    #[\\PHPUnit\\Framework\\Attributes\\TestDox('龍')]
                    public function testBar() { self::assertTrue(true); }
                    /**
                     */
                    #[\\PHPUnit\\Framework\\Attributes\\TestDox('byte224: {$byte224}')]
                    public function testBaz() { self::assertTrue(true); }
                }
                PHP,
            <<<PHP
                <?php
                class FooTest extends \\PHPUnit\\Framework\\TestCase {
                    /**
                     * @testDox a'b"c
                     */
                    public function testFoo() { self::assertTrue(true); }
                    /**
                     * @testDox 龍
                     */
                    public function testBar() { self::assertTrue(true); }
                    /**
                     * @testDox byte224: {$byte224}
                     */
                    public function testBaz() { self::assertTrue(true); }
                }
                PHP,
        ];

        yield 'handle After' => self::createCase(
            ['method'],
            '#[After]',
            '@after',
        );

        yield 'handle AfterClass' => self::createCase(
            ['method'],
            '#[AfterClass]',
            '@afterClass',
        );

        yield 'handle BackupGlobals enabled' => self::createCase(
            ['class', 'method'],
            '#[BackupGlobals(true)]',
            '@backupGlobals enabled',
        );

        yield 'handle BackupGlobals disabled' => self::createCase(
            ['class', 'method'],
            '#[BackupGlobals(false)]',
            '@backupGlobals disabled',
        );

        yield 'handle BackupGlobals no' => self::createCase(
            ['class', 'method'],
            '#[BackupGlobals(false)]',
            '@backupGlobals no',
        );

        yield 'handle BackupStaticProperties enabled' => self::createCase(
            ['class', 'method'],
            '#[BackupStaticProperties(true)]',
            '@backupStaticAttributes enabled',
        );

        yield 'handle BackupStaticProperties disabled' => self::createCase(
            ['class', 'method'],
            '#[BackupStaticProperties(false)]',
            '@backupStaticAttributes disabled',
        );

        yield 'handle Before' => self::createCase(
            ['method'],
            '#[Before]',
            '@before',
        );

        yield 'handle BeforeClass' => self::createCase(
            ['method'],
            '#[BeforeClass]',
            '@beforeClass',
        );

        yield 'handle CoversClass' => self::createCase(
            ['class'],
            '#[CoversClass(\\VendorName\\ClassName::class)]',
            '@covers \VendorName\ClassName',
        );

        yield 'handle CoversFunction' => self::createCase(
            ['class'],
            "#[CoversFunction('functionName')]",
            '@covers ::functionName',
        );

        yield 'handle CoversNothing' => self::createCase(
            ['class', 'method'],
            '#[CoversNothing]',
            '@coversNothing',
        );

        yield 'handle DataProvider' => self::createCase(
            ['method'],
            "#[DataProvider('provideFooCases')]",
            '@dataProvider provideFooCases',
        );

        yield 'handle DataProviderExternal' => self::createCase(
            ['method'],
            "#[DataProviderExternal(BarTest::class, 'provideFooCases')]",
            '@dataProvider BarTest::provideFooCases',
        );

        yield 'handle Depends' => self::createCase(
            ['method'],
            "#[Depends('methodName')]",
            '@depends methodName',
        );

        yield 'handle DependsExternal' => self::createCase(
            ['method'],
            "#[DependsExternal(ClassName::class, 'methodName')]",
            '@depends ClassName::methodName',
        );

        yield 'handle DependsExternalUsingDeepClone' => self::createCase(
            ['method'],
            "#[DependsExternalUsingDeepClone(ClassName::class, 'methodName')]",
            '@depends clone ClassName::methodName',
        );

        yield 'handle DependsExternalUsingShallowClone' => self::createCase(
            ['method'],
            "#[DependsExternalUsingShallowClone(ClassName::class, 'methodName')]",
            '@depends shallowClone ClassName::methodName',
        );

        yield 'handle DependsOnClass' => self::createCase(
            ['method'],
            '#[DependsOnClass(ClassName::class)]',
            '@depends ClassName::class',
        );

        yield 'handle DependsOnClassUsingDeepClone' => self::createCase(
            ['method'],
            '#[DependsOnClassUsingDeepClone(ClassName::class)]',
            '@depends clone ClassName::class',
        );

        yield 'handle DependsOnClassUsingShallowClone' => self::createCase(
            ['method'],
            '#[DependsOnClassUsingShallowClone(ClassName::class)]',
            '@depends shallowClone ClassName::class',
        );

        yield 'handle DependsUsingDeepClone' => self::createCase(
            ['method'],
            "#[DependsUsingDeepClone('methodName')]",
            '@depends clone methodName',
        );

        yield 'handle DependsUsingShallowClone' => self::createCase(
            ['method'],
            "#[DependsUsingShallowClone('methodName')]",
            '@depends shallowClone methodName',
        );

        yield 'handle DoesNotPerformAssertions' => self::createCase(
            ['class', 'method'],
            '#[DoesNotPerformAssertions]',
            '@doesNotPerformAssertions',
        );

        yield 'handle Group' => self::createCase(
            ['class', 'method'],
            "#[Group('groupName')]",
            '@group groupName',
        );

        yield 'handle Large' => self::createCase(
            ['class'],
            '#[Large]',
            '@large',
        );

        yield 'handle Medium' => self::createCase(
            ['class'],
            '#[Medium]',
            '@medium',
        );

        yield 'handle PostCondition' => self::createCase(
            ['method'],
            '#[PostCondition]',
            '@postCondition',
        );

        yield 'handle PreCondition' => self::createCase(
            ['method'],
            '#[PreCondition]',
            '@preCondition',
        );

        yield 'handle PreserveGlobalState enabled' => self::createCase(
            ['class', 'method'],
            '#[PreserveGlobalState(true)]',
            '@preserveGlobalState enabled',
        );

        yield 'handle PreserveGlobalState disabled' => self::createCase(
            ['class', 'method'],
            '#[PreserveGlobalState(false)]',
            '@preserveGlobalState    disabled',
        );

        yield 'handle RequiresFunction' => self::createCase(
            ['class', 'method'],
            "#[RequiresFunction('imap_open')]",
            '@requires function imap_open',
        );

        yield 'handle RequiresMethod' => self::createCase(
            ['class', 'method'],
            "#[RequiresMethod(ReflectionMethod::class, 'setAccessible')]",
            '@requires function ReflectionMethod::setAccessible',
        );

        yield 'handle RequiresOperatingSystem' => self::createCase(
            ['class', 'method'],
            "#[RequiresOperatingSystem('Linux')]",
            '@requires OS Linux',
        );

        yield 'handle RequiresOperatingSystemFamily' => self::createCase(
            ['class', 'method'],
            "#[RequiresOperatingSystemFamily('Windows')]",
            '@requires OSFAMILY Windows',
        );

        yield 'handle RequiresPhp' => self::createCase(
            ['class', 'method'],
            "#[RequiresPhp('8.1.20')]",
            '@requires PHP 8.1.20',
        );

        yield 'handle RequiresPhpExtension' => self::createCase(
            ['class', 'method'],
            "#[RequiresPhpExtension('mysqli', '>= 8.3.0')]",
            '@requires extension mysqli >= 8.3.0',
        );

        yield 'handle RequiresPhpunit' => self::createCase(
            ['class', 'method'],
            "#[RequiresPhpunit('^10.1.0')]",
            '@requires PHPUnit ^10.1.0',
        );

        yield 'handle RequiresSetting' => self::createCase(
            ['class', 'method'],
            "#[RequiresSetting('date.timezone', 'Europe/London')]",
            '@requires setting date.timezone Europe/London',
        );

        yield 'handle RunInSeparateProcess' => self::createCase(
            ['method'],
            '#[RunInSeparateProcess]',
            '@runInSeparateProcess',
        );

        yield 'handle RunTestsInSeparateProcesses' => self::createCase(
            ['class'],
            '#[RunTestsInSeparateProcesses]',
            '@runTestsInSeparateProcesses',
        );

        yield 'handle Small' => self::createCase(
            ['class'],
            '#[Small]',
            '@small',
        );

        yield 'handle Test' => self::createCase(
            ['method'],
            '#[Test]',
            '@test',
        );

        yield 'handle TestDox' => self::createCase(
            ['class', 'method'],
            "#[TestDox('Hello world!')]",
            '@testDox Hello world!',
        );

        yield 'handle Ticket' => self::createCase(
            ['class', 'method'],
            "#[Ticket('ABC-123')]",
            '@ticket ABC-123',
        );

        yield 'handle UsesClass' => self::createCase(
            ['class'],
            '#[UsesClass(ClassName::class)]',
            '@uses ClassName',
        );

        yield 'handle UsesFunction' => self::createCase(
            ['class'],
            "#[UsesFunction('functionName')]",
            '@uses ::functionName',
        );

        yield 'handle TestWith' => [
            <<<'PHP'
                <?php
                /**
                 * @testWith [true, false]
                 */
                class FooTest extends \PHPUnit\Framework\TestCase {
                    /**
                     */
                    #[\PHPUnit\Framework\Attributes\TestWithJson('[1, 2]')]
                    public function testFoo($x) {}
                    /**
                     */
                    #[\PHPUnit\Framework\Attributes\TestWithJson('[3, 4, 5]')]
                    #[\PHPUnit\Framework\Attributes\TestWithJson('[6, 7, 8]')]
                    #[\PHPUnit\Framework\Attributes\TestWithJson('["a", "b"]')]
                    #[\PHPUnit\Framework\Attributes\TestWithJson('["c\'d"]')]
                    public function testBar($x) {}
                }
                PHP,
            <<<'PHP'
                <?php
                /**
                 * @testWith [true, false]
                 */
                class FooTest extends \PHPUnit\Framework\TestCase {
                    /**
                     * @testWith [1, 2]
                     */
                    public function testFoo($x) {}
                    /**
                     * @testWith [3, 4, 5]
                     *           [6, 7, 8]
                     *           ["a", "b"]
                     *           ["c'd"]
                     */
                    public function testBar($x) {}
                }
                PHP,
        ];
    }

    /**
     * @param non-empty-list<'class'|'method'> $scopes
     *
     * @return array{string, string}
     */
    private static function createCase(array $scopes, string $expectedAttribute, string $inputAnnotation): array
    {
        $expectedAttribute = str_replace('#[', '#[\\PHPUnit\\Framework\\Attributes\\', $expectedAttribute);

        return [
            sprintf(
                <<<'PHP'
                    <?php
                    %s
                    class FooTest extends \PHPUnit\Framework\TestCase {
                        %s
                        public function testFoo($x) {}
                        %s
                        public function testBar($x) {}
                    }
                    PHP,
                \in_array('class', $scopes, true)
                    ? sprintf("/**\n */\n%s", $expectedAttribute)
                    : sprintf("/**\n * %s\n */", $inputAnnotation),
                \in_array('method', $scopes, true)
                    ? sprintf("/**\n     */\n    %s", $expectedAttribute)
                    : sprintf("/**\n     * %s\n     */", $inputAnnotation),
                \in_array('method', $scopes, true)
                    ? sprintf("\n    %s", $expectedAttribute)
                    : sprintf('/** %s */', $inputAnnotation),
            ),
            sprintf(
                <<<'PHP'
                    <?php
                    /**
                     * %s
                     */
                    class FooTest extends \PHPUnit\Framework\TestCase {
                        /**
                         * %s
                         */
                        public function testFoo($x) {}
                        /** %s */
                        public function testBar($x) {}
                    }
                    PHP,
                $inputAnnotation,
                $inputAnnotation,
                $inputAnnotation,
            ),
        ];
    }
}
