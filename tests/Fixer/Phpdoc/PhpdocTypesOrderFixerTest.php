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

namespace PhpCsFixer\Tests\Fixer\Phpdoc;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Phpdoc\PhpdocTypesOrderFixer
 */
final class PhpdocTypesOrderFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixWithAlphaAlgorithmAndNullAlwaysFirstCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixCases
     */
    public function testFixWithNullFirst($expected, $input = null)
    {
        $this->fixer->configure([
            'sort_algorithm' => 'none',
            'null_adjustment' => 'always_first',
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixCases()
    {
        return [
            [
                '<?php /** @var null|string */',
                '<?php /** @var string|null */',
            ],
            [
                '<?php /** @param null|string $foo */',
                '<?php /** @param string|null $foo */',
            ],
            [
                '<?php /** @property null|string $foo */',
                '<?php /** @property string|null $foo */',
            ],
            [
                '<?php /** @property-read null|string $foo */',
                '<?php /** @property-read string|null $foo */',
            ],
            [
                '<?php /** @property-write null|string $foo */',
                '<?php /** @property-write string|null $foo */',
            ],
            [
                '<?php /** @method null|string foo(null|int $foo, null|string $bar) */',
                '<?php /** @method string|null foo(int|null $foo, string|null $bar) */',
            ],
            [
                '<?php /** @return null|string */',
                '<?php /** @return string|null */',
            ],
            [
                '<?php /** @var null|string[]|resource|false|object|Foo|Bar\Baz|bool[]|string|array|int */',
                '<?php /** @var string[]|resource|false|object|null|Foo|Bar\Baz|bool[]|string|array|int */',
            ],
            [
                '<?php /** @var null|array<int, string> Foo */',
                '<?php /** @var array<int, string>|null Foo */',
            ],
            [
                '<?php /** @var null|array<int, array<string>> Foo */',
                '<?php /** @var array<int, array<string>>|null Foo */',
            ],
            [
                '<?php /** @var NULL|string */',
                '<?php /** @var string|NULL */',
            ],
            [
                '<?php /** @var Foo|?Bar */',
            ],
            [
                '<?php /** @var ?Foo|Bar */',
            ],
            [
                '<?php /** @var array<null|string> */',
                '<?php /** @var array<string|null> */',
            ],
            [
                '<?php /** @var array<int, null|string> */',
                '<?php /** @var array<int, string|null> */',
            ],
            [
                '<?php /** @var array<int,     array<null|int|string>> */',
                '<?php /** @var array<int,     array<int|string|null>> */',
            ],
            [
                '<?php /** @var null|null */',
            ],
            [
                '<?php /** @var null|\null */',
            ],
            [
                '<?php /** @var \null|null */',
            ],
            [
                '<?php /** @var \null|\null */',
            ],
            [
                '<?php /** @var \null|int */',
                '<?php /** @var int|\null */',
            ],
            [
                '<?php /** @var array<\null|int> */',
                '<?php /** @var array<int|\null> */',
            ],
            [
                '<?php /** @var array<int, array<int, array<int, array<int, array<null|OutputInterface>>>>> */',
                '<?php /** @var array<int, array<int, array<int, array<int, array<OutputInterface|null>>>>> */',
            ],
            [
                '<?php /** @var null|Foo[]|Foo|Foo\Bar|Foo_Bar */',
                '<?php /** @var Foo[]|null|Foo|Foo\Bar|Foo_Bar */',
            ],
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixWithNullLastCases
     */
    public function testFixWithNullLast($expected, $input = null)
    {
        $this->fixer->configure([
            'sort_algorithm' => 'none',
            'null_adjustment' => 'always_last',
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithNullLastCases()
    {
        return [
            [
                '<?php /** @var string|null */',
                '<?php /** @var null|string */',
            ],
            [
                '<?php /** @param string|null $foo */',
                '<?php /** @param null|string $foo */',
            ],
            [
                '<?php /** @property string|null $foo */',
                '<?php /** @property null|string $foo */',
            ],
            [
                '<?php /** @property-read string|null $foo */',
                '<?php /** @property-read null|string $foo */',
            ],
            [
                '<?php /** @property-write string|null $foo */',
                '<?php /** @property-write null|string $foo */',
            ],
            [
                '<?php /** @method string|null foo(int|null $foo, string|null $bar) */',
                '<?php /** @method null|string foo(null|int $foo, null|string $bar) */',
            ],
            [
                '<?php /** @return string|null */',
                '<?php /** @return null|string */',
            ],
            [
                '<?php /** @var string[]|resource|false|object|Foo|Bar\Baz|bool[]|string|array|int|null */',
                '<?php /** @var string[]|resource|false|object|null|Foo|Bar\Baz|bool[]|string|array|int */',
            ],
            [
                '<?php /** @var array<int, string>|null Foo */',
                '<?php /** @var null|array<int, string> Foo */',
            ],
            [
                '<?php /** @var array<int, array<string>>|null Foo */',
                '<?php /** @var null|array<int, array<string>> Foo */',
            ],
            [
                '<?php /** @var string|NULL */',
                '<?php /** @var NULL|string */',
            ],
            [
                '<?php /** @var Foo|?Bar */',
            ],
            [
                '<?php /** @var ?Foo|Bar */',
            ],
            [
                '<?php /** @var Foo|?\Bar */',
            ],
            [
                '<?php /** @var ?\Foo|Bar */',
            ],
            [
                '<?php /** @var array<string|null> */',
                '<?php /** @var array<null|string> */',
            ],
            [
                '<?php /** @var array<int, string|null> */',
                '<?php /** @var array<int, null|string> */',
            ],
            [
                '<?php /** @var array<int,     array<int|string|null>> */',
                '<?php /** @var array<int,     array<null|int|string>> */',
            ],
            [
                '<?php /** @var null|null */',
            ],
            [
                '<?php /** @var null|\null */',
            ],
            [
                '<?php /** @var \null|null */',
            ],
            [
                '<?php /** @var \null|\null */',
            ],
            [
                '<?php /** @var int|\null */',
                '<?php /** @var \null|int */',
            ],
            [
                '<?php /** @var array<int|\null> */',
                '<?php /** @var array<\null|int> */',
            ],
            [
                '<?php /** @var array<int, array<int, array<int, array<int, array<OutputInterface|null>>>>> */',
                '<?php /** @var array<int, array<int, array<int, array<int, array<null|OutputInterface>>>>> */',
            ],
            [
                '<?php /** @var Foo[]|Foo|Foo\Bar|Foo_Bar|null */',
                '<?php /** @var Foo[]|null|Foo|Foo\Bar|Foo_Bar */',
            ],
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixWithAlphaAlgorithmCases
     */
    public function testFixWithAlphaAlgorithm($expected, $input = null)
    {
        $this->fixer->configure([
            'sort_algorithm' => 'alpha',
            'null_adjustment' => 'none',
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithAlphaAlgorithmCases()
    {
        return [
            [
                '<?php /** @var int|null|string */',
                '<?php /** @var string|int|null */',
            ],
            [
                '<?php /** @param Bar|\Foo */',
                '<?php /** @param \Foo|Bar */',
            ],
            [
                '<?php /** @property-read \Bar|Foo */',
                '<?php /** @property-read Foo|\Bar */',
            ],
            [
                '<?php /** @property-write bar|Foo */',
                '<?php /** @property-write Foo|bar */',
            ],
            [
                '<?php /** @return Bar|foo */',
                '<?php /** @return foo|Bar */',
            ],
            [
                '<?php /** @method \Bar|Foo foo(\Bar|Foo $foo, \Bar|Foo $bar) */',
                '<?php /** @method Foo|\Bar foo(Foo|\Bar $foo, Foo|\Bar $bar) */',
            ],
            [
                '<?php /** @var array|Bar\Baz|bool[]|false|Foo|int|null|object|resource|string|string[] */',
                '<?php /** @var string[]|resource|false|object|null|Foo|Bar\Baz|bool[]|string|array|int */',
            ],
            [
                '<?php /** @var array<int, string>|null Foo */',
                '<?php /** @var null|array<int, string> Foo */',
            ],
            [
                '<?php /** @var array<int, array<string>>|null Foo */',
                '<?php /** @var null|array<int, array<string>> Foo */',
            ],
            [
                '<?php /** @var ?Bar|Foo */',
                '<?php /** @var Foo|?Bar */',
            ],
            [
                '<?php /** @var Bar|?Foo */',
                '<?php /** @var ?Foo|Bar */',
            ],
            [
                '<?php /** @var ?\Bar|Foo */',
                '<?php /** @var Foo|?\Bar */',
            ],
            [
                '<?php /** @var Bar|?\Foo */',
                '<?php /** @var ?\Foo|Bar */',
            ],
            [
                '<?php /** @var array<null|string> */',
                '<?php /** @var array<string|null> */',
            ],
            [
                '<?php /** @var array<int|string, null|string> */',
                '<?php /** @var array<string|int, string|null> */',
            ],
            [
                '<?php /** @var array<int|string,     array<int|string, null|string>> */',
                '<?php /** @var array<string|int,     array<string|int, string|null>> */',
            ],
            [
                '<?php /** @var null|null */',
            ],
            [
                '<?php /** @var null|\null */',
            ],
            [
                '<?php /** @var \null|null */',
            ],
            [
                '<?php /** @var \null|\null */',
            ],
            [
                '<?php /** @var int|\null|string */',
                '<?php /** @var string|\null|int */',
            ],
            [
                '<?php /** @var array<int|\null|string> */',
                '<?php /** @var array<string|\null|int> */',
            ],
            [
                '<?php /** @var array<int, array<int, array<int, array<int, array<null|OutputInterface>>>>> */',
                '<?php /** @var array<int, array<int, array<int, array<int, array<OutputInterface|null>>>>> */',
            ],
            [
                '<?php /** @var Foo|Foo[]|Foo\Bar|Foo_Bar|null */',
                '<?php /** @var Foo[]|null|Foo|Foo\Bar|Foo_Bar */',
            ],
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixWithAlphaAlgorithmAndNullAlwaysFirstCases
     */
    public function testFixWithAlphaAlgorithmAndNullAlwaysFirst($expected, $input = null)
    {
        $this->fixer->configure([
            'sort_algorithm' => 'alpha',
            'null_adjustment' => 'always_first',
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithAlphaAlgorithmAndNullAlwaysFirstCases()
    {
        return [
            [
                '<?php /** @var null|int|string */',
                '<?php /** @var string|int|null */',
            ],
            [
                '<?php /** @param Bar|\Foo */',
                '<?php /** @param \Foo|Bar */',
            ],
            [
                '<?php /** @property-read \Bar|Foo */',
                '<?php /** @property-read Foo|\Bar */',
            ],
            [
                '<?php /** @property-write bar|Foo */',
                '<?php /** @property-write Foo|bar */',
            ],
            [
                '<?php /** @return Bar|foo */',
                '<?php /** @return foo|Bar */',
            ],
            [
                '<?php /** @method \Bar|Foo foo(\Bar|Foo $foo, \Bar|Foo $bar) */',
                '<?php /** @method Foo|\Bar foo(Foo|\Bar $foo, Foo|\Bar $bar) */',
            ],
            [
                '<?php /** @var null|array|Bar\Baz|bool[]|false|Foo|int|object|resource|string|string[] */',
                '<?php /** @var string[]|resource|false|object|null|Foo|Bar\Baz|bool[]|string|array|int */',
            ],
            [
                '<?php /** @var null|array<int, string> Foo */',
            ],
            [
                '<?php /** @var null|array<int, array<string>> Foo */',
            ],
            [
                '<?php /** @var NULL|int|string */',
                '<?php /** @var string|int|NULL */',
            ],
            [
                '<?php /** @var ?Bar|Foo */',
                '<?php /** @var Foo|?Bar */',
            ],
            [
                '<?php /** @var Bar|?Foo */',
                '<?php /** @var ?Foo|Bar */',
            ],
            [
                '<?php /** @var ?\Bar|Foo */',
                '<?php /** @var Foo|?\Bar */',
            ],
            [
                '<?php /** @var Bar|?\Foo */',
                '<?php /** @var ?\Foo|Bar */',
            ],
            [
                '<?php /** @var array<null|int|string> */',
                '<?php /** @var array<string|int|null> */',
            ],
            [
                '<?php /** @var array<int|string, null|int|string> */',
                '<?php /** @var array<string|int, string|int|null> */',
            ],
            [
                '<?php /** @var array<int|string,     array<int|string, null|int|string>> */',
                '<?php /** @var array<string|int,     array<string|int, string|int|null>> */',
            ],
            [
                '<?php /** @var null|null */',
            ],
            [
                '<?php /** @var null|\null */',
            ],
            [
                '<?php /** @var \null|null */',
            ],
            [
                '<?php /** @var \null|\null */',
            ],
            [
                '<?php /** @var array<\null|int|string> */',
                '<?php /** @var array<string|\null|int> */',
            ],
            [
                '<?php /** @var array<int, array<int, array<int, array<int, array<null|OutputInterface>>>>> */',
                '<?php /** @var array<int, array<int, array<int, array<int, array<OutputInterface|null>>>>> */',
            ],
            [
                '<?php /** @var null|Foo|Foo[]|Foo\Bar|Foo_Bar */',
                '<?php /** @var Foo[]|null|Foo|Foo\Bar|Foo_Bar */',
            ],
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixWithAlphaAlgorithmAndNullAlwaysLastCases
     */
    public function testFixWithAlphaAlgorithmAndNullAlwaysLast($expected, $input = null)
    {
        $this->fixer->configure([
            'sort_algorithm' => 'alpha',
            'null_adjustment' => 'always_last',
        ]);

        $this->doTest($expected, $input);
    }

    public function provideFixWithAlphaAlgorithmAndNullAlwaysLastCases()
    {
        return [
            [
                '<?php /** @var int|string|null */',
                '<?php /** @var string|int|null */',
            ],
            [
                '<?php /** @param Bar|\Foo */',
                '<?php /** @param \Foo|Bar */',
            ],
            [
                '<?php /** @property-read \Bar|Foo */',
                '<?php /** @property-read Foo|\Bar */',
            ],
            [
                '<?php /** @property-write bar|Foo */',
                '<?php /** @property-write Foo|bar */',
            ],
            [
                '<?php /** @return Bar|foo */',
                '<?php /** @return foo|Bar */',
            ],
            [
                '<?php /** @method \Bar|Foo foo(\Bar|Foo $foo, \Bar|Foo $bar) */',
                '<?php /** @method Foo|\Bar foo(Foo|\Bar $foo, Foo|\Bar $bar) */',
            ],
            [
                '<?php /** @var array|Bar\Baz|bool[]|false|Foo|int|object|resource|string|string[]|null */',
                '<?php /** @var string[]|resource|false|object|null|Foo|Bar\Baz|bool[]|string|array|int */',
            ],
            [
                '<?php /** @var array<int, string>|null Foo */',
                '<?php /** @var null|array<int, string> Foo */',
            ],
            [
                '<?php /** @var array<int, array<string>>|null Foo */',
                '<?php /** @var null|array<int, array<string>> Foo */',
            ],
            [
                '<?php /** @var int|string|NULL */',
                '<?php /** @var string|int|NULL */',
            ],
            [
                '<?php /** @var ?Bar|Foo */',
                '<?php /** @var Foo|?Bar */',
            ],
            [
                '<?php /** @var Bar|?Foo */',
                '<?php /** @var ?Foo|Bar */',
            ],
            [
                '<?php /** @var ?\Bar|Foo */',
                '<?php /** @var Foo|?\Bar */',
            ],
            [
                '<?php /** @var Bar|?\Foo */',
                '<?php /** @var ?\Foo|Bar */',
            ],
            [
                '<?php /** @var array<int|string|null> */',
                '<?php /** @var array<string|int|null> */',
            ],
            [
                '<?php /** @var array<int|string, int|string|null> */',
                '<?php /** @var array<string|int, string|int|null> */',
            ],
            [
                '<?php /** @var array<int|string,     array<int|string, int|string|null>> */',
                '<?php /** @var array<string|int,     array<string|int, string|int|null>> */',
            ],
            [
                '<?php /** @var null|null */',
            ],
            [
                '<?php /** @var null|\null */',
            ],
            [
                '<?php /** @var \null|null */',
            ],
            [
                '<?php /** @var \null|\null */',
            ],
            [
                '<?php /** @var array<int|string|\null> */',
                '<?php /** @var array<string|\null|int> */',
            ],
            [
                '<?php /** @var array<int, array<int, array<int, array<int, array<OutputInterface|null>>>>> */',
                '<?php /** @var array<int, array<int, array<int, array<int, array<null|OutputInterface>>>>> */',
            ],
            [
                '<?php /** @var Foo|Foo[]|Foo\Bar|Foo_Bar|null */',
                '<?php /** @var Foo[]|null|Foo|Foo\Bar|Foo_Bar */',
            ],
        ];
    }
}
