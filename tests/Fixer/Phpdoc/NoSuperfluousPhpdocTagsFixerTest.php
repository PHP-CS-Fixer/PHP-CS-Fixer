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

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Phpdoc\NoSuperfluousPhpdocTagsFixer
 */
final class NoSuperfluousPhpdocTagsFixerTest extends AbstractFixerTestCase
{
    /**
     * @param array<string, mixed> $config
     *
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null, array $config = []): void
    {
        $this->fixer->configure($config);
        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): iterable
    {
        yield 'no type declaration' => [
            '<?php
class Foo {
    /**
     * @param Bar $bar
     *
     * @return Baz
     */
    public function doFoo($bar) {}
}',
        ];

        yield 'same type declaration' => [
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
        ];

        yield 'same optional type declaration' => [
            '<?php
class Foo {
    /**
     */
    public function doFoo(Bar $bar = NULL) {}
}',
            '<?php
class Foo {
    /**
     * @param Bar|null $bar
     */
    public function doFoo(Bar $bar = NULL) {}
}',
        ];

        yield 'same type declaration with description' => [
            '<?php
class Foo {
    /**
     * @param Bar $bar an instance of Bar
     */
    public function doFoo(Bar $bar) {}
}',
        ];

        yield 'allow_mixed=>false' => [
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
        ];

        yield 'allow_mixed=>true' => [
            '<?php
class Foo {
    /**
     *
     */
    public function doFoo($bar) {}
}',
            null,
            ['allow_mixed' => true],
        ];

        yield 'allow_mixed=>false on property' => [
            '<?php
class Foo {
    /**
     */
    private $bar;
}',
            '<?php
class Foo {
    /**
     * @var mixed
     */
    private $bar;
}',
            ['allow_mixed' => false],
        ];

        yield 'allow_mixed=>false on property with var' => [
            '<?php
class Foo {
    /**
     */
    private $bar;
}',
            '<?php
class Foo {
    /**
     * @var mixed $bar
     */
    private $bar;
}',
            ['allow_mixed' => false],
        ];

        yield 'allow_mixed=>false on property but with comment' => [
            '<?php
class Foo {
    /**
     * @var mixed comment
     */
    private $bar;
}',
            null,
            ['allow_mixed' => false],
        ];

        yield 'allow_unused_params=>true' => [
            '<?php
class Foo {
    /**
     * @param string|int $c
     */
    public function doFoo($bar /*, $c = 0 */) {}
}',
            null,
            ['allow_unused_params' => true],
        ];

        yield 'multiple different types' => [
            '<?php
class Foo {
    /**
     * @param SubclassOfBar1|SubclassOfBar2 $bar
     */
    public function doFoo(Bar $bar) {}
}',
        ];

        yield 'same type declaration with different casing' => [
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
        ];

        yield 'same type declaration with leading backslash - global' => [
            '<?php
class Foo {
    /**
     */
    public function doFoo(Bar $bar) {}
}',
            '<?php
class Foo {
    /**
     * @param \Bar $bar
     */
    public function doFoo(Bar $bar) {}
}',
        ];

        yield 'same type declaration with leading backslash - namespaced' => [
            '<?php
namespace Xxx;

class Foo {
    /**
     */
    public function doFoo(Model\Invoice $bar) {}
}',
            '<?php
namespace Xxx;

class Foo {
    /**
     * @param \Xxx\Model\Invoice $bar
     */
    public function doFoo(Model\Invoice $bar) {}
}',
        ];

        yield 'same type declaration without leading backslash - global' => [
            '<?php
class Foo {
    /**
     */
    public function doFoo(\Bar $bar) {}
}',
            '<?php
class Foo {
    /**
     * @param Bar $bar
     */
    public function doFoo(\Bar $bar) {}
}',
        ];

        yield 'same type declaration without leading backslash - namespaced' => [
            '<?php
namespace Xxx;

class Foo {
    /**
     */
    public function doFoo(\Xxx\Bar $bar) {}
}',
            '<?php
namespace Xxx;

class Foo {
    /**
     * @param Bar $bar
     */
    public function doFoo(\Xxx\Bar $bar) {}
}',
        ];

        yield 'same type declaration with null implied from native type - param type' => [
            '<?php
class Foo {
    /**
     */
    public function setAttribute(?string $value, string $value2 = null): void
    {
    }
}',
            '<?php
class Foo {
    /**
     * @param string $value
     * @param string $value2
     */
    public function setAttribute(?string $value, string $value2 = null): void
    {
    }
}',
        ];

        yield 'same type declaration with null implied from native type - return type' => [
            '<?php
class Foo {
    /**
     */
    public function getX(): ?X
    {
    }
}',
            '<?php
class Foo {
    /**
     * @return X
     */
    public function getX(): ?X
    {
    }
}',
        ];

        yield 'same type declaration with null implied from native type - property' => [
            '<?php
class Foo {
    /**  */
    public ?bool $enabled;
}',
            '<?php
class Foo {
    /** @var bool */
    public ?bool $enabled;
}',
        ];

        yield 'same type declaration with null but native type without null - invalid phpdoc must be kept unfixed' => [
            '<?php
class Foo {
    /** @var bool|null */
    public bool $enabled;
}',
        ];

        yield 'multiple arguments' => [
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
        ];

        yield 'with import' => [
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
        ];

        yield 'with root symbols' => [
            '<?php
/**
 */
function foo(\Foo\Bar $bar) {}',
            '<?php
/**
 * @param \Foo\Bar $bar
 */
function foo(\Foo\Bar $bar) {}',
        ];

        yield 'with mix of imported and fully qualified symbols' => [
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
        ];

        yield 'with aliased import' => [
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
        ];

        yield 'with unmapped param' => [
            '<?php
use Foo\Bar;

/**
 * @param Bar
 */
function foo(Bar $bar) {}',
        ];

        yield 'with param superfluous but not return' => [
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
        ];

        yield 'with not all params superfluous' => [
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
        ];

        yield 'with special type declarations' => [
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
        ];

        yield 'PHPDoc at the end of file' => [
            '<?php
/**
 * Foo
 */',
        ];

        yield 'with_variable_in_description' => [
            '<?php
class Foo {
    /**
     * @param $foo Some description that includes a $variable
     */
    public function doFoo($foo) {}
}',
        ];

        yield 'with_null' => [
            '<?php
class Foo {
    /**
     * @param null $foo
     * @return null
     */
    public function doFoo($foo) {}
}',
        ];

        yield 'inheritdoc' => [
            '<?php
class Foo {
    /**
     * @inheritDoc
     */
    public function doFoo($foo) {}
}',
        ];

        yield 'inline_inheritdoc' => [
            '<?php
class Foo {
    /**
     * {@inheritdoc}
     */
    public function doFoo($foo) {}
}',
        ];

        yield 'dont_remove_inheritdoc' => [
            '<?php
class Foo {
    /**
     * @inheritDoc
     */
    public function doFoo($foo) {}
}',
            null,
            ['remove_inheritdoc' => false],
        ];

        yield 'dont_remove_inline_inheritdoc' => [
            '<?php
class Foo {
    /**
     * {@inheritdoc}
     */
    public function doFoo($foo) {}
}',
            null,
            ['remove_inheritdoc' => false],
        ];

        yield 'remove_inheritdoc' => [
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
        ];

        yield 'remove_inline_inheritdoc' => [
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
        ];

        yield 'dont_remove_inheritdoc_when_surrounded_by_text' => [
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
        ];

        yield 'dont_remove_inheritdoc_when_preceded_by_text' => [
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
        ];

        yield 'dont_remove_inheritdoc_when_followed_by_text' => [
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
        ];

        yield 'dont_remove_inline_inheritdoc_inside_text' => [
            '<?php
class Foo {
    /**
     * Foo {@inheritDoc} Bar.
     */
    public function doFoo($foo) {}
}',
            null,
            ['remove_inheritdoc' => true],
        ];

        yield 'inheritdocs' => [
            '<?php
class Foo {
    /**
     * @inheritDocs
     */
    public function doFoo($foo) {}
}',
        ];

        yield 'inline_inheritdocs' => [
            '<?php
class Foo {
    /**
     * {@inheritdocs}
     */
    public function doFoo($foo) {}
}',
        ];

        yield 'dont_remove_inheritdocs' => [
            '<?php
class Foo {
    /**
     * @inheritDocs
     */
    public function doFoo($foo) {}
}',
            null,
            ['remove_inheritdoc' => false],
        ];

        yield 'dont_remove_inline_inheritdocs' => [
            '<?php
class Foo {
    /**
     * {@inheritdocs}
     */
    public function doFoo($foo) {}
}',
            null,
            ['remove_inheritdoc' => false],
        ];

        yield 'remove_inheritdocs' => [
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
        ];

        yield 'remove_inline_inheritdocs' => [
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
        ];

        yield 'dont_remove_inheritdocs_when_surrounded_by_text' => [
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
        ];

        yield 'dont_remove_inheritdocs_when_preceded_by_text' => [
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
        ];

        yield 'dont_remove_inheritdocs_when_followed_by_text' => [
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
        ];

        yield 'dont_remove_inline_inheritdocs_inside_text' => [
            '<?php
class Foo {
    /**
     * Foo {@inheritDocs} Bar.
     */
    public function doFoo($foo) {}
}',
            null,
            ['remove_inheritdoc' => true],
        ];

        yield 'property_inheritdoc' => [
            '<?php
class Foo {
    /**
     * @inheritDoc
     */
    private $foo;
}',
        ];

        yield 'inline_property_inheritdoc' => [
            '<?php
class Foo {
    /**
     * {@inheritdoc}
     */
    private $foo;
}',
        ];

        yield 'dont_remove_property_inheritdoc' => [
            '<?php
class Foo {
    /**
     * @inheritDoc
     */
    private $foo;
}',
            null,
            ['remove_inheritdoc' => false],
        ];

        yield 'dont_remove_property_inline_inheritdoc' => [
            '<?php
class Foo {
    /**
     * {@inheritdoc}
     */
    private $foo;
}',
            null,
            ['remove_inheritdoc' => false],
        ];

        yield 'remove_property_inheritdoc' => [
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
        ];

        yield 'remove_inline_property_inheritdoc' => [
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
        ];

        yield 'dont_remove_property_inheritdoc_when_surrounded_by_text' => [
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
        ];

        yield 'dont_remove_property_inheritdoc_when_preceded_by_text' => [
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
        ];

        yield 'dont_remove_property_inheritdoc_when_followed_by_text' => [
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
        ];

        yield 'dont_remove_property_inline_inheritdoc_inside_text' => [
            '<?php
class Foo {
    /**
     * Foo {@inheritDoc} Bar.
     */
    private $foo;
}',
            null,
            ['remove_inheritdoc' => true],
        ];

        yield 'property_inheritdocs' => [
            '<?php
class Foo {
    /**
     * @inheritDocs
     */
    private $foo;
}',
        ];

        yield 'inline_property_inheritdocs' => [
            '<?php
class Foo {
    /**
     * {@inheritdocs}
     */
    private $foo;
}',
        ];

        yield 'dont_remove_property_inheritdocs' => [
            '<?php
class Foo {
    /**
     * @inheritDocs
     */
    private $foo;
}',
            null,
            ['remove_inheritdoc' => false],
        ];

        yield 'dont_remove_inline_property_inheritdocs' => [
            '<?php
class Foo {
    /**
     * {@inheritdocs}
     */
    private $foo;
}',
            null,
            ['remove_inheritdoc' => false],
        ];

        yield 'remove_property_property_inheritdoc' => [
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
        ];

        yield 'remove_inline_property_inheritdocs' => [
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
        ];

        yield 'dont_remove_property_inheritdocs_when_surrounded_by_text' => [
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
        ];

        yield 'dont_remove_property_inheritdocs_when_preceded_by_text' => [
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
        ];

        yield 'dont_remove_property_inheritdocs_when_followed_by_text' => [
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
        ];

        yield 'dont_remove_inline_property_inheritdocs_inside_text' => [
            '<?php
class Foo {
    /**
     * Foo {@inheritDocs} Bar.
     */
    private $foo;
}',
            null,
            ['remove_inheritdoc' => true],
        ];

        yield 'class_inheritdoc' => [
            '<?php
/**
 * @inheritDoc
 */
class Foo {}',
        ];

        yield 'dont_remove_class_inheritdoc' => [
            '<?php
/**
 * @inheritDoc
 */
class Foo {}',
            null,
            ['remove_inheritdoc' => false],
        ];

        yield 'remove_class_inheritdoc' => [
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
        ];

        yield 'remove_interface_inheritdoc' => [
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
        ];

        yield 'dont_remove_class_inheritdoc_when_surrounded_by_text' => [
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
        ];

        yield 'dont_remove_class_inheritdoc_when_preceded_by_text' => [
            '<?php
/**
 * Foo.
 *
 * @inheritDoc
 */
class Foo {}',
            null,
            ['remove_inheritdoc' => true],
        ];

        yield 'dont_remove_class_inheritdoc_when_followed_by_text' => [
            '<?php
/**
 * @inheritDoc
 *
 * Bar.
 */
class Foo {}',
            null,
            ['remove_inheritdoc' => true],
        ];

        yield 'remove_inheritdoc_after_other_tag' => [
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
        ];

        yield 'remove_only_inheritdoc_line' => [
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
        ];

        yield 'remove_single_line_inheritdoc' => [
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
        ];

        yield 'remove_inheritdoc_on_first_line' => [
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
        ];

        yield 'remove_inheritdoc_on_last_line' => [
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
        ];

        yield 'remove_inheritdoc_non_structural_element_it_does_not_inherit' => [
            '<?php
/**
 *
 */
$foo = 1;',
            '<?php
/**
 * @inheritDoc
 */
$foo = 1;',
            ['remove_inheritdoc' => true],
        ];

        yield 'property with unsupported type' => [
            '<?php
class Foo {
    /**
     * @var foo:bar
     */
    private $foo;
}',
        ];

        yield 'method with unsupported types' => [
            '<?php
class Foo {
    /**
     * @param foo:bar $foo
     * @return foo:bar
     */
    public function foo($foo) {}
}',
        ];

        yield 'with constant values as type' => [
            '<?php
class Foo {
    /**
     * @var Bar::A|Bar::B|Baz::*|null
     */
    private $foo;

    /**
     * @var 1|\'a\'|\'b\'
     */
    private $bar;
}',
        ];

        yield 'same type declaration (with extra empty line)' => [
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
        ];

        yield 'same type declaration (with return type) with description' => [
            '<?php
class Foo {
    /**
     * @param Bar $bar an instance of Bar
     *
     * @return Baz an instance of Baz
     */
    public function doFoo(Bar $bar): Baz {}
}',
        ];

        yield 'multiple different types (with return type)' => [
            '<?php
class Foo {
    /**
     * @param SubclassOfBar1|SubclassOfBar2 $bar
     *
     * @return SubclassOfBaz1|SubclassOfBaz2 $bar
     */
    public function doFoo(Bar $bar): Baz {}
}',
        ];

        yield 'with import (with return type)' => [
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
        ];

        yield 'with root symbols (with return type)' => [
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
        ];

        yield 'with mix of imported and fully qualified symbols (with return type)' => [
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
        ];

        yield 'with aliased import (with return type)' => [
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
        ];

        yield 'with scalar type declarations' => [
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
        ];

        yield 'really long one' => [
            '<?php
                    /**
                     * "Sponsored" by https://github.com/PrestaShop/PrestaShop/blob/1.6.1.24/tools/tcpdf/tcpdf.php (search for "Get page dimensions from format name")
                     * @see
                     * @param $number - it can be:
                     * '.implode("\n                     * ", range(1, 1000)).'
                     */
                     function display($number) {}
                ',
        ];

        yield 'return with @inheritDoc in description' => [
            '<?php
                    /**
                     */
                    function foo(): bool {}
                ',
            '<?php
                    /**
                     * @return bool @inheritDoc
                     */
                    function foo(): bool {}
                ',
            ['remove_inheritdoc' => true],
        ];

        yield 'remove_trait_inheritdoc' => [
            '<?php
/**
 *
 */
trait Foo {}',
            '<?php
/**
 * @inheritDoc
 */
trait Foo {}',
            ['remove_inheritdoc' => true],
        ];

        yield 'same nullable type declaration' => [
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
        ];

        yield 'same nullable type declaration reversed' => [
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
        ];

        yield 'same nullable type declaration with description' => [
            '<?php
class Foo {
    /**
     * @param Bar|null $bar an instance of Bar
     *
     * @return Baz|null an instance of Baz
     */
    public function doFoo(?Bar $bar): ?Baz {}
}',
        ];

        yield 'same optional nullable type declaration' => [
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
        ];

        yield 'multiple different types (nullable)' => [
            '<?php
class Foo {
    /**
     * @param SubclassOfBar1|SubclassOfBar2|null $bar
     *
     * @return SubclassOfBaz1|SubclassOfBaz2|null $bar
     */
    public function doFoo(?Bar $bar): ?Baz {}
}',
        ];

        yield 'with nullable import' => [
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
        ];

        yield 'with nullable root symbols' => [
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
        ];

        yield 'with nullable mix of imported and fully qualified symbols' => [
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
        ];

        yield 'with nullable aliased import' => [
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
        ];

        yield 'with nullable special type declarations' => [
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
        ];

        yield 'remove abstract annotation in function' => [
            '<?php
abstract class Foo {
    /**
     */
    public abstract function doFoo();
}',
            '<?php
abstract class Foo {
    /**
     * @abstract
     */
    public abstract function doFoo();
}', ];

        yield 'dont remove abstract annotation in function' => [
            '<?php
class Foo {
    /**
     * @abstract
     */
    public function doFoo() {}
}', ];

        yield 'remove final annotation in function' => [
            '<?php
class Foo {
    /**
     */
    public final function doFoo() {}
}',
            '<?php
class Foo {
    /**
     * @final
     */
    public final function doFoo() {}
}', ];

        yield 'dont remove final annotation in function' => [
            '<?php
class Foo {
    /**
     * @final
     */
    public function doFoo() {}
}', ];

        yield 'remove abstract annotation in class' => [
            '<?php
/**
 */
abstract class Foo {
}',
            '<?php
/**
 * @abstract
 */
abstract class Foo {
}', ];

        yield 'dont remove abstract annotation in class' => [
            '<?php
abstract class Bar{}

/**
 * @abstract
 */
class Foo {
}', ];

        yield 'remove final annotation in class' => [
            '<?php
/**
 */
final class Foo {
}',
            '<?php
/**
 * @final
 */
final class Foo {
}', ];

        yield 'dont remove final annotation in class' => [
            '<?php
final class Bar{}

/**
 * @final
 */
class Foo {
}', ];

        yield 'remove when used with reference' => [
            '<?php class Foo {
                    /**
                     */
                     function f1(string &$x) {}
                    /**
                     */
                     function f2(string &$x) {}
                    /**
                     */
                     function f3(string &$x) {}
                }',
            '<?php class Foo {
                    /**
                     * @param string $x
                     */
                     function f1(string &$x) {}
                    /**
                     * @param string &$x
                     */
                     function f2(string &$x) {}
                    /**
                     * @param string $y Description
                     */
                     function f3(string &$x) {}
                }',
        ];

        yield 'dont remove when used with reference' => [
            '<?php class Foo {
                    /**
                     * @param string ...$x Description
                     */
                     function f(string ...$x) {}
                }',
        ];

        yield 'remove when used with splat operator' => [
            '<?php class Foo {
                    /**
                     */
                     function f1(string ...$x) {}
                    /**
                     */
                     function f2(string ...$x) {}
                }',
            '<?php class Foo {
                    /**
                     * @param string ...$x
                     */
                     function f1(string ...$x) {}
                    /**
                     * @param string ...$y Description
                     */
                     function f2(string ...$x) {}
                }',
        ];

        yield 'dont remove when used with splat operator' => [
            '<?php class Foo {
                    /**
                     * @param string ...$x Description
                     */
                     function f(string ...$x) {}
                }',
        ];

        yield 'remove when used with reference and splat operator' => [
            '<?php class Foo {
                    /**
                     */
                     function f1(string &...$x) {}
                    /**
                     */
                     function f2(string &...$x) {}
                    /**
                     */
                     function f3(string &...$x) {}
                }',
            '<?php class Foo {
                    /**
                     * @param string ...$x
                     */
                     function f1(string &...$x) {}
                    /**
                     * @param string &...$x
                     */
                     function f2(string &...$x) {}
                    /**
                     * @param string ...$y Description
                     */
                     function f3(string &...$x) {}
                }',
        ];

        yield 'dont remove when used with reference and splat operator' => [
            '<?php class Foo {
                    /**
                     * @param string &...$x Description
                     */
                     function f(string &...$x) {}
                }',
        ];

        yield 'some typed static public property' => [
            '<?php
class Foo {
    /**
     */
    static public Bar $bar;
}',
            '<?php
class Foo {
    /**
     * @var Bar
     */
    static public Bar $bar;
}',
        ];

        yield 'some typed public static property' => [
            '<?php
class Foo {
    /**
     */
    public static Bar $bar;
}',
            '<?php
class Foo {
    /**
     * @var Bar
     */
    public static Bar $bar;
}',
        ];

        yield 'some typed public property' => [
            '<?php
class Foo {
    /**
     */
    public Bar $bar;
}',
            '<?php
class Foo {
    /**
     * @var Bar
     */
    public Bar $bar;
}',
        ];

        yield 'some typed public property with single line PHPDoc' => [
            '<?php
class Foo {
    /**  */
    public Bar $bar;
}',
            '<?php
class Foo {
    /** @var Bar */
    public Bar $bar;
}',
        ];

        yield 'some typed public property with semi-single line PHPDoc' => [
            '<?php
class Foo {
    /**
     */
    public Bar $bar;

    /**
     */
    public Baz $baz;
}',
            '<?php
class Foo {
    /** @var Bar
     */
    public Bar $bar;

    /**
     * @var Baz */
    public Baz $baz;
}',
        ];

        yield 'some typed protected property' => [
            '<?php
class Foo {
    /**
     */
    protected Bar $bar;
}',
            '<?php
class Foo {
    /**
     * @var Bar
     */
    protected Bar $bar;
}',
        ];

        yield 'some typed private property' => [
            '<?php
class Foo {
    /**
     */
    private Bar $bar;
}',
            '<?php
class Foo {
    /**
     * @var Bar
     */
    private Bar $bar;
}',
        ];

        yield 'some typed nullable private property' => [
            '<?php
class Foo {
    /**
     */
    private ?Bar $bar;
}',
            '<?php
class Foo {
    /**
     * @var null|Bar
     */
    private ?Bar $bar;
}',
        ];

        yield 'some typed nullable property with name declared in phpdoc' => [
            '<?php
class Foo {
    /**
     */
    private ?Bar $bar;
}',
            '<?php
class Foo {
    /**
     * @var null|Bar $bar
     */
    private ?Bar $bar;
}',
        ];

        yield 'some array property' => [
            '<?php
class Foo {
    /**
     */
    private array $bar;
}',
            '<?php
class Foo {
    /**
     * @var array
     */
    private array $bar;
}',
        ];

        yield 'some nullable array property' => [
            '<?php
class Foo {
    /**
     */
    private ?array $bar;
}',
            '<?php
class Foo {
    /**
     * @var array|null
     */
    private ?array $bar;
}',
        ];

        yield 'some object property' => [
            '<?php
class Foo {
    /**
     */
    private object $bar;
}',
            '<?php
class Foo {
    /**
     * @var object
     */
    private object $bar;
}',
        ];

        yield 'phpdoc does not match property type declaration' => [
            '<?php
class Foo {
    /**
     * @var FooImplementation1|FooImplementation2
     */
    private FooInterface $bar;
}',
        ];

        yield 'allow_mixed=>false but with description' => [
            '<?php
class Foo {
    /**
     * @var mixed description
     */
    private $bar;
}',
            null,
            ['allow_mixed' => false],
        ];

        yield 'allow_mixed=>false but with description and var name' => [
            '<?php
class Foo {
    /**
     * @var mixed $bar description
     */
    private $bar;
}',
            null,
            ['allow_mixed' => false],
        ];

        yield 'allow_mixed=>true ||' => [
            '<?php
class Foo {
    /**
     * @var mixed
     */
    private $bar;
}',
            null,
            ['allow_mixed' => true],
        ];

        yield 'some fully qualified typed property' => [
            '<?php
class Foo {
    /**
     */
    protected \Foo\Bar $bar;
}',
            '<?php
class Foo {
    /**
     * @var \Foo\Bar
     */
    protected \Foo\Bar $bar;
}',
        ];

        yield 'some fully qualified imported typed property' => [
            '<?php
namespace App;
use Foo\Bar;
class Foo {
    /**
     */
    protected Bar $bar;
}',
            '<?php
namespace App;
use Foo\Bar;
class Foo {
    /**
     * @var \Foo\Bar
     */
    protected Bar $bar;
}',
        ];

        yield 'self as native type and interface name in phpdocs' => [
            '<?php
interface Foo {
    /**
     */
    public function bar(self $other): self;
}',
            '<?php
interface Foo {
    /**
     * @param Foo $other
     * @return Foo
     */
    public function bar(self $other): self;
}',
        ];

        yield 'interface name as native type and self in phpdocs' => [
            '<?php
interface Foo {
    /**
     */
    public function bar(Foo $other): Foo;
}',
            '<?php
interface Foo {
    /**
     * @param self $other
     * @return self
     */
    public function bar(Foo $other): Foo;
}',
        ];

        yield 'self as native type and class name in phpdocs' => [
            '<?php
class Foo {
    /**
     */
    public self $foo;

    /**
     */
    public function bar(self $other): self {}
}',
            '<?php
class Foo {
    /**
     * @var Foo
     */
    public self $foo;

    /**
     * @param Foo $other
     * @return Foo
     */
    public function bar(self $other): self {}
}',
        ];

        yield 'class name as native type and self in phpdocs' => [
            '<?php
class Foo {
    /**
     */
    public Foo $foo;

    /**
     */
    public function bar(Foo $other): Foo {}
}',
            '<?php
class Foo {
    /**
     * @var self
     */
    public Foo $foo;

    /**
     * @param self $other
     * @return self
     */
    public function bar(Foo $other): Foo {}
}',
        ];

        yield 'anonymous class' => [
            '<?php
new class() extends Foo {
    /**
     * @var Foo
     */
    public self $foo;

    /**
     * @param Foo $other
     * @return Foo
     */
    public function bar(self $other, int $superfluous): self {}
};',
            '<?php
new class() extends Foo {
    /**
     * @var Foo
     */
    public self $foo;

    /**
     * @param Foo $other
     * @param int $superfluous
     * @return Foo
     */
    public function bar(self $other, int $superfluous): self {}
};',
        ];

        yield 'remove empty var' => [
            '<?php
class Foo {
    /**
     */
    private $foo;
}',
            '<?php
class Foo {
    /**
     * @var
     */
    private $foo;
}',
        ];

        yield 'remove empty var single line' => [
            '<?php
class Foo {
    /**  */
    private $foo;
}',
            '<?php
class Foo {
    /** @var */
    private $foo;
}',
        ];

        yield 'dont remove var without a type but with a property name and a description' => [
            '<?php
class Foo {
    /**
     * @var $foo some description
     */
    private $foo;
}',
        ];

        yield 'dont remove single line var without a type but with a property name and a description' => [
            '<?php
class Foo {
    /** @var $foo some description */
    private $foo;
}',
        ];

        yield 'remove var without a type but with a property name' => [
            '<?php
class Foo {
    /**
     */
    private $foo;
}',
            '<?php
class Foo {
    /**
     * @var $foo
     */
    private $foo;
}',
        ];

        yield 'remove single line var without a type but with a property name' => [
            '<?php
class Foo {
    /**  */
    private $foo;
}',
            '<?php
class Foo {
    /** @var $foo */
    private $foo;
}',
        ];

        yield 'remove empty param' => [
            '<?php
class Foo {
    /**
     */
    public function foo($foo) {}
}',
            '<?php
class Foo {
    /**
     * @param
     */
    public function foo($foo) {}
}',
        ];

        yield 'remove empty single line param' => [
            '<?php
class Foo {
    /**  */
    public function foo($foo) {}
}',
            '<?php
class Foo {
    /** @param */
    public function foo($foo) {}
}',
        ];

        yield 'remove param without a type' => [
            '<?php
class Foo {
    /**
     */
    public function foo($foo) {}
}',
            '<?php
class Foo {
    /**
     * @param $foo
     */
    public function foo($foo) {}
}',
        ];

        yield 'remove single line param without a type' => [
            '<?php
class Foo {
    /**  */
    public function foo($foo) {}
}',
            '<?php
class Foo {
    /** @param $foo */
    public function foo($foo) {}
}',
        ];

        yield 'dont remove param without a type but with a description' => [
            '<?php
class Foo {
    /**
     * @param $foo description
     */
    public function foo($foo) {}
}',
        ];

        yield 'dont remove single line param without a type but with a description' => [
            '<?php
class Foo {
    /** @param $foo description */
    public function foo($foo) {}
}',
        ];

        yield 'remove empty return' => [
            '<?php
class Foo {
    /**
     */
    public function foo($foo) {}
}',
            '<?php
class Foo {
    /**
     * @return
     */
    public function foo($foo) {}
}',
        ];

        yield 'remove empty single line return' => [
            '<?php
class Foo {
    /**  */
    public function foo($foo) {}
}',
            '<?php
class Foo {
    /** @return */
    public function foo($foo) {}
}',
        ];

        yield 'explicit null must stay - global namespace' => [
            '<?php
class Foo {
    /** @return null */
    public function foo() {}
}',
        ];

        yield 'explicit null must stay - custom namespace' => [
            '<?php
namespace A\B;
class Foo {
    /** @return null */
    public function foo() {}
}',
        ];

        yield 'superfluous asterisk in corrupted phpDoc' => [
            '<?php
class Foo {
    /** * @return Baz */
    public function doFoo($bar) {}
}',
        ];

        yield 'superfluous return type after superfluous asterisk in corrupted phpDoc' => [
            '<?php
class Foo {
    /**  */
    public function doFoo($bar): Baz {}
}',
            '<?php
class Foo {
    /** * @return Baz */
    public function doFoo($bar): Baz {}
}',
        ];

        yield 'superfluous parameter type for anonymous function' => [
            '<?php
/**  */
function (int $foo) { return 1; };',
            '<?php
/** @param int $foo */
function (int $foo) { return 1; };',
        ];

        yield 'superfluous return type for anonymous function' => [
            '<?php
/**  */
function ($foo): int { return 1; };',
            '<?php
/** @return int */
function ($foo): int { return 1; };',
        ];

        yield 'superfluous parameter type for static anonymous function' => [
            '<?php
/**  */
static function (int $foo) { return 1; };',
            '<?php
/** @param int $foo */
static function (int $foo) { return 1; };',
        ];

        yield 'superfluous return type for static anonymous function' => [
            '<?php
/**  */
static function ($foo): int { return 1; };',
            '<?php
/** @return int */
static function ($foo): int { return 1; };',
        ];

        yield 'superfluous parameter type for arrow function' => [
            '<?php
/**  */
fn (int $foo) => 1;',
            '<?php
/** @param int $foo */
fn (int $foo) => 1;',
        ];

        yield 'superfluous return type for arrow function' => [
            '<?php
/**  */
fn ($foo): int => 1;',
            '<?php
/** @return int */
fn ($foo): int => 1;',
        ];

        yield 'superfluous parameter type for static arrow function' => [
            '<?php
/**  */
static fn (int $foo) => 1;',
            '<?php
/** @param int $foo */
static fn (int $foo) => 1;',
        ];

        yield 'superfluous return type for static arrow function' => [
            '<?php
/**  */
static fn ($foo): int => 1;',
            '<?php
/** @return int */
static fn ($foo): int => 1;',
        ];

        yield 'multiline @param must be kept even if there is no description on the phpdoc tag line' => [
            <<<'EOD'
                <?php
                /**
                 * @param string $arg
                 *                    - foo
                 *                    - foo2
                 */
                function foo(string $arg) {}
                EOD,
        ];

        yield 'multiline @return must be kept even if there is no description on the phpdoc tag line' => [
            <<<'EOD'
                <?php
                /**
                 * @return string
                 *                - foo
                 *                - foo2
                 */
                function foo(string $arg): string {}
                EOD,
        ];

        yield 'multiline @var must be kept even if there is no description on the phpdoc tag line' => [
            <<<'EOD'
                <?php
                class Cl {
                    /**
                     * @var string
                     *             - foo
                     *             - foo2
                     */
                    public string $prop;
                }
                EOD,
        ];
    }

    /**
     * @param array<string, mixed> $config
     *
     * @dataProvider provideFix80Cases
     *
     * @requires PHP 8.0
     */
    public function testFix80(string $expected, ?string $input = null, array $config = []): void
    {
        $this->fixer->configure($config);
        $this->doTest($expected, $input);
    }

    public static function provideFix80Cases(): iterable
    {
        yield 'static return' => [
            '<?php
class Foo {
    /**
     */
    public function foo($foo): static {}
}',
            '<?php
class Foo {
    /**
     * @return static
     */
    public function foo($foo): static {}
}',
        ];

        yield 'union type on parameter' => [
            '<?php
class Foo {
    /**
     */
    public function foo(int|string $foo) {}
}',
            '<?php
class Foo {
    /**
     * @param int|string $foo
     */
    public function foo(int|string $foo) {}
}',
        ];

        yield 'union type on return type' => [
            '<?php
class Foo {
    /**
     */
    public function foo($foo): int|string {}
}',
            '<?php
class Foo {
    /**
     * @return int|string
     */
    public function foo($foo): int|string {}
}',
        ];

        yield 'union type on property' => [
            '<?php
class Foo {
    /**
     */
    public int|string $foo;
}',
            '<?php
class Foo {
    /**
     * @var int|string
     */
    public int|string $foo;
}',
        ];

        yield 'union type on property with spaces' => [
            '<?php
class Foo {
    /**
     */
    public int  |  string $foo;
}',
            '<?php
class Foo {
    /**
     * @var int|string
     */
    public int  |  string $foo;
}',
        ];

        yield 'union type with null' => [
            '<?php
/**
 */
function foo(int|string|null $foo) {}',
            '<?php
/**
 * @param int|string|null $foo
 */
function foo(int|string|null $foo) {}',
        ];

        yield 'union type in different order' => [
            '<?php
/**
 */
function foo(string|int $foo) {}',
            '<?php
/**
 * @param int|string $foo
 */
function foo(string|int $foo) {}',
        ];

        yield 'more details in phpdocs' => [
            '<?php
/**
 * @param string|array<string> $foo
 */
function foo(string|array $foo) {}',
        ];

        yield 'missing types in phpdocs' => [
            '<?php
/**
 * @param string|int $foo
 */
function foo(string|array|int $foo) {}',
        ];

        yield 'too many types in phpdocs' => [
            '<?php
/**
 * @param string|array|int $foo
 */
function foo(string|int $foo) {}',
        ];

        yield 'promoted properties' => [
            '<?php class Foo {
                /**
                 */
                public function __construct(
                    public string $a,
                    protected ?string $b,
                    private ?string $c,
                ) {}
            }',
            '<?php class Foo {
                /**
                 * @param string $a
                 * @param null|string $b
                 * @param string|null $c
                 */
                public function __construct(
                    public string $a,
                    protected ?string $b,
                    private ?string $c,
                ) {}
            }',
        ];

        yield 'single attribute' => [
            '<?php
class Foo
{
    /**
     */
    #[MyAttribute]
    private int $bar = 1;
}',
            '<?php
class Foo
{
    /**
     * @var int
     */
    #[MyAttribute]
    private int $bar = 1;
}',
        ];

        yield 'multiple attributes' => [
            '<?php
class Foo
{
    /**
     */
    #[MyAttribute]
    #[MyAttribute2]
    private int $bar = 1;
}',
            '<?php
class Foo
{
    /**
     * @var int
     */
    #[MyAttribute]
    #[MyAttribute2]
    private int $bar = 1;
}',
        ];

        yield 'anonymous class with attribute' => [
            '<?php
new #[Bar] class() extends Foo {
    /**
     * @var Foo
     */
    public self $foo;

    /**
     * @param Foo $other
     * @return Foo
     */
    public function bar(self $other, int $superfluous): self {}
};',
            '<?php
