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
 * @covers \PhpCsFixer\Fixer\PhpUnit\PhpUnitDataProviderNameFixer
 */
final class PhpUnitDataProviderNameFixerTest extends AbstractFixerTestCase
{
    /**
     * @param array<string, bool> $configuration
     *
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null, array $configuration = []): void
    {
        $this->fixer->configure($configuration);
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<array{0: string, 1?: string, 2?: array<string, string>}>
     */
    public static function provideFixCases(): iterable
    {
        yield 'data provider named with different casing' => [
            '<?php
class FooTest extends TestCase {
    /**
     * @dataProvider provideFooCases
     */
    public function testFoo() {}
    public function provideFooCases() {}
}',
            '<?php
class FooTest extends TestCase {
    /**
     * @dataProvider provideFooCases
     */
    public function testFoo() {}
    public function PROVIDEFOOCASES() {}
}',
        ];

        yield 'fixing simple scenario with test class prefixed' => [
            '<?php
class FooTest extends TestCase {
    /**
     * @dataProvider provideFooCases
     */
    public function testFoo() {}
    public function provideFooCases() {}
}',
            '<?php
class FooTest extends TestCase {
    /**
     * @dataProvider fooDataProvider
     */
    public function testFoo() {}
    public function fooDataProvider() {}
}',
        ];

        yield 'fixing simple scenario with test class annotated' => [
            '<?php
class FooTest extends TestCase {
    /**
     * @test
     * @dataProvider provideFooCases
     */
    public function foo() {}
    public function provideFooCases() {}
}',
            '<?php
class FooTest extends TestCase {
    /**
     * @test
     * @dataProvider fooDataProvider
     */
    public function foo() {}
    public function fooDataProvider() {}
}',
        ];

        yield 'data provider not found' => [
            '<?php
class FooTest extends TestCase {
    /**
     * @dataProvider notExistingFunction
     */
    public function testFoo() {}
}',
        ];

        yield 'data provider used multiple times' => [
            '<?php
class FooTest extends TestCase {
    /**
     * @dataProvider reusedDataProvider
     */
    public function testFoo() {}
    /**
     * @dataProvider reusedDataProvider
     */
    public function testBar() {}
    public function reusedDataProvider() {}
}',
        ];

        yield 'data provider call without function' => [
            '<?php
class FooTest extends TestCase {
    /**
     * @dataProvider fooDataProvider
     */
    private $prop;
}',
        ];

        yield 'data provider target name already used' => [
            '<?php
class FooTest extends TestCase {
    /**
     * @dataProvider dataProvider
     */
    public function testFoo() {}
    public function dataProvider() {}
    public function provideFooCases() {}
}',
        ];

        yield 'data provider defined for anonymous function' => [
            '<?php
class FooTest extends TestCase {
    public function testFoo()
    {
        /**
         * @dataProvider notDataProvider
         */
        function () { return true; };
    }
    public function notDataProvider() {}
}',
        ];

        yield 'multiple data providers for one test function' => [
            '<?php
class FooTest extends TestCase {
    /**
     * @dataProvider foo1DataProvider
     * @dataProvider foo2DataProvider
     */
    public function testFoo() {}
    public function foo1DataProvider() {}
    public function foo2DataProvider() {}
}',
        ];

        yield 'data provider with new name being part of FQCN used in the code' => [
            '<?php
class FooTest extends TestCase {
    /**
     * @dataProvider provideFooCases
     */
    public function testFoo() {
        $x = Foo\ProvideFooCases::X_DEFAULT;
    }
    public function provideFooCases() {}
}',
            '<?php
class FooTest extends TestCase {
    /**
     * @dataProvider foo
     */
    public function testFoo() {
        $x = Foo\ProvideFooCases::X_DEFAULT;
    }
    public function foo() {}
}',
        ];

        yield 'complex example' => [
            '<?php
class FooTest extends TestCase {
    /** @dataProvider notExistingFunction */
    public function testClosure()
    {
        /** Preparing data */
        $x = 0;
        /** @dataProvider notDataProvider */
        function () { return true; };
    }

    /**
     * @dataProvider reusedDataProvider
     * @dataProvider testFooProvider
     */
    public function testFoo() {}

    /**
     * @dataProvider reusedDataProvider
     * @dataProvider testBarProvider
     */
    public function testBar() {}

    public function reusedDataProvider() {}

    /** @dataProvider provideBazCases */
    public function testBaz() {}
    public function provideBazCases() {}

    /** @dataProvider provideSomethingCases */
    public function testSomething() {}
    public function provideSomethingCases() {}
    public function testFooProvider() {}
    public function testBarProvider() {}
}',
            '<?php
class FooTest extends TestCase {
    /** @dataProvider notExistingFunction */
    public function testClosure()
    {
        /** Preparing data */
        $x = 0;
        /** @dataProvider notDataProvider */
        function () { return true; };
    }

    /**
     * @dataProvider reusedDataProvider
     * @dataProvider testFooProvider
     */
    public function testFoo() {}

    /**
     * @dataProvider reusedDataProvider
     * @dataProvider testBarProvider
     */
    public function testBar() {}

    public function reusedDataProvider() {}

    /** @dataProvider provideBazCases */
    public function testBaz() {}
    public function provideBazCases() {}

    /** @dataProvider someDataProvider */
    public function testSomething() {}
    public function someDataProvider() {}
    public function testFooProvider() {}
    public function testBarProvider() {}
}',
        ];

        yield 'fixing when string like expected data provider name is present' => [
            '<?php
class FooTest extends TestCase {
    /**
     * @dataProvider provideFooCases
     */
    public function testFoo() {
        $foo->provideFooCases(); // do not get fooled that data provider name is already taken
    }
    public function provideFooCases() {}
}',
            '<?php
class FooTest extends TestCase {
    /**
     * @dataProvider fooDataProvider
     */
    public function testFoo() {
        $foo->provideFooCases(); // do not get fooled that data provider name is already taken
    }
    public function fooDataProvider() {}
}',
        ];

        foreach (['abstract', 'final', 'private', 'protected', 'static', '/* private */'] as $modifier) {
            yield sprintf('test function with %s modifier', $modifier) => [
                sprintf('<?php
                abstract class FooTest extends TestCase {
                    /**
                     * @dataProvider provideFooCases
                     */
                    %s function testFoo() %s
                    public function provideFooCases() {}
                }', $modifier, 'abstract' === $modifier ? ';' : '{}'),
                sprintf('<?php
                abstract class FooTest extends TestCase {
                    /**
                     * @dataProvider fooDataProvider
                     */
                    %s function testFoo() %s
                    public function fooDataProvider() {}
                }', $modifier, 'abstract' === $modifier ? ';' : '{}'),
            ];
        }

        foreach (
            [
                'custom prefix' => [
                    'theBestPrefixFooCases',
                    'testFoo',
                    ['prefix' => 'theBestPrefix'],
                ],
                'custom suffix' => [
                    'provideFooTheBestSuffix',
                    'testFoo',
                    ['suffix' => 'TheBestSuffix'],
                ],
                'custom prefix and suffix' => [
                    'theBestPrefixFooTheBestSuffix',
                    'testFoo',
                    ['prefix' => 'theBestPrefix', 'suffix' => 'TheBestSuffix'],
                ],
                'empty prefix' => [
                    'fooDataProvider',
                    'testFoo',
                    ['prefix' => '', 'suffix' => 'DataProvider'],
                ],
                'empty suffix' => [
                    'dataProviderForFoo',
                    'testFoo',
                    ['prefix' => 'dataProviderFor', 'suffix' => ''],
                ],
                'prefix and suffix with underscores' => [
                    'provide_foo_data',
                    'test_foo',
                    ['prefix' => 'provide_', 'suffix' => '_data'],
                ],
                'empty prefix and suffix with underscores' => [
                    'foo_data_provider',
                    'test_foo',
                    ['prefix' => '', 'suffix' => '_data_provider'],
                ],
                'prefix with underscores and empty suffix' => [
                    'data_provider_foo',
                    'test_foo',
                    ['prefix' => 'data_provider_', 'suffix' => ''],
                ],
                'prefix with underscores and empty suffix and test function starting with uppercase' => [
                    'data_provider_Foo',
                    'test_Foo',
                    ['prefix' => 'data_provider_', 'suffix' => ''],
                ],
                'prefix and suffix with underscores and test function having multiple consecutive underscores' => [
                    'provide_foo_data',
                    'test___foo',
                    ['prefix' => 'provide_', 'suffix' => '_data'],
                ],
                'uppercase naming' => [
                    'PROVIDE_FOO_DATA',
                    'TEST_FOO',
                    ['prefix' => 'PROVIDE_', 'suffix' => '_DATA'],
                ],
                'camelCase test function and prefix with underscores' => [
                    'data_provider_FooBar',
                    'testFooBar',
                    ['prefix' => 'data_provider_', 'suffix' => ''],
                ],
            ] as $name => [$dataProvider, $testFunction, $config]
        ) {
            yield $name => [
                sprintf('<?php
                    class FooTest extends TestCase {
                        /**
                         * @dataProvider %s
                         */
                        public function %s() {}
                        public function %s() {}
                    }', $dataProvider, $testFunction, $dataProvider),
                sprintf('<?php
                    class FooTest extends TestCase {
                        /**
                         * @dataProvider dtPrvdr
                         */
                        public function %s() {}
                        public function dtPrvdr() {}
                    }', $testFunction),
                $config,
            ];
        }
    }
}
