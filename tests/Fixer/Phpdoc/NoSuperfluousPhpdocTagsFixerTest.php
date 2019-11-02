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
    public function testFix($expected, $input = null, array $config = [])
    {
        $this->fixer->configure($config);
        $this->doTest($expected, $input);
    }

    public function provideFixCases()
    {
        return [
            'no typehint' => [
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
            'same typehint' => [
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
            'same optional typehint' => [
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
            'same typehint with description' => [
                '<?php
class Foo {
    /**
     * @param Bar $bar an instance of Bar
     */
    public function doFoo(Bar $bar) {}
}',
            ],
            'allow_mixed=>false' => [
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
                ['allow_mixed' => false],
            ],
            'allow_mixed=>true' => [
                '<?php
class Foo {
    /**
     *
     */
    public function doFoo($bar) {}
}',
                null,
                ['allow_mixed' => true],
            ],
            'allow_unused_params=>true' => [
                '<?php
class Foo {
    /**
     * @param string|int $c
     */
    public function doFoo($bar /*, $c = 0 */) {}
}',
                null,
                ['allow_unused_params' => true],
            ],
            'multiple different types' => [
                '<?php
class Foo {
    /**
     * @param SubclassOfBar1|SubclassOfBar2 $bar
     */
    public function doFoo(Bar $bar) {}
}',
            ],
            'same typehint with different casing' => [
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
            'multiple arguments' => [
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
            'with import' => [
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
            'with root symbols' => [
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
            'with mix of imported and fully qualified symbols' => [
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
            'with aliased imported' => [
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
            'with unmapped param' => [
                '<?php
use Foo\Bar;

/**
 * @param Bar
 */
function foo(Bar $bar) {}',
            ],
            'with param superfluous but not return' => [
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
            'with not all params superfluous' => [
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
            'with special type hints' => [
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
            'PHPDoc at the end of file' => [
                '<?php
/**
 * Foo
 */',
            ],
            'with_variable_in_description' => [
                '<?php
class Foo {
    /**
     * @param $foo Some description that includes a $variable
     */
    public function doFoo($foo) {}
}',
            ],
            'with_null' => [
                '<?php
class Foo {
    /**
     * @param null $foo
     * @return null
     */
    public function doFoo($foo) {}
}',
            ],
            'inheritdoc' => [
                '<?php
class Foo {
    /**
     * @inheritDoc
     */
    public function doFoo($foo) {}
}',
            ],
            'inline_inheritdoc' => [
                '<?php
class Foo {
    /**
     * {@inheritdoc}
     */
    public function doFoo($foo) {}
}',
            ],
            'dont_remove_inheritdoc' => [
                '<?php
class Foo {
    /**
     * @inheritDoc
     */
    public function doFoo($foo) {}
}',
                null,
                ['remove_inheritdoc' => false],
            ],
            'dont_remove_inline_inheritdoc' => [
                '<?php
class Foo {
    /**
     * {@inheritdoc}
     */
    public function doFoo($foo) {}
}',
                null,
                ['remove_inheritdoc' => false],
            ],
            'remove_inheritdoc' => [
                '<?php
class Foo {
    /**
     *
     */
    public function doFoo($foo) {}
}',
                '<?php
class Foo {
    /**
     * @inheritDoc
     */
    public function doFoo($foo) {}
}',
                ['remove_inheritdoc' => true],
            ],
            'remove_inline_inheritdoc' => [
                '<?php
class Foo {
    /**
     *
     */
    public function doFoo($foo) {}
}',
                '<?php
class Foo {
    /**
     * {@inheritdoc}
     */
    public function doFoo($foo) {}
}',
                ['remove_inheritdoc' => true],
            ],
            'dont_remove_inheritdoc_when_surrounded_by_text' => [
                '<?php
class Foo {
    /**
     * Foo.
     *
     * @inheritDoc
     *
     * Bar.
     */
    public function doFoo($foo) {}
}',
                null,
                ['remove_inheritdoc' => true],
            ],
            'dont_remove_inheritdoc_when_preceded_by_text' => [
                '<?php
class Foo {
    /**
     * Foo.
     *
     * @inheritDoc
     */
    public function doFoo($foo) {}
}',
                null,
                ['remove_inheritdoc' => true],
            ],
            'dont_remove_inheritdoc_when_followed_by_text' => [
                '<?php
class Foo {
    /**
     * @inheritDoc
     *
     * Bar.
     */
    public function doFoo($foo) {}
}',
                null,
                ['remove_inheritdoc' => true],
            ],
            'dont_remove_inline_inheritdoc_inside_text' => [
                '<?php
class Foo {
    /**
     * Foo {@inheritDoc} Bar.
     */
    public function doFoo($foo) {}
}',
                null,
                ['remove_inheritdoc' => true],
            ],
            'inheritdocs' => [
                '<?php
class Foo {
    /**
     * @inheritDocs
     */
    public function doFoo($foo) {}
}',
            ],
            'inline_inheritdocs' => [
                '<?php
class Foo {
    /**
     * {@inheritdocs}
     */
    public function doFoo($foo) {}
}',
            ],
            'dont_remove_inheritdocs' => [
                '<?php
class Foo {
    /**
     * @inheritDocs
     */
    public function doFoo($foo) {}
}',
                null,
                ['remove_inheritdoc' => false],
            ],
            'dont_remove_inline_inheritdocs' => [
                '<?php
class Foo {
    /**
     * {@inheritdocs}
     */
    public function doFoo($foo) {}
}',
                null,
                ['remove_inheritdoc' => false],
            ],
            'remove_inheritdocs' => [
                '<?php
class Foo {
    /**
     *
     */
    public function doFoo($foo) {}
}',
                '<?php
class Foo {
    /**
     * @inheritDocs
     */
    public function doFoo($foo) {}
}',
                ['remove_inheritdoc' => true],
            ],
            'remove_inline_inheritdocs' => [
                '<?php
class Foo {
    /**
     *
     */
    public function doFoo($foo) {}
}',
                '<?php
class Foo {
    /**
     * {@inheritdocs}
     */
    public function doFoo($foo) {}
}',
                ['remove_inheritdoc' => true],
            ],
            'dont_remove_inheritdocs_when_surrounded_by_text' => [
                '<?php
class Foo {
    /**
     * Foo.
     *
     * @inheritDocs
     *
     * Bar.
     */
    public function doFoo($foo) {}
}',
                null,
                ['remove_inheritdoc' => true],
            ],
            'dont_remove_inheritdocs_when_preceded_by_text' => [
                '<?php
class Foo {
    /**
     * Foo.
     *
     * @inheritDocs
     */
    public function doFoo($foo) {}
}',
                null,
                ['remove_inheritdoc' => true],
            ],
            'dont_remove_inheritdocs_when_followed_by_text' => [
                '<?php
class Foo {
    /**
     * @inheritDocs
     *
     * Bar.
     */
    public function doFoo($foo) {}
}',
                null,
                ['remove_inheritdoc' => true],
            ],
            'dont_remove_inline_inheritdocs_inside_text' => [
                '<?php
class Foo {
    /**
     * Foo {@inheritDocs} Bar.
     */
    public function doFoo($foo) {}
}',
                null,
                ['remove_inheritdoc' => true],
            ],
            'property_inheritdoc' => [
                '<?php
class Foo {
    /**
     * @inheritDoc
     */
    private $foo;
}',
            ],
            'inline_property_inheritdoc' => [
                '<?php
class Foo {
    /**
     * {@inheritdoc}
     */
    private $foo;
}',
            ],
            'dont_remove_property_inheritdoc' => [
                '<?php
class Foo {
    /**
     * @inheritDoc
     */
    private $foo;
}',
                null,
                ['remove_inheritdoc' => false],
            ],
            'dont_remove_property_inline_inheritdoc' => [
                '<?php
class Foo {
    /**
     * {@inheritdoc}
     */
    private $foo;
}',
                null,
                ['remove_inheritdoc' => false],
            ],
            'remove_property_inheritdoc' => [
                '<?php
class Foo {
    /**
     *
     */
    private $foo;
}',
                '<?php
class Foo {
    /**
     * @inheritDoc
     */
    private $foo;
}',
                ['remove_inheritdoc' => true],
            ],
            'remove_inline_property_inheritdoc' => [
                '<?php
class Foo {
    /**
     *
     */
    private $foo;
}',
                '<?php
class Foo {
    /**
     * {@inheritdoc}
     */
    private $foo;
}',
                ['remove_inheritdoc' => true],
            ],
            'dont_remove_property_inheritdoc_when_surrounded_by_text' => [
                '<?php
class Foo {
    /**
     * Foo.
     *
     * @inheritDoc
     *
     * Bar.
     */
    private $foo;
}',
                null,
                ['remove_inheritdoc' => true],
            ],
            'dont_remove_property_inheritdoc_when_preceded_by_text' => [
                '<?php
class Foo {
    /**
     * Foo.
     *
     * @inheritDoc
     */
    private $foo;
}',
                null,
                ['remove_inheritdoc' => true],
            ],
            'dont_remove_property_inheritdoc_when_followed_by_text' => [
                '<?php
class Foo {
    /**
     * @inheritDoc
     *
     * Bar.
     */
    private $foo;
}',
                null,
                ['remove_inheritdoc' => true],
            ],
            'dont_remove_property_inline_inheritdoc_inside_text' => [
                '<?php
class Foo {
    /**
     * Foo {@inheritDoc} Bar.
     */
    private $foo;
}',
                null,
                ['remove_inheritdoc' => true],
            ],
            'property_inheritdocs' => [
                '<?php
class Foo {
    /**
     * @inheritDocs
     */
    private $foo;
}',
            ],
            'inline_property_inheritdocs' => [
                '<?php
class Foo {
    /**
     * {@inheritdocs}
     */
    private $foo;
}',
            ],
            'dont_remove_property_inheritdocs' => [
                '<?php
class Foo {
    /**
     * @inheritDocs
     */
    private $foo;
}',
                null,
                ['remove_inheritdoc' => false],
            ],
            'dont_remove_inline_property_inheritdocs' => [
                '<?php
class Foo {
    /**
     * {@inheritdocs}
     */
    private $foo;
}',
                null,
                ['remove_inheritdoc' => false],
            ],
            'remove_property_property_inheritdoc' => [
                '<?php
class Foo {
    /**
     *
     */
    private $foo;
}',
                '<?php
class Foo {
    /**
     * @inheritDocs
     */
    private $foo;
}',
                ['remove_inheritdoc' => true],
            ],
            'remove_inline_property_inheritdocs' => [
                '<?php
class Foo {
    /**
     *
     */
    private $foo;
}',
                '<?php
class Foo {
    /**
     * {@inheritdocs}
     */
    private $foo;
}',
                ['remove_inheritdoc' => true],
            ],
            'dont_remove_property_inheritdocs_when_surrounded_by_text' => [
                '<?php
class Foo {
    /**
     * Foo.
     *
     * @inheritDocs
     *
     * Bar.
     */
    private $foo;
}',
                null,
                ['remove_inheritdoc' => true],
            ],
            'dont_remove_property_inheritdocs_when_preceded_by_text' => [
                '<?php
class Foo {
    /**
     * Foo.
     *
     * @inheritDocs
     */
    private $foo;
}',
                null,
                ['remove_inheritdoc' => true],
            ],
            'dont_remove_property_inheritdocs_when_followed_by_text' => [
                '<?php
class Foo {
    /**
     * @inheritDocs
     *
     * Bar.
     */
    private $foo;
}',
                null,
                ['remove_inheritdoc' => true],
            ],
            'dont_remove_inline_property_inheritdocs_inside_text' => [
                '<?php
class Foo {
    /**
     * Foo {@inheritDocs} Bar.
     */
    private $foo;
}',
                null,
                ['remove_inheritdoc' => true],
            ],
            'class_inheritdoc' => [
                '<?php
/**
 * @inheritDoc
 */
class Foo {}',
            ],
            'dont_remove_class_inheritdoc' => [
                '<?php
/**
 * @inheritDoc
 */
class Foo {}',
                null,
                ['remove_inheritdoc' => false],
            ],
            'remove_class_inheritdoc' => [
                '<?php
/**
 *
 */
class Foo {}',
                '<?php
/**
 * @inheritDoc
 */
class Foo {}',
                ['remove_inheritdoc' => true],
            ],
            'remove_interface_inheritdoc' => [
                '<?php
/**
 *
 */
interface Foo {}',
                '<?php
/**
 * @inheritDoc
 */
interface Foo {}',
                ['remove_inheritdoc' => true],
            ],
            'dont_remove_class_inheritdoc_when_surrounded_by_text' => [
                '<?php
/**
 * Foo.
 *
 * @inheritDoc
 *
 * Bar.
 */
class Foo {}',
                null,
                ['remove_inheritdoc' => true],
            ],
            'dont_remove_class_inheritdoc_when_preceded_by_text' => [
                '<?php
/**
 * Foo.
 *
 * @inheritDoc
 */
class Foo {}',
                null,
                ['remove_inheritdoc' => true],
            ],
            'dont_remove_class_inheritdoc_when_followed_by_text' => [
                '<?php
/**
 * @inheritDoc
 *
 * Bar.
 */
class Foo {}',
                null,
                ['remove_inheritdoc' => true],
            ],
            'remove_inheritdoc_after_other_tag' => [
                '<?php
class Foo {
    /**
     * @param int $foo an integer
     *
     *
     */
    public function doFoo($foo) {}
}',
                '<?php
class Foo {
    /**
     * @param int $foo an integer
     *
     * @inheritDoc
     */
    public function doFoo($foo) {}
}',
                ['remove_inheritdoc' => true],
            ],
            'remove_only_inheritdoc_line' => [
                '<?php
class Foo {
    /**
     *
     *
     *
     *
     *
     *
     *
     *
     *
     *
     *
     *
     *
     */
    public function doFoo($foo) {}
}',
                '<?php
class Foo {
    /**
     *
     *
     *
     *
     *
     *
     * @inheritDoc
     *
     *
     *
     *
     *
     *
     */
    public function doFoo($foo) {}
}',
                ['remove_inheritdoc' => true],
            ],
            'remove_single_line_inheritdoc' => [
                '<?php
class Foo {
    /** */
    public function doFoo($foo) {}
}',
                '<?php
class Foo {
    /** @inheritDoc */
    public function doFoo($foo) {}
}',
                ['remove_inheritdoc' => true],
            ],
            'remove_inheritdoc_on_first_line' => [
                '<?php
class Foo {
    /**
     */
    public function doFoo($foo) {}
}',
                '<?php
class Foo {
    /** @inheritDoc
     */
    public function doFoo($foo) {}
}',
                ['remove_inheritdoc' => true],
            ],
            'remove_inheritdoc_on_last_line' => [
                '<?php
class Foo {
    /**
     * */
    public function doFoo($foo) {}
}',
                '<?php
class Foo {
    /**
     * @inheritDoc */
    public function doFoo($foo) {}
}',
                ['remove_inheritdoc' => true],
            ],
            'dont_remove_inheritdoc_non_structural_element' => [
                '<?php
/**
 * @inheritDoc
 */
$foo = 1;',
                null,
                ['remove_inheritdoc' => true],
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
            'same type hint' => [
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
            'same type hint with description' => [
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
            'multiple different types' => [
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
            'with import' => [
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
            'with root symbols' => [
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
            'with mix of imported and fully qualified symbols' => [
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
            'with aliased imported' => [
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
            'with scalar type hints' => [
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
            'same nullable type hint' => [
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
            'same nullable type hint reversed' => [
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
     * @param null|Bar $bar
     *
     * @return null|Baz
     */
    public function doFoo(?Bar $bar): ?Baz {}
}',
            ],
            'same nullable type hint with description' => [
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
            'same optional nullable type hint' => [
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
            'multiple different types' => [
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
            'with import' => [
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
            'with root symbols' => [
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
            'with mix of imported and fully qualified symbols' => [
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
            'with aliased imported' => [
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
            'with special type hints' => [
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