new #[Bar] class() extends Foo {
    /**
     * @var Foo
     */
    public self $foo;

    /**
     * @param Foo $other
     * @param int $superfluous
     * @return Foo
     */
    public function bar(self $other, int $superfluous): self {}
};',
        ];
    }

    /**
     * @param array<string, mixed> $config
     *
     * @dataProvider provideFix81Cases
     *
     * @requires PHP 8.1
     */
    public function testFix81(string $expected, ?string $input = null, array $config = []): void
    {
        $this->fixer->configure($config);
        $this->doTest($expected, $input);
    }

    public static function provideFix81Cases(): iterable
    {
        yield 'some readonly properties' => [
            '<?php
class Foo {
    /**
     */
    private readonly array $bar1;

    /**
     */
    readonly private array $bar2;

    /**
     */
    readonly array $bar3;
}',
            '<?php
class Foo {
    /**
     * @var array
     */
    private readonly array $bar1;

    /**
     * @var array
     */
    readonly private array $bar2;

    /**
     * @var array
     */
    readonly array $bar3;
}',
        ];

        yield 'more details in phpdocs' => [
            '<?php
/**
 * @param Foo&Bar $foo
 */
function foo(FooInterface&Bar $foo) {}',
        ];

        yield 'intersection' => [
            '<?php
/**
 */
function foo(Foo&Bar $foo) {}',
            '<?php
/**
 * @param Foo&Bar $foo
 */
function foo(Foo&Bar $foo) {}',
        ];

        yield 'intersection different order' => [
            '<?php
/**
 * Composite types (i.e. mixing union and intersection types) is not supported in PHP8.1
 *
 * @param A|string[] $bar
 */
function foo(A & B & C $foo, A|array $bar) {}',
            '<?php
/**
 * Composite types (i.e. mixing union and intersection types) is not supported in PHP8.1
 *
 * @param C&A&B $foo
 * @param A|string[] $bar
 */
function foo(A & B & C $foo, A|array $bar) {}',
        ];

        yield 'remove_enum_inheritdoc' => [
            '<?php
/**
 *
 */
enum Foo {}',
            '<?php
/**
 * @inheritDoc
 */
enum Foo {}',
            ['remove_inheritdoc' => true],
        ];

        yield 'promoted readonly properties' => [
            '<?php class Foo {
                /**
                 */
                public function __construct(
                    public readonly string $a,
                    readonly public string $b,
                    public readonly ?string $c,
                ) {}
            }',
            '<?php class Foo {
                /**
                 * @param string $a
                 * @param string $b
                 * @param null|string $c
                 */
                public function __construct(
                    public readonly string $a,
                    readonly public string $b,
                    public readonly ?string $c,
                ) {}
            }',
        ];

        yield 'self as native type and enum name in phpdocs' => [
            '<?php
enum Foo {
    /**
     */
    public function bar(self $other): self {}
}',
            '<?php
enum Foo {
    /**
     * @param Foo $other
     * @return Foo
     */
    public function bar(self $other): self {}
}',
        ];

        yield 'enum name as native type and self in phpdocs' => [
            '<?php
enum Foo {
    /**
     */
    public function bar(Foo $other): Foo {}
}',
            '<?php
enum Foo {
    /**
     * @param self $other
     * @return self
     */
    public function bar(Foo $other): Foo {}
}',
        ];
    }

    /**
     * @param array<string, mixed> $config
     *
     * @dataProvider provideFix82Cases
     *
     * @requires PHP 8.2
     */
    public function testFix82(string $expected, ?string $input = null, array $config = []): void
    {
        $this->fixer->configure($config);
        $this->doTest($expected, $input);
    }

    public static function provideFix82Cases(): iterable
    {
        yield 'explicit null with null native type' => [
            '<?php
class Foo {
    /**  */
    public function foo(): null { return null; }
}',
            '<?php
class Foo {
    /** @return null */
    public function foo(): null { return null; }
}',
        ];
    }
}
