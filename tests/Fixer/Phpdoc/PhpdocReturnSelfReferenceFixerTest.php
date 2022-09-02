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

namespace PhpCsFixer\Tests\Fixer\Phpdoc;

use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Phpdoc\PhpdocReturnSelfReferenceFixer
 */
final class PhpdocReturnSelfReferenceFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideDefaultConfigurationTestCases
     */
    public function testFixWithDefaultConfiguration(string $expected, ?string $input = null): void
    {
        $this->fixer->configure([]);
        $this->doTest($expected, $input);
    }

    public function provideDefaultConfigurationTestCases(): array
    {
        return [
            [
                '<?php interface A{/** @return    $this */public function test();}',
                '<?php interface A{/** @return    this */public function test();}',
            ],
            [
                '<?php interface B{/** @return self|int */function test();}',
                '<?php interface B{/** @return $SELF|int */function test();}',
            ],
            [
                '<?php class D {} /** @return {@this} */ require_once($a);echo 1;echo 1;echo 1;echo 1;echo 1;echo 1;echo 1;echo 1;',
            ],
            [
                '<?php /** @return this */ require_once($a);echo 1;echo 1;echo 1;echo 1;echo 1;echo 1;echo 1;echo 1; class E {}',
            ],
            [
                '<?php

trait SomeTrait
{
    /** @return $this */
    public function someTest(): self
    {
        return $this;
    }
}
// class Foo { use Bla; } $a = (new Foo())->someTest();',
                '<?php

trait SomeTrait
{
    /** @return this */
    public function someTest(): self
    {
        return $this;
    }
}
// class Foo { use Bla; } $a = (new Foo())->someTest();',
            ],
        ];
    }

    /**
     * @param array<string, string> $configuration
     *
     * @dataProvider provideTestCases
     */
    public function testFix(string $expected, ?string $input = null, array $configuration = []): void
    {
        $this->fixer->configure(['replacements' => $configuration]);
        $this->doTest($expected, $input);
    }

    public function provideTestCases(): array
    {
        return [
            [
                '<?php interface C{/** @return $self|int */function test();}',
                null,
                ['$static' => 'static'],
            ],
        ];
    }

    /**
     * @dataProvider provideGeneratedFixCases
     */
    public function testGeneratedFix(string $expected, string $input): void
    {
        $config = ['replacements' => [$input => $expected]];
        $this->fixer->configure($config);

        $expected = sprintf('<?php
/**
 * Please do not use @return %s|static|self|this|$static|$self|@static|@self|@this as return type hint
 */
class F
{
    /**
     * @param %s
     *
     * @return %s
     */
     public function AB($self)
     {
        return $this; // %s
     }
}
', $input, $input, $expected, $input);

        $input = sprintf('<?php
/**
 * Please do not use @return %s|static|self|this|$static|$self|@static|@self|@this as return type hint
 */
class F
{
    /**
     * @param %s
     *
     * @return %s
     */
     public function AB($self)
     {
        return $this; // %s
     }
}
', $input, $input, $input, $input);

        $this->doTest($expected, $input);
    }

    public function provideGeneratedFixCases(): array
    {
        return [
            ['$this', 'this'],
            ['$this', '@this'],
            ['self', '$self'],
            ['self', '@self'],
            ['static', '$static'],
            ['static', '@STATIC'],
        ];
    }

    /**
     * @param array<mixed> $configuration
     *
     * @dataProvider provideInvalidConfigurationCases
     */
    public function testInvalidConfiguration(array $configuration, string $message): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageMatches(sprintf('/^\[phpdoc_return_self_reference\] %s$/', preg_quote($message, '/')));

        $this->fixer->configure($configuration);
    }

    public function provideInvalidConfigurationCases(): array
    {
        return [
            [
                ['replacements' => [1 => 'a']],
                'Invalid configuration: Unknown key "integer#1", expected any of "this", "@this", "$self", "@self", "$static", "@static".',
            ],
            [
                ['replacements' => [
                    'this' => 'foo',
                ]],
                'Invalid configuration: Unknown value "string#foo", expected any of "$this", "static", "self".',
            ],
        ];
    }

    public function testAnonymousClassFixing(): void
    {
        $this->doTest(
            '<?php
                $a = new class() {

                    /** @return $this */
                    public function a() {
                    }
                };

                class C
                {
                    public function A()
                    {
                        $a = new class() {
                            /** @return $this */
                            public function a() {}
                        };
                    }
                }
            ',
            '<?php
                $a = new class() {

                    /** @return @this */
                    public function a() {
                    }
                };

                class C
                {
                    public function A()
                    {
                        $a = new class() {
                            /** @return @this */
                            public function a() {}
                        };
                    }
                }
            '
        );
    }

    /**
     * @dataProvider provideFix81Cases
     *
     * @requires PHP 8.1
     */
    public function testFix81(string $expected, string $input): void
    {
        $this->doTest($expected, $input);
    }

    public function provideFix81Cases(): iterable
    {
        yield [
            '<?php
enum Foo {
    case CAT;

    /** @return $this */
    public function test(): self {
        return $this;
    }
}

var_dump(Foo::CAT->test());
',
            '<?php
enum Foo {
    case CAT;

    /** @return this */
    public function test(): self {
        return $this;
    }
}

var_dump(Foo::CAT->test());
',
        ];
    }
}
