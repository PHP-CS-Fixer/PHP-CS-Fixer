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
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @param Bar $bar
                     *
                     * @return Baz
                     */
                    public function doFoo($bar) {}
                }
                EOD,
        ];

        yield 'same type declaration' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     */
                    public function doFoo(Bar $bar) {}
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @param Bar $bar
                     */
                    public function doFoo(Bar $bar) {}
                }
                EOD,
        ];

        yield 'same optional type declaration' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     */
                    public function doFoo(Bar $bar = NULL) {}
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @param Bar|null $bar
                     */
                    public function doFoo(Bar $bar = NULL) {}
                }
                EOD,
        ];

        yield 'same type declaration with description' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @param Bar $bar an instance of Bar
                     */
                    public function doFoo(Bar $bar) {}
                }
                EOD,
        ];

        yield 'allow_mixed=>false' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     *
                     */
                    public function doFoo($bar) {}
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @param mixed $bar
                     *
                     * @return mixed
                     */
                    public function doFoo($bar) {}
                }
                EOD,
            ['allow_mixed' => false],
        ];

        yield 'allow_mixed=>true' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     *
                     */
                    public function doFoo($bar) {}
                }
                EOD,
            null,
            ['allow_mixed' => true],
        ];

        yield 'allow_mixed=>false on property' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     */
                    private $bar;
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @var mixed
                     */
                    private $bar;
                }
                EOD,
            ['allow_mixed' => false],
        ];

        yield 'allow_mixed=>false on property with var' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     */
                    private $bar;
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @var mixed $bar
                     */
                    private $bar;
                }
                EOD,
            ['allow_mixed' => false],
        ];

        yield 'allow_mixed=>false on property but with comment' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @var mixed comment
                     */
                    private $bar;
                }
                EOD,
            null,
            ['allow_mixed' => false],
        ];

        yield 'allow_unused_params=>true' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @param string|int $c
                     */
                    public function doFoo($bar /*, $c = 0 */) {}
                }
                EOD,
            null,
            ['allow_unused_params' => true],
        ];

        yield 'multiple different types' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @param SubclassOfBar1|SubclassOfBar2 $bar
                     */
                    public function doFoo(Bar $bar) {}
                }
                EOD,
        ];

        yield 'same type declaration with different casing' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     */
                    public function doFoo(Bar $bar) {}
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @param bar $bar
                     */
                    public function doFoo(Bar $bar) {}
                }
                EOD,
        ];

        yield 'same type declaration with leading backslash - global' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     */
                    public function doFoo(Bar $bar) {}
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @param \Bar $bar
                     */
                    public function doFoo(Bar $bar) {}
                }
                EOD,
        ];

        yield 'same type declaration with leading backslash - namespaced' => [
            <<<'EOD'
                <?php
                namespace Xxx;

                class Foo {
                    /**
                     */
                    public function doFoo(Model\Invoice $bar) {}
                }
                EOD,
            <<<'EOD'
                <?php
                namespace Xxx;

                class Foo {
                    /**
                     * @param \Xxx\Model\Invoice $bar
                     */
                    public function doFoo(Model\Invoice $bar) {}
                }
                EOD,
        ];

        yield 'same type declaration without leading backslash - global' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     */
                    public function doFoo(\Bar $bar) {}
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @param Bar $bar
                     */
                    public function doFoo(\Bar $bar) {}
                }
                EOD,
        ];

        yield 'same type declaration without leading backslash - namespaced' => [
            <<<'EOD'
                <?php
                namespace Xxx;

                class Foo {
                    /**
                     */
                    public function doFoo(\Xxx\Bar $bar) {}
                }
                EOD,
            <<<'EOD'
                <?php
                namespace Xxx;

                class Foo {
                    /**
                     * @param Bar $bar
                     */
                    public function doFoo(\Xxx\Bar $bar) {}
                }
                EOD,
        ];

        yield 'same type declaration with null implied from native type - param type' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     */
                    public function setAttribute(?string $value, string $value2 = null): void
                    {
                    }
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @param string $value
                     * @param string $value2
                     */
                    public function setAttribute(?string $value, string $value2 = null): void
                    {
                    }
                }
                EOD,
        ];

        yield 'same type declaration with null implied from native type - return type' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     */
                    public function getX(): ?X
                    {
                    }
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @return X
                     */
                    public function getX(): ?X
                    {
                    }
                }
                EOD,
        ];

        yield 'same type declaration with null implied from native type - property' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**  */
                    public ?bool $enabled;
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                    /** @var bool */
                    public ?bool $enabled;
                }
                EOD,
        ];

        yield 'same type declaration with null but native type without null - invalid phpdoc must be kept unfixed' => [
            <<<'EOD'
                <?php
                class Foo {
                    /** @var bool|null */
                    public bool $enabled;
                }
                EOD,
        ];

        yield 'multiple arguments' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @param SubclassOfBar1|SubclassOfBar2 $bar
                     */
                    public function doFoo(Bar $bar, Baz $baz = null) {}
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @param SubclassOfBar1|SubclassOfBar2 $bar
                     * @param Baz|null $baz
                     */
                    public function doFoo(Bar $bar, Baz $baz = null) {}
                }
                EOD,
        ];

        yield 'with import' => [
            <<<'EOD'
                <?php
                use Foo\Bar;

                /**
                 */
                function foo(Bar $bar) {}
                EOD,
            <<<'EOD'
                <?php
                use Foo\Bar;

                /**
                 * @param Bar $bar
                 */
                function foo(Bar $bar) {}
                EOD,
        ];

        yield 'with root symbols' => [
            <<<'EOD'
                <?php
                /**
                 */
                function foo(\Foo\Bar $bar) {}
                EOD,
            <<<'EOD'
                <?php
                /**
                 * @param \Foo\Bar $bar
                 */
                function foo(\Foo\Bar $bar) {}
                EOD,
        ];

        yield 'with mix of imported and fully qualified symbols' => [
            <<<'EOD'
                <?php
                use Foo\Bar;
                use Foo\Baz;

                /**
                 */
                function foo(Bar $bar, \Foo\Baz $baz) {}
                EOD,
            <<<'EOD'
                <?php
                use Foo\Bar;
                use Foo\Baz;

                /**
                 * @param \Foo\Bar $bar
                 * @param Baz $baz
                 */
                function foo(Bar $bar, \Foo\Baz $baz) {}
                EOD,
        ];

        yield 'with aliased import' => [
            <<<'EOD'
                <?php
                use Foo\Bar as Baz;

                /**
                 */
                function foo(Baz $bar) {}
                EOD,
            <<<'EOD'
                <?php
                use Foo\Bar as Baz;

                /**
                 * @param \Foo\Bar $bar
                 */
                function foo(Baz $bar) {}
                EOD,
        ];

        yield 'with unmapped param' => [
            <<<'EOD'
                <?php
                use Foo\Bar;

                /**
                 * @param Bar
                 */
                function foo(Bar $bar) {}
                EOD,
        ];

        yield 'with param superfluous but not return' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     *
                     * @return Baz
                     */
                    public function doFoo(Bar $bar) {}
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @param Bar $bar
                     *
                     * @return Baz
                     */
                    public function doFoo(Bar $bar) {}
                }
                EOD,
        ];

        yield 'with not all params superfluous' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @param Bax|Baz $baxz
                     */
                    public function doFoo(Bar $bar, $baxz) {}
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @param Bar $bar
                     * @param Bax|Baz $baxz
                     */
                    public function doFoo(Bar $bar, $baxz) {}
                }
                EOD,
        ];

        yield 'with special type declarations' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     */
                    public function doFoo(array $bar, callable $baz) {}
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @param array    $bar
                     * @param callable $baz
                     */
                    public function doFoo(array $bar, callable $baz) {}
                }
                EOD,
        ];

        yield 'PHPDoc at the end of file' => [
            <<<'EOD'
                <?php
                /**
                 * Foo
                 */
                EOD,
        ];

        yield 'with_variable_in_description' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @param $foo Some description that includes a $variable
                     */
                    public function doFoo($foo) {}
                }
                EOD,
        ];

        yield 'with_null' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @param null $foo
                     * @return null
                     */
                    public function doFoo($foo) {}
                }
                EOD,
        ];

        yield 'inheritdoc' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @inheritDoc
                     */
                    public function doFoo($foo) {}
                }
                EOD,
        ];

        yield 'inline_inheritdoc' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * {@inheritdoc}
                     */
                    public function doFoo($foo) {}
                }
                EOD,
        ];

        yield 'dont_remove_inheritdoc' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @inheritDoc
                     */
                    public function doFoo($foo) {}
                }
                EOD,
            null,
            ['remove_inheritdoc' => false],
        ];

        yield 'dont_remove_inline_inheritdoc' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * {@inheritdoc}
                     */
                    public function doFoo($foo) {}
                }
                EOD,
            null,
            ['remove_inheritdoc' => false],
        ];

        yield 'remove_inheritdoc' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     *
                     */
                    public function doFoo($foo) {}
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @inheritDoc
                     */
                    public function doFoo($foo) {}
                }
                EOD,
            ['remove_inheritdoc' => true],
        ];

        yield 'remove_inline_inheritdoc' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     *
                     */
                    public function doFoo($foo) {}
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * {@inheritdoc}
                     */
                    public function doFoo($foo) {}
                }
                EOD,
            ['remove_inheritdoc' => true],
        ];

        yield 'dont_remove_inheritdoc_when_surrounded_by_text' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * Foo.
                     *
                     * @inheritDoc
                     *
                     * Bar.
                     */
                    public function doFoo($foo) {}
                }
                EOD,
            null,
            ['remove_inheritdoc' => true],
        ];

        yield 'dont_remove_inheritdoc_when_preceded_by_text' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * Foo.
                     *
                     * @inheritDoc
                     */
                    public function doFoo($foo) {}
                }
                EOD,
            null,
            ['remove_inheritdoc' => true],
        ];

        yield 'dont_remove_inheritdoc_when_followed_by_text' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @inheritDoc
                     *
                     * Bar.
                     */
                    public function doFoo($foo) {}
                }
                EOD,
            null,
            ['remove_inheritdoc' => true],
        ];

        yield 'dont_remove_inline_inheritdoc_inside_text' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * Foo {@inheritDoc} Bar.
                     */
                    public function doFoo($foo) {}
                }
                EOD,
            null,
            ['remove_inheritdoc' => true],
        ];

        yield 'inheritdocs' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @inheritDocs
                     */
                    public function doFoo($foo) {}
                }
                EOD,
        ];

        yield 'inline_inheritdocs' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * {@inheritdocs}
                     */
                    public function doFoo($foo) {}
                }
                EOD,
        ];

        yield 'dont_remove_inheritdocs' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @inheritDocs
                     */
                    public function doFoo($foo) {}
                }
                EOD,
            null,
            ['remove_inheritdoc' => false],
        ];

        yield 'dont_remove_inline_inheritdocs' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * {@inheritdocs}
                     */
                    public function doFoo($foo) {}
                }
                EOD,
            null,
            ['remove_inheritdoc' => false],
        ];

        yield 'remove_inheritdocs' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     *
                     */
                    public function doFoo($foo) {}
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @inheritDocs
                     */
                    public function doFoo($foo) {}
                }
                EOD,
            ['remove_inheritdoc' => true],
        ];

        yield 'remove_inline_inheritdocs' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     *
                     */
                    public function doFoo($foo) {}
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * {@inheritdocs}
                     */
                    public function doFoo($foo) {}
                }
                EOD,
            ['remove_inheritdoc' => true],
        ];

        yield 'dont_remove_inheritdocs_when_surrounded_by_text' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * Foo.
                     *
                     * @inheritDocs
                     *
                     * Bar.
                     */
                    public function doFoo($foo) {}
                }
                EOD,
            null,
            ['remove_inheritdoc' => true],
        ];

        yield 'dont_remove_inheritdocs_when_preceded_by_text' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * Foo.
                     *
                     * @inheritDocs
                     */
                    public function doFoo($foo) {}
                }
                EOD,
            null,
            ['remove_inheritdoc' => true],
        ];

        yield 'dont_remove_inheritdocs_when_followed_by_text' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @inheritDocs
                     *
                     * Bar.
                     */
                    public function doFoo($foo) {}
                }
                EOD,
            null,
            ['remove_inheritdoc' => true],
        ];

        yield 'dont_remove_inline_inheritdocs_inside_text' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * Foo {@inheritDocs} Bar.
                     */
                    public function doFoo($foo) {}
                }
                EOD,
            null,
            ['remove_inheritdoc' => true],
        ];

        yield 'property_inheritdoc' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @inheritDoc
                     */
                    private $foo;
                }
                EOD,
        ];

        yield 'inline_property_inheritdoc' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * {@inheritdoc}
                     */
                    private $foo;
                }
                EOD,
        ];

        yield 'dont_remove_property_inheritdoc' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @inheritDoc
                     */
                    private $foo;
                }
                EOD,
            null,
            ['remove_inheritdoc' => false],
        ];

        yield 'dont_remove_property_inline_inheritdoc' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * {@inheritdoc}
                     */
                    private $foo;
                }
                EOD,
            null,
            ['remove_inheritdoc' => false],
        ];

        yield 'remove_property_inheritdoc' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     *
                     */
                    private $foo;
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @inheritDoc
                     */
                    private $foo;
                }
                EOD,
            ['remove_inheritdoc' => true],
        ];

        yield 'remove_inline_property_inheritdoc' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     *
                     */
                    private $foo;
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * {@inheritdoc}
                     */
                    private $foo;
                }
                EOD,
            ['remove_inheritdoc' => true],
        ];

        yield 'dont_remove_property_inheritdoc_when_surrounded_by_text' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * Foo.
                     *
                     * @inheritDoc
                     *
                     * Bar.
                     */
                    private $foo;
                }
                EOD,
            null,
            ['remove_inheritdoc' => true],
        ];

        yield 'dont_remove_property_inheritdoc_when_preceded_by_text' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * Foo.
                     *
                     * @inheritDoc
                     */
                    private $foo;
                }
                EOD,
            null,
            ['remove_inheritdoc' => true],
        ];

        yield 'dont_remove_property_inheritdoc_when_followed_by_text' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @inheritDoc
                     *
                     * Bar.
                     */
                    private $foo;
                }
                EOD,
            null,
            ['remove_inheritdoc' => true],
        ];

        yield 'dont_remove_property_inline_inheritdoc_inside_text' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * Foo {@inheritDoc} Bar.
                     */
                    private $foo;
                }
                EOD,
            null,
            ['remove_inheritdoc' => true],
        ];

        yield 'property_inheritdocs' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @inheritDocs
                     */
                    private $foo;
                }
                EOD,
        ];

        yield 'inline_property_inheritdocs' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * {@inheritdocs}
                     */
                    private $foo;
                }
                EOD,
        ];

        yield 'dont_remove_property_inheritdocs' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @inheritDocs
                     */
                    private $foo;
                }
                EOD,
            null,
            ['remove_inheritdoc' => false],
        ];

        yield 'dont_remove_inline_property_inheritdocs' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * {@inheritdocs}
                     */
                    private $foo;
                }
                EOD,
            null,
            ['remove_inheritdoc' => false],
        ];

        yield 'remove_property_property_inheritdoc' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     *
                     */
                    private $foo;
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @inheritDocs
                     */
                    private $foo;
                }
                EOD,
            ['remove_inheritdoc' => true],
        ];

        yield 'remove_inline_property_inheritdocs' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     *
                     */
                    private $foo;
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * {@inheritdocs}
                     */
                    private $foo;
                }
                EOD,
            ['remove_inheritdoc' => true],
        ];

        yield 'dont_remove_property_inheritdocs_when_surrounded_by_text' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * Foo.
                     *
                     * @inheritDocs
                     *
                     * Bar.
                     */
                    private $foo;
                }
                EOD,
            null,
            ['remove_inheritdoc' => true],
        ];

        yield 'dont_remove_property_inheritdocs_when_preceded_by_text' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * Foo.
                     *
                     * @inheritDocs
                     */
                    private $foo;
                }
                EOD,
            null,
            ['remove_inheritdoc' => true],
        ];

        yield 'dont_remove_property_inheritdocs_when_followed_by_text' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @inheritDocs
                     *
                     * Bar.
                     */
                    private $foo;
                }
                EOD,
            null,
            ['remove_inheritdoc' => true],
        ];

        yield 'dont_remove_inline_property_inheritdocs_inside_text' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * Foo {@inheritDocs} Bar.
                     */
                    private $foo;
                }
                EOD,
            null,
            ['remove_inheritdoc' => true],
        ];

        yield 'class_inheritdoc' => [
            <<<'EOD'
                <?php
                /**
                 * @inheritDoc
                 */
                class Foo {}
                EOD,
        ];

        yield 'dont_remove_class_inheritdoc' => [
            <<<'EOD'
                <?php
                /**
                 * @inheritDoc
                 */
                class Foo {}
                EOD,
            null,
            ['remove_inheritdoc' => false],
        ];

        yield 'remove_class_inheritdoc' => [
            <<<'EOD'
                <?php
                /**
                 *
                 */
                class Foo {}
                EOD,
            <<<'EOD'
                <?php
                /**
                 * @inheritDoc
                 */
                class Foo {}
                EOD,
            ['remove_inheritdoc' => true],
        ];

        yield 'remove_interface_inheritdoc' => [
            <<<'EOD'
                <?php
                /**
                 *
                 */
                interface Foo {}
                EOD,
            <<<'EOD'
                <?php
                /**
                 * @inheritDoc
                 */
                interface Foo {}
                EOD,
            ['remove_inheritdoc' => true],
        ];

        yield 'dont_remove_class_inheritdoc_when_surrounded_by_text' => [
            <<<'EOD'
                <?php
                /**
                 * Foo.
                 *
                 * @inheritDoc
                 *
                 * Bar.
                 */
                class Foo {}
                EOD,
            null,
            ['remove_inheritdoc' => true],
        ];

        yield 'dont_remove_class_inheritdoc_when_preceded_by_text' => [
            <<<'EOD'
                <?php
                /**
                 * Foo.
                 *
                 * @inheritDoc
                 */
                class Foo {}
                EOD,
            null,
            ['remove_inheritdoc' => true],
        ];

        yield 'dont_remove_class_inheritdoc_when_followed_by_text' => [
            <<<'EOD'
                <?php
                /**
                 * @inheritDoc
                 *
                 * Bar.
                 */
                class Foo {}
                EOD,
            null,
            ['remove_inheritdoc' => true],
        ];

        yield 'remove_inheritdoc_after_other_tag' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @param int $foo an integer
                     *
                     *
                     */
                    public function doFoo($foo) {}
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @param int $foo an integer
                     *
                     * @inheritDoc
                     */
                    public function doFoo($foo) {}
                }
                EOD,
            ['remove_inheritdoc' => true],
        ];

        yield 'remove_only_inheritdoc_line' => [
            <<<'EOD'
                <?php
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
                }
                EOD,
            <<<'EOD'
                <?php
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
                }
                EOD,
            ['remove_inheritdoc' => true],
        ];

        yield 'remove_single_line_inheritdoc' => [
            <<<'EOD'
                <?php
                class Foo {
                    /** */
                    public function doFoo($foo) {}
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                    /** @inheritDoc */
                    public function doFoo($foo) {}
                }
                EOD,
            ['remove_inheritdoc' => true],
        ];

        yield 'remove_inheritdoc_on_first_line' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     */
                    public function doFoo($foo) {}
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                    /** @inheritDoc
                     */
                    public function doFoo($foo) {}
                }
                EOD,
            ['remove_inheritdoc' => true],
        ];

        yield 'remove_inheritdoc_on_last_line' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * */
                    public function doFoo($foo) {}
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @inheritDoc */
                    public function doFoo($foo) {}
                }
                EOD,
            ['remove_inheritdoc' => true],
        ];

        yield 'remove_inheritdoc_non_structural_element_it_does_not_inherit' => [
            <<<'EOD'
                <?php
                /**
                 *
                 */
                $foo = 1;
                EOD,
            <<<'EOD'
                <?php
                /**
                 * @inheritDoc
                 */
                $foo = 1;
                EOD,
            ['remove_inheritdoc' => true],
        ];

        yield 'property with unsupported type' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @var foo:bar
                     */
                    private $foo;
                }
                EOD,
        ];

        yield 'method with unsupported types' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @param foo:bar $foo
                     * @return foo:bar
                     */
                    public function foo($foo) {}
                }
                EOD,
        ];

        yield 'with constant values as type' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @var Bar::A|Bar::B|Baz::*|null
                     */
                    private $foo;

                    /**
                     * @var 1|'a'|'b'
                     */
                    private $bar;
                }
                EOD,
        ];

        yield 'same type declaration (with extra empty line)' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     *
                     */
                    public function doFoo(Bar $bar): Baz {}
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @param Bar $bar
                     *
                     * @return Baz
                     */
                    public function doFoo(Bar $bar): Baz {}
                }
                EOD,
        ];

        yield 'same type declaration (with return type) with description' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @param Bar $bar an instance of Bar
                     *
                     * @return Baz an instance of Baz
                     */
                    public function doFoo(Bar $bar): Baz {}
                }
                EOD,
        ];

        yield 'multiple different types (with return type)' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @param SubclassOfBar1|SubclassOfBar2 $bar
                     *
                     * @return SubclassOfBaz1|SubclassOfBaz2 $bar
                     */
                    public function doFoo(Bar $bar): Baz {}
                }
                EOD,
        ];

        yield 'with import (with return type)' => [
            <<<'EOD'
                <?php
                use Foo\Bar;
                use Foo\Baz;

                /**
                 */
                function foo(Bar $bar): Baz {}
                EOD,
            <<<'EOD'
                <?php
                use Foo\Bar;
                use Foo\Baz;

                /**
                 * @param Bar $bar
                 * @return Baz
                 */
                function foo(Bar $bar): Baz {}
                EOD,
        ];

        yield 'with root symbols (with return type)' => [
            <<<'EOD'
                <?php
                /**
                 */
                function foo(\Foo\Bar $bar): \Foo\Baz {}
                EOD,
            <<<'EOD'
                <?php
                /**
                 * @param \Foo\Bar $bar
                 * @return \Foo\Baz
                 */
                function foo(\Foo\Bar $bar): \Foo\Baz {}
                EOD,
        ];

        yield 'with mix of imported and fully qualified symbols (with return type)' => [
            <<<'EOD'
                <?php
                use Foo\Bar;
                use Foo\Baz;
                use Foo\Qux;

                /**
                 */
                function foo(Bar $bar, \Foo\Baz $baz): \Foo\Qux {}
                EOD,
            <<<'EOD'
                <?php
                use Foo\Bar;
                use Foo\Baz;
                use Foo\Qux;

                /**
                 * @param \Foo\Bar $bar
                 * @param Baz $baz
                 * @return Qux
                 */
                function foo(Bar $bar, \Foo\Baz $baz): \Foo\Qux {}
                EOD,
        ];

        yield 'with aliased import (with return type)' => [
            <<<'EOD'
                <?php
                use Foo\Bar as Baz;

                /**
                 */
                function foo(Baz $bar): Baz {}
                EOD,
            <<<'EOD'
                <?php
                use Foo\Bar as Baz;

                /**
                 * @param \Foo\Bar $bar
                 * @return \Foo\Bar
                 */
                function foo(Baz $bar): Baz {}
                EOD,
        ];

        yield 'with scalar type declarations' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     *
                     */
                    public function doFoo(int $bar, string $baz): bool {}
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @param int    $bar
                     * @param string $baz
                     *
                     * @return bool
                     */
                    public function doFoo(int $bar, string $baz): bool {}
                }
                EOD,
        ];

        yield 'really long one' => [
            <<<'EOD'
                <?php
                                    /**
                                     * "Sponsored" by https://github.com/PrestaShop/PrestaShop/blob/1.6.1.24/tools/tcpdf/tcpdf.php (search for "Get page dimensions from format name")
                                     * @see
                                     * @param $number - it can be:
                                     *
                EOD.' '.implode("\n                     * ", range(1, 1_000)).<<<'EOD'

                                     */
                                     function display($number) {}
                EOD."\n                ",
        ];

        yield 'return with @inheritDoc in description' => [
            <<<'EOD'
                <?php
                                    /**
                                     */
                                    function foo(): bool {}
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    /**
                                     * @return bool @inheritDoc
                                     */
                                    function foo(): bool {}
                EOD."\n                ",
            ['remove_inheritdoc' => true],
        ];

        yield 'remove_trait_inheritdoc' => [
            <<<'EOD'
                <?php
                /**
                 *
                 */
                trait Foo {}
                EOD,
            <<<'EOD'
                <?php
                /**
                 * @inheritDoc
                 */
                trait Foo {}
                EOD,
            ['remove_inheritdoc' => true],
        ];

        yield 'same nullable type declaration' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     *
                     */
                    public function doFoo(?Bar $bar): ?Baz {}
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @param Bar|null $bar
                     *
                     * @return Baz|null
                     */
                    public function doFoo(?Bar $bar): ?Baz {}
                }
                EOD,
        ];

        yield 'same nullable type declaration reversed' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     *
                     */
                    public function doFoo(?Bar $bar): ?Baz {}
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @param null|Bar $bar
                     *
                     * @return null|Baz
                     */
                    public function doFoo(?Bar $bar): ?Baz {}
                }
                EOD,
        ];

        yield 'same nullable type declaration with description' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @param Bar|null $bar an instance of Bar
                     *
                     * @return Baz|null an instance of Baz
                     */
                    public function doFoo(?Bar $bar): ?Baz {}
                }
                EOD,
        ];

        yield 'same optional nullable type declaration' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     */
                    public function doFoo(?Bar $bar = null) {}
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @param Bar|null $bar
                     */
                    public function doFoo(?Bar $bar = null) {}
                }
                EOD,
        ];

        yield 'multiple different types (nullable)' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @param SubclassOfBar1|SubclassOfBar2|null $bar
                     *
                     * @return SubclassOfBaz1|SubclassOfBaz2|null $bar
                     */
                    public function doFoo(?Bar $bar): ?Baz {}
                }
                EOD,
        ];

        yield 'with nullable import' => [
            <<<'EOD'
                <?php
                use Foo\Bar;
                use Foo\Baz;

                /**
                 */
                function foo(?Bar $bar): ?Baz {}
                EOD,
            <<<'EOD'
                <?php
                use Foo\Bar;
                use Foo\Baz;

                /**
                 * @param Bar|null $bar
                 * @return Baz|null
                 */
                function foo(?Bar $bar): ?Baz {}
                EOD,
        ];

        yield 'with nullable root symbols' => [
            <<<'EOD'
                <?php
                /**
                 */
                function foo(?\Foo\Bar $bar): ?\Foo\Baz {}
                EOD,
            <<<'EOD'
                <?php
                /**
                 * @param \Foo\Bar|null $bar
                 * @return \Foo\Baz|null
                 */
                function foo(?\Foo\Bar $bar): ?\Foo\Baz {}
                EOD,
        ];

        yield 'with nullable mix of imported and fully qualified symbols' => [
            <<<'EOD'
                <?php
                use Foo\Bar;
                use Foo\Baz;
                use Foo\Qux;

                /**
                 */
                function foo(?Bar $bar, ?\Foo\Baz $baz): ?\Foo\Qux {}
                EOD,
            <<<'EOD'
                <?php
                use Foo\Bar;
                use Foo\Baz;
                use Foo\Qux;

                /**
                 * @param \Foo\Bar|null $bar
                 * @param Baz|null $baz
                 * @return Qux|null
                 */
                function foo(?Bar $bar, ?\Foo\Baz $baz): ?\Foo\Qux {}
                EOD,
        ];

        yield 'with nullable aliased import' => [
            <<<'EOD'
                <?php
                use Foo\Bar as Baz;

                /**
                 */
                function foo(?Baz $bar): ?Baz {}
                EOD,
            <<<'EOD'
                <?php
                use Foo\Bar as Baz;

                /**
                 * @param \Foo\Bar|null $bar
                 * @return \Foo\Bar|null
                 */
                function foo(?Baz $bar): ?Baz {}
                EOD,
        ];

        yield 'with nullable special type declarations' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     *
                     */
                    public function doFoo(iterable $bar, ?int $baz): ?array {}
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @param iterable $bar
                     * @param int|null $baz
                     *
                     * @return array|null
                     */
                    public function doFoo(iterable $bar, ?int $baz): ?array {}
                }
                EOD,
        ];

        yield 'remove abstract annotation in function' => [
            <<<'EOD'
                <?php
                abstract class Foo {
                    /**
                     */
                    public abstract function doFoo();
                }
                EOD,
            <<<'EOD'
                <?php
                abstract class Foo {
                    /**
                     * @abstract
                     */
                    public abstract function doFoo();
                }
                EOD, ];

        yield 'dont remove abstract annotation in function' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @abstract
                     */
                    public function doFoo() {}
                }
                EOD, ];

        yield 'remove final annotation in function' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     */
                    public final function doFoo() {}
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @final
                     */
                    public final function doFoo() {}
                }
                EOD, ];

        yield 'dont remove final annotation in function' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @final
                     */
                    public function doFoo() {}
                }
                EOD, ];

        yield 'remove abstract annotation in class' => [
            <<<'EOD'
                <?php
                /**
                 */
                abstract class Foo {
                }
                EOD,
            <<<'EOD'
                <?php
                /**
                 * @abstract
                 */
                abstract class Foo {
                }
                EOD, ];

        yield 'dont remove abstract annotation in class' => [
            <<<'EOD'
                <?php
                abstract class Bar{}

                /**
                 * @abstract
                 */
                class Foo {
                }
                EOD, ];

        yield 'remove final annotation in class' => [
            <<<'EOD'
                <?php
                /**
                 */
                final class Foo {
                }
                EOD,
            <<<'EOD'
                <?php
                /**
                 * @final
                 */
                final class Foo {
                }
                EOD, ];

        yield 'dont remove final annotation in class' => [
            <<<'EOD'
                <?php
                final class Bar{}

                /**
                 * @final
                 */
                class Foo {
                }
                EOD, ];

        yield 'remove when used with reference' => [
            <<<'EOD'
                <?php class Foo {
                                    /**
                                     */
                                     function f1(string &$x) {}
                                    /**
                                     */
                                     function f2(string &$x) {}
                                    /**
                                     */
                                     function f3(string &$x) {}
                                }
                EOD,
            <<<'EOD'
                <?php class Foo {
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
                                }
                EOD,
        ];

        yield 'dont remove when used with reference' => [
            <<<'EOD'
                <?php class Foo {
                                    /**
                                     * @param string ...$x Description
                                     */
                                     function f(string ...$x) {}
                                }
                EOD,
        ];

        yield 'remove when used with splat operator' => [
            <<<'EOD'
                <?php class Foo {
                                    /**
                                     */
                                     function f1(string ...$x) {}
                                    /**
                                     */
                                     function f2(string ...$x) {}
                                }
                EOD,
            <<<'EOD'
                <?php class Foo {
                                    /**
                                     * @param string ...$x
                                     */
                                     function f1(string ...$x) {}
                                    /**
                                     * @param string ...$y Description
                                     */
                                     function f2(string ...$x) {}
                                }
                EOD,
        ];

        yield 'dont remove when used with splat operator' => [
            <<<'EOD'
                <?php class Foo {
                                    /**
                                     * @param string ...$x Description
                                     */
                                     function f(string ...$x) {}
                                }
                EOD,
        ];

        yield 'remove when used with reference and splat operator' => [
            <<<'EOD'
                <?php class Foo {
                                    /**
                                     */
                                     function f1(string &...$x) {}
                                    /**
                                     */
                                     function f2(string &...$x) {}
                                    /**
                                     */
                                     function f3(string &...$x) {}
                                }
                EOD,
            <<<'EOD'
                <?php class Foo {
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
                                }
                EOD,
        ];

        yield 'dont remove when used with reference and splat operator' => [
            <<<'EOD'
                <?php class Foo {
                                    /**
                                     * @param string &...$x Description
                                     */
                                     function f(string &...$x) {}
                                }
                EOD,
        ];

        yield 'some typed static public property' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     */
                    static public Bar $bar;
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @var Bar
                     */
                    static public Bar $bar;
                }
                EOD,
        ];

        yield 'some typed public static property' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     */
                    public static Bar $bar;
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @var Bar
                     */
                    public static Bar $bar;
                }
                EOD,
        ];

        yield 'some typed public property' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     */
                    public Bar $bar;
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @var Bar
                     */
                    public Bar $bar;
                }
                EOD,
        ];

        yield 'some typed public property with single line PHPDoc' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**  */
                    public Bar $bar;
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                    /** @var Bar */
                    public Bar $bar;
                }
                EOD,
        ];

        yield 'some typed public property with semi-single line PHPDoc' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     */
                    public Bar $bar;

                    /**
                     */
                    public Baz $baz;
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                    /** @var Bar
                     */
                    public Bar $bar;

                    /**
                     * @var Baz */
                    public Baz $baz;
                }
                EOD,
        ];

        yield 'some typed protected property' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     */
                    protected Bar $bar;
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @var Bar
                     */
                    protected Bar $bar;
                }
                EOD,
        ];

        yield 'some typed private property' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     */
                    private Bar $bar;
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @var Bar
                     */
                    private Bar $bar;
                }
                EOD,
        ];

        yield 'some typed nullable private property' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     */
                    private ?Bar $bar;
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @var null|Bar
                     */
                    private ?Bar $bar;
                }
                EOD,
        ];

        yield 'some typed nullable property with name declared in phpdoc' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     */
                    private ?Bar $bar;
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @var null|Bar $bar
                     */
                    private ?Bar $bar;
                }
                EOD,
        ];

        yield 'some array property' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     */
                    private array $bar;
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @var array
                     */
                    private array $bar;
                }
                EOD,
        ];

        yield 'some nullable array property' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     */
                    private ?array $bar;
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @var array|null
                     */
                    private ?array $bar;
                }
                EOD,
        ];

        yield 'some object property' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     */
                    private object $bar;
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @var object
                     */
                    private object $bar;
                }
                EOD,
        ];

        yield 'phpdoc does not match property type declaration' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @var FooImplementation1|FooImplementation2
                     */
                    private FooInterface $bar;
                }
                EOD,
        ];

        yield 'allow_mixed=>false but with description' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @var mixed description
                     */
                    private $bar;
                }
                EOD,
            null,
            ['allow_mixed' => false],
        ];

        yield 'allow_mixed=>false but with description and var name' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @var mixed $bar description
                     */
                    private $bar;
                }
                EOD,
            null,
            ['allow_mixed' => false],
        ];

        yield 'allow_mixed=>true ||' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @var mixed
                     */
                    private $bar;
                }
                EOD,
            null,
            ['allow_mixed' => true],
        ];

        yield 'some fully qualified typed property' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     */
                    protected \Foo\Bar $bar;
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @var \Foo\Bar
                     */
                    protected \Foo\Bar $bar;
                }
                EOD,
        ];

        yield 'some fully qualified imported typed property' => [
            <<<'EOD'
                <?php
                namespace App;
                use Foo\Bar;
                class Foo {
                    /**
                     */
                    protected Bar $bar;
                }
                EOD,
            <<<'EOD'
                <?php
                namespace App;
                use Foo\Bar;
                class Foo {
                    /**
                     * @var \Foo\Bar
                     */
                    protected Bar $bar;
                }
                EOD,
        ];

        yield 'self as native type and interface name in phpdocs' => [
            <<<'EOD'
                <?php
                interface Foo {
                    /**
                     */
                    public function bar(self $other): self;
                }
                EOD,
            <<<'EOD'
                <?php
                interface Foo {
                    /**
                     * @param Foo $other
                     * @return Foo
                     */
                    public function bar(self $other): self;
                }
                EOD,
        ];

        yield 'interface name as native type and self in phpdocs' => [
            <<<'EOD'
                <?php
                interface Foo {
                    /**
                     */
                    public function bar(Foo $other): Foo;
                }
                EOD,
            <<<'EOD'
                <?php
                interface Foo {
                    /**
                     * @param self $other
                     * @return self
                     */
                    public function bar(Foo $other): Foo;
                }
                EOD,
        ];

        yield 'self as native type and class name in phpdocs' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     */
                    public self $foo;

                    /**
                     */
                    public function bar(self $other): self {}
                }
                EOD,
            <<<'EOD'
                <?php
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
                }
                EOD,
        ];

        yield 'class name as native type and self in phpdocs' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     */
                    public Foo $foo;

                    /**
                     */
                    public function bar(Foo $other): Foo {}
                }
                EOD,
            <<<'EOD'
                <?php
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
                }
                EOD,
        ];

        yield 'anonymous class' => [
            <<<'EOD'
                <?php
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
                };
                EOD,
            <<<'EOD'
                <?php
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
                };
                EOD,
        ];

        yield 'remove empty var' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     */
                    private $foo;
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @var
                     */
                    private $foo;
                }
                EOD,
        ];

        yield 'remove empty var single line' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**  */
                    private $foo;
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                    /** @var */
                    private $foo;
                }
                EOD,
        ];

        yield 'dont remove var without a type but with a property name and a description' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @var $foo some description
                     */
                    private $foo;
                }
                EOD,
        ];

        yield 'dont remove single line var without a type but with a property name and a description' => [
            <<<'EOD'
                <?php
                class Foo {
                    /** @var $foo some description */
                    private $foo;
                }
                EOD,
        ];

        yield 'remove var without a type but with a property name' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     */
                    private $foo;
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @var $foo
                     */
                    private $foo;
                }
                EOD,
        ];

        yield 'remove single line var without a type but with a property name' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**  */
                    private $foo;
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                    /** @var $foo */
                    private $foo;
                }
                EOD,
        ];

        yield 'remove empty param' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     */
                    public function foo($foo) {}
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @param
                     */
                    public function foo($foo) {}
                }
                EOD,
        ];

        yield 'remove empty single line param' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**  */
                    public function foo($foo) {}
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                    /** @param */
                    public function foo($foo) {}
                }
                EOD,
        ];

        yield 'remove param without a type' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     */
                    public function foo($foo) {}
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @param $foo
                     */
                    public function foo($foo) {}
                }
                EOD,
        ];

        yield 'remove single line param without a type' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**  */
                    public function foo($foo) {}
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                    /** @param $foo */
                    public function foo($foo) {}
                }
                EOD,
        ];

        yield 'dont remove param without a type but with a description' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @param $foo description
                     */
                    public function foo($foo) {}
                }
                EOD,
        ];

        yield 'dont remove single line param without a type but with a description' => [
            <<<'EOD'
                <?php
                class Foo {
                    /** @param $foo description */
                    public function foo($foo) {}
                }
                EOD,
        ];

        yield 'remove empty return' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     */
                    public function foo($foo) {}
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @return
                     */
                    public function foo($foo) {}
                }
                EOD,
        ];

        yield 'remove empty single line return' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**  */
                    public function foo($foo) {}
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                    /** @return */
                    public function foo($foo) {}
                }
                EOD,
        ];

        yield 'explicit null must stay - global namespace' => [
            <<<'EOD'
                <?php
                class Foo {
                    /** @return null */
                    public function foo() {}
                }
                EOD,
        ];

        yield 'explicit null must stay - custom namespace' => [
            <<<'EOD'
                <?php
                namespace A\B;
                class Foo {
                    /** @return null */
                    public function foo() {}
                }
                EOD,
        ];

        yield 'superfluous asterisk in corrupted phpDoc' => [
            <<<'EOD'
                <?php
                class Foo {
                    /** * @return Baz */
                    public function doFoo($bar) {}
                }
                EOD,
        ];

        yield 'superfluous return type after superfluous asterisk in corrupted phpDoc' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**  */
                    public function doFoo($bar): Baz {}
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                    /** * @return Baz */
                    public function doFoo($bar): Baz {}
                }
                EOD,
        ];

        yield 'superfluous parameter type for anonymous function' => [
            <<<'EOD'
                <?php
                /**  */
                function (int $foo) { return 1; };
                EOD,
            <<<'EOD'
                <?php
                /** @param int $foo */
                function (int $foo) { return 1; };
                EOD,
        ];

        yield 'superfluous return type for anonymous function' => [
            <<<'EOD'
                <?php
                /**  */
                function ($foo): int { return 1; };
                EOD,
            <<<'EOD'
                <?php
                /** @return int */
                function ($foo): int { return 1; };
                EOD,
        ];

        yield 'superfluous parameter type for static anonymous function' => [
            <<<'EOD'
                <?php
                /**  */
                static function (int $foo) { return 1; };
                EOD,
            <<<'EOD'
                <?php
                /** @param int $foo */
                static function (int $foo) { return 1; };
                EOD,
        ];

        yield 'superfluous return type for static anonymous function' => [
            <<<'EOD'
                <?php
                /**  */
                static function ($foo): int { return 1; };
                EOD,
            <<<'EOD'
                <?php
                /** @return int */
                static function ($foo): int { return 1; };
                EOD,
        ];

        yield 'superfluous parameter type for arrow function' => [
            <<<'EOD'
                <?php
                /**  */
                fn (int $foo) => 1;
                EOD,
            <<<'EOD'
                <?php
                /** @param int $foo */
                fn (int $foo) => 1;
                EOD,
        ];

        yield 'superfluous return type for arrow function' => [
            <<<'EOD'
                <?php
                /**  */
                fn ($foo): int => 1;
                EOD,
            <<<'EOD'
                <?php
                /** @return int */
                fn ($foo): int => 1;
                EOD,
        ];

        yield 'superfluous parameter type for static arrow function' => [
            <<<'EOD'
                <?php
                /**  */
                static fn (int $foo) => 1;
                EOD,
            <<<'EOD'
                <?php
                /** @param int $foo */
                static fn (int $foo) => 1;
                EOD,
        ];

        yield 'superfluous return type for static arrow function' => [
            <<<'EOD'
                <?php
                /**  */
                static fn ($foo): int => 1;
                EOD,
            <<<'EOD'
                <?php
                /** @return int */
                static fn ($foo): int => 1;
                EOD,
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
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     */
                    public function foo($foo): static {}
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @return static
                     */
                    public function foo($foo): static {}
                }
                EOD,
        ];

        yield 'union type on parameter' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     */
                    public function foo(int|string $foo) {}
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @param int|string $foo
                     */
                    public function foo(int|string $foo) {}
                }
                EOD,
        ];

        yield 'union type on return type' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     */
                    public function foo($foo): int|string {}
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @return int|string
                     */
                    public function foo($foo): int|string {}
                }
                EOD,
        ];

        yield 'union type on property' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     */
                    public int|string $foo;
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @var int|string
                     */
                    public int|string $foo;
                }
                EOD,
        ];

        yield 'union type on property with spaces' => [
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     */
                    public int  |  string $foo;
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                    /**
                     * @var int|string
                     */
                    public int  |  string $foo;
                }
                EOD,
        ];

        yield 'union type with null' => [
            <<<'EOD'
                <?php
                /**
                 */
                function foo(int|string|null $foo) {}
                EOD,
            <<<'EOD'
                <?php
                /**
                 * @param int|string|null $foo
                 */
                function foo(int|string|null $foo) {}
                EOD,
        ];

        yield 'union type in different order' => [
            <<<'EOD'
                <?php
                /**
                 */
                function foo(string|int $foo) {}
                EOD,
            <<<'EOD'
                <?php
                /**
                 * @param int|string $foo
                 */
                function foo(string|int $foo) {}
                EOD,
        ];

        yield 'more details in phpdocs' => [
            <<<'EOD'
                <?php
                /**
                 * @param string|array<string> $foo
                 */
                function foo(string|array $foo) {}
                EOD,
        ];

        yield 'missing types in phpdocs' => [
            <<<'EOD'
                <?php
                /**
                 * @param string|int $foo
                 */
                function foo(string|array|int $foo) {}
                EOD,
        ];

        yield 'too many types in phpdocs' => [
            <<<'EOD'
                <?php
                /**
                 * @param string|array|int $foo
                 */
                function foo(string|int $foo) {}
                EOD,
        ];

        yield 'promoted properties' => [
            <<<'EOD'
                <?php class Foo {
                                /**
                                 */
                                public function __construct(
                                    public string $a,
                                    protected ?string $b,
                                    private ?string $c,
                                ) {}
                            }
                EOD,
            <<<'EOD'
                <?php class Foo {
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
                            }
                EOD,
        ];

        yield 'single attribute' => [
            <<<'EOD'
                <?php
                class Foo
                {
                    /**
                     */
                    #[MyAttribute]
                    private int $bar = 1;
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo
                {
                    /**
                     * @var int
                     */
                    #[MyAttribute]
                    private int $bar = 1;
                }
                EOD,
        ];

        yield 'multiple attributes' => [
            <<<'EOD'
                <?php
                class Foo
                {
                    /**
                     */
                    #[MyAttribute]
                    #[MyAttribute2]
                    private int $bar = 1;
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo
                {
                    /**
                     * @var int
                     */
                    #[MyAttribute]
                    #[MyAttribute2]
                    private int $bar = 1;
                }
                EOD,
        ];

        yield 'anonymous class with attribute' => [
            <<<'EOD'
                <?php
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
                };
                EOD,
            <<<'EOD'
                <?php
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
                };
                EOD,
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
            <<<'EOD'
                <?php
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
                }
                EOD,
            <<<'EOD'
                <?php
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
                }
                EOD,
        ];

        yield 'more details in phpdocs' => [
            <<<'EOD'
                <?php
                /**
                 * @param Foo&Bar $foo
                 */
                function foo(FooInterface&Bar $foo) {}
                EOD,
        ];

        yield 'intersection' => [
            <<<'EOD'
                <?php
                /**
                 */
                function foo(Foo&Bar $foo) {}
                EOD,
            <<<'EOD'
                <?php
                /**
                 * @param Foo&Bar $foo
                 */
                function foo(Foo&Bar $foo) {}
                EOD,
        ];

        yield 'intersection different order' => [
            <<<'EOD'
                <?php
                /**
                 * Composite types (i.e. mixing union and intersection types) is not supported in PHP8.1
                 *
                 * @param A|string[] $bar
                 */
                function foo(A & B & C $foo, A|array $bar) {}
                EOD,
            <<<'EOD'
                <?php
                /**
                 * Composite types (i.e. mixing union and intersection types) is not supported in PHP8.1
                 *
                 * @param C&A&B $foo
                 * @param A|string[] $bar
                 */
                function foo(A & B & C $foo, A|array $bar) {}
                EOD,
        ];

        yield 'remove_enum_inheritdoc' => [
            <<<'EOD'
                <?php
                /**
                 *
                 */
                enum Foo {}
                EOD,
            <<<'EOD'
                <?php
                /**
                 * @inheritDoc
                 */
                enum Foo {}
                EOD,
            ['remove_inheritdoc' => true],
        ];

        yield 'promoted readonly properties' => [
            <<<'EOD'
                <?php class Foo {
                                /**
                                 */
                                public function __construct(
                                    public readonly string $a,
                                    readonly public string $b,
                                    public readonly ?string $c,
                                ) {}
                            }
                EOD,
            <<<'EOD'
                <?php class Foo {
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
                            }
                EOD,
        ];

        yield 'self as native type and enum name in phpdocs' => [
            <<<'EOD'
                <?php
                enum Foo {
                    /**
                     */
                    public function bar(self $other): self {}
                }
                EOD,
            <<<'EOD'
                <?php
                enum Foo {
                    /**
                     * @param Foo $other
                     * @return Foo
                     */
                    public function bar(self $other): self {}
                }
                EOD,
        ];

        yield 'enum name as native type and self in phpdocs' => [
            <<<'EOD'
                <?php
                enum Foo {
                    /**
                     */
                    public function bar(Foo $other): Foo {}
                }
                EOD,
            <<<'EOD'
                <?php
                enum Foo {
                    /**
                     * @param self $other
                     * @return self
                     */
                    public function bar(Foo $other): Foo {}
                }
                EOD,
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
            <<<'EOD'
                <?php
                class Foo {
                    /**  */
                    public function foo(): null { return null; }
                }
                EOD,
            <<<'EOD'
                <?php
                class Foo {
                    /** @return null */
                    public function foo(): null { return null; }
                }
                EOD,
        ];
    }
}
