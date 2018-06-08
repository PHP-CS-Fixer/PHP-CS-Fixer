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
 * @covers \PhpCsFixer\Fixer\Phpdoc\NoSuperfluousPhpdocTagsFixer
 */
final class NoSuperfluousPhpdocTagsFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases()
    {
        return [
            'no_typehint' => [
                '<?php
class Foo {
    /**
     * @param Bar $bar
     *
     * @return Baz
     */
    public function doFoo($bar) {}
}',
            ],
            'same_typehint' => [
                '<?php
class Foo {
    /**
     */
    public function doFoo(Bar $bar) {}
}',
                '<?php
class Foo {
    /**
     * @param Bar $bar
     */
    public function doFoo(Bar $bar) {}
}',
            ],
            'same_optional_typehint' => [
                '<?php
class Foo {
    /**
     */
    public function doFoo(Bar $bar = null) {}
}',
                '<?php
class Foo {
    /**
     * @param Bar|null $bar
     */
    public function doFoo(Bar $bar = null) {}
}',
            ],
            'same_typehint_with_description' => [
                '<?php
class Foo {
    /**
     * @param Bar $bar an instance of Bar
     */
    public function doFoo(Bar $bar) {}
}',
            ],
            'no_typehint_mixed' => [
                '<?php
class Foo {
    /**
     *
     */
    public function doFoo($bar) {}
}',
                '<?php
class Foo {
    /**
     * @param mixed $bar
     *
     * @return mixed
     */
    public function doFoo($bar) {}
}',
            ],
            'multiple_different_types' => [
                '<?php
class Foo {
    /**
     * @param SubclassOfBar1|SubclassOfBar2 $bar
     */
    public function doFoo(Bar $bar) {}
}',
            ],
            'same_typehint_with_different_casing' => [
                '<?php
class Foo {
    /**
     */
    public function doFoo(Bar $bar) {}
}',
                '<?php
class Foo {
    /**
     * @param bar $bar
     */
    public function doFoo(Bar $bar) {}
}',
            ],
            'multiple_arguments' => [
                '<?php
class Foo {
    /**
     * @param SubclassOfBar1|SubclassOfBar2 $bar
     */
    public function doFoo(Bar $bar, Baz $baz = null) {}
}',
                '<?php
class Foo {
    /**
     * @param SubclassOfBar1|SubclassOfBar2 $bar
     * @param Baz|null $baz
     */
    public function doFoo(Bar $bar, Baz $baz = null) {}
}',
            ],
            'with_import' => [
                '<?php
use Foo\Bar;

/**
 */
function foo(Bar $bar) {}',
                '<?php
use Foo\Bar;

/**
 * @param Bar $bar
 */
function foo(Bar $bar) {}',
            ],
            'with_root_symbols' => [
                '<?php
/**
 */
function foo(\Foo\Bar $bar) {}',
                '<?php
/**
 * @param \Foo\Bar $bar
 */
function foo(\Foo\Bar $bar) {}',
            ],
            'with_mix_of_imported_and_fully_qualified_symbols' => [
                '<?php
use Foo\Bar;
use Foo\Baz;

/**
 */
function foo(Bar $bar, \Foo\Baz $baz) {}',
                '<?php
use Foo\Bar;
use Foo\Baz;

/**
 * @param \Foo\Bar $bar
 * @param Baz $baz
 */
function foo(Bar $bar, \Foo\Baz $baz) {}',
            ],
            'with_aliased_imported' => [
                '<?php
use Foo\Bar as Baz;

/**
 */
function foo(Baz $bar) {}',
                '<?php
use Foo\Bar as Baz;

/**
 * @param \Foo\Bar $bar
 */
function foo(Baz $bar) {}',
            ],
            'with_unmapped_param' => [
                '<?php
use Foo\Bar;

/**
 * @param Bar
 */
function foo(Bar $bar) {}',
            ],
            'with_param_superfluous_but_not_return' => [
                '<?php
class Foo {
    /**
     *
     * @return Baz
     */
    public function doFoo(Bar $bar) {}
}',
                '<?php
class Foo {
    /**
     * @param Bar $bar
     *
     * @return Baz
     */
    public function doFoo(Bar $bar) {}
}',
            ],
            'with_not_all_params_superfluous' => [
                '<?php
class Foo {
    /**
     * @param Bax|Baz $baxz
     */
    public function doFoo(Bar $bar, $baxz) {}
}',
                '<?php
class Foo {
    /**
     * @param Bar $bar
     * @param Bax|Baz $baxz
     */
    public function doFoo(Bar $bar, $baxz) {}
}',
            ],
            'with_special_type_hints' => [
                '<?php
class Foo {
    /**
     */
    public function doFoo(array $bar, callable $baz) {}
}',
                '<?php
class Foo {
    /**
     * @param array    $bar
     * @param callable $baz
     */
    public function doFoo(array $bar, callable $baz) {}
}',
            ],
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixPhp70Cases
     * @requires PHP 7.0
     */
    public function testFixPhp70($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFixPhp70Cases()
    {
        return [
            'same_typehint' => [
                '<?php
class Foo {
    /**
     *
     */
    public function doFoo(Bar $bar): Baz {}
}',
                '<?php
class Foo {
    /**
     * @param Bar $bar
     *
     * @return Baz
     */
    public function doFoo(Bar $bar): Baz {}
}',
            ],
            'same_typehint_with_description' => [
                '<?php
class Foo {
    /**
     * @param Bar $bar an instance of Bar
     *
     * @return Baz an instance of Baz
     */
    public function doFoo(Bar $bar): Baz {}
}',
            ],
            'multiple_different_types' => [
                '<?php
class Foo {
    /**
     * @param SubclassOfBar1|SubclassOfBar2 $bar
     *
     * @return SubclassOfBaz1|SubclassOfBaz2 $bar
     */
    public function doFoo(Bar $bar): Baz {}
}',
            ],
            'with_import' => [
                '<?php
use Foo\Bar;
use Foo\Baz;

/**
 */
function foo(Bar $bar): Baz {}',
                '<?php
use Foo\Bar;
use Foo\Baz;

/**
 * @param Bar $bar
 * @return Baz
 */
function foo(Bar $bar): Baz {}',
            ],
            'with_root_symbols' => [
                '<?php
/**
 */
function foo(\Foo\Bar $bar): \Foo\Baz {}',
                '<?php
/**
 * @param \Foo\Bar $bar
 * @return \Foo\Baz
 */
function foo(\Foo\Bar $bar): \Foo\Baz {}',
            ],
            'with_mix_of_imported_and_fully_qualified_symbols' => [
                '<?php
use Foo\Bar;
use Foo\Baz;
use Foo\Qux;

/**
 */
function foo(Bar $bar, \Foo\Baz $baz): \Foo\Qux {}',
                '<?php
use Foo\Bar;
use Foo\Baz;
use Foo\Qux;

/**
 * @param \Foo\Bar $bar
 * @param Baz $baz
 * @return Qux
 */
function foo(Bar $bar, \Foo\Baz $baz): \Foo\Qux {}',
            ],
            'with_aliased_imported' => [
                '<?php
use Foo\Bar as Baz;

/**
 */
function foo(Baz $bar): Baz {}',
                '<?php
use Foo\Bar as Baz;

/**
 * @param \Foo\Bar $bar
 * @return \Foo\Bar
 */
function foo(Baz $bar): Baz {}',
            ],
            'with_scalar_type_hints' => [
                '<?php
class Foo {
    /**
     *
     */
    public function doFoo(int $bar, string $baz): bool {}
}',
                '<?php
class Foo {
    /**
     * @param int    $bar
     * @param string $baz
     *
     * @return bool
     */
    public function doFoo(int $bar, string $baz): bool {}
}',
            ],
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixPhp71Cases
     * @requires PHP 7.1
     */
    public function testFixPhp71($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFixPhp71Cases()
    {
        return [
            'same_nullable_typehint' => [
                '<?php
class Foo {
    /**
     *
     */
    public function doFoo(?Bar $bar): ?Baz {}
}',
                '<?php
class Foo {
    /**
     * @param Bar|null $bar
     *
     * @return Baz|null
     */
    public function doFoo(?Bar $bar): ?Baz {}
}',
            ],
            'same_nullable_typehint_with_description' => [
                '<?php
class Foo {
    /**
     * @param Bar|null $bar an instance of Bar
     *
     * @return Baz|null an instance of Baz
     */
    public function doFoo(?Bar $bar): ?Baz {}
}',
            ],
            'same_optional_nullable_typehint' => [
                '<?php
class Foo {
    /**
     */
    public function doFoo(?Bar $bar = null) {}
}',
                '<?php
class Foo {
    /**
     * @param Bar|null $bar
     */
    public function doFoo(?Bar $bar = null) {}
}',
            ],
            'multiple_different_types' => [
                '<?php
class Foo {
    /**
     * @param SubclassOfBar1|SubclassOfBar2|null $bar
     *
     * @return SubclassOfBaz1|SubclassOfBaz2|null $bar
     */
    public function doFoo(?Bar $bar): ?Baz {}
}',
            ],
            'with_import' => [
                '<?php
use Foo\Bar;
use Foo\Baz;

/**
 */
function foo(?Bar $bar): ?Baz {}',
                '<?php
use Foo\Bar;
use Foo\Baz;

/**
 * @param Bar|null $bar
 * @return Baz|null
 */
function foo(?Bar $bar): ?Baz {}',
            ],
            'with_root_symbols' => [
                '<?php
/**
 */
function foo(?\Foo\Bar $bar): ?\Foo\Baz {}',
                '<?php
/**
 * @param \Foo\Bar|null $bar
 * @return \Foo\Baz|null
 */
function foo(?\Foo\Bar $bar): ?\Foo\Baz {}',
            ],
            'with_mix_of_imported_and_fully_qualified_symbols' => [
                '<?php
use Foo\Bar;
use Foo\Baz;
use Foo\Qux;

/**
 */
function foo(?Bar $bar, ?\Foo\Baz $baz): ?\Foo\Qux {}',
                '<?php
use Foo\Bar;
use Foo\Baz;
use Foo\Qux;

/**
 * @param \Foo\Bar|null $bar
 * @param Baz|null $baz
 * @return Qux|null
 */
function foo(?Bar $bar, ?\Foo\Baz $baz): ?\Foo\Qux {}',
            ],
            'with_aliased_imported' => [
                '<?php
use Foo\Bar as Baz;

/**
 */
function foo(?Baz $bar): ?Baz {}',
                '<?php
use Foo\Bar as Baz;

/**
 * @param \Foo\Bar|null $bar
 * @return \Foo\Bar|null
 */
function foo(?Baz $bar): ?Baz {}',
            ],
            'with_special_type_hints' => [
                '<?php
class Foo {
    /**
     *
     */
    public function doFoo(iterable $bar, ?int $baz): ?array {}
}',
                '<?php
class Foo {
    /**
     * @param iterable $bar
     * @param int|null $baz
     *
     * @return array|null
     */
    public function doFoo(iterable $bar, ?int $baz): ?array {}
}',
            ],
        ];
    }
}
