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

namespace PhpCsFixer\Tests\Fixer\ClassNotation;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Filippo Tessarotto <zoeslam@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ClassNotation\NoUnneededFinalMethodFixer
 */
final class NoUnneededFinalMethodFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideCases()
    {
        return [
            'default' => [
                '<?php
final class Foo {
    public function foo() {}
    protected function bar() {}
    private function baz() {}
}',
                '<?php
final class Foo {
    final public function foo() {}
    final protected function bar() {}
    final private function baz() {}
}',
            ],
            'preserve-comment' => [
                '<?php final class Foo { /* comment */public function foo() {} }',
                '<?php final class Foo { final/* comment */public function foo() {} }',
            ],
            'multiple-classes-per-file' => [
                '<?php final class Foo { public function foo() {} } abstract class Bar { final public function bar() {} }',
                '<?php final class Foo { final public function foo() {} } abstract class Bar { final public function bar() {} }',
            ],
            'non-final' => [
                '<php class Foo { final public function foo() {} }',
            ],
            'abstract-class' => [
                '<php abstract class Foo { final public function foo() {} }',
            ],
            'trait' => [
                '<php trait Foo { final public function foo() {} }',
            ],
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @requires PHP 7.0
     * @dataProvider providePhp70Cases
     */
    public function testFixPhp70($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function providePhp70Cases()
    {
        return [
            'anonymous-class-inside' => [
                '<?php
final class Foo
{
    public function foo()
    {
    }

    private function bar()
    {
        new class {
            final public function baz()
            {
            }
        };
    }
}
',
                '<?php
final class Foo
{
    final public function foo()
    {
    }

    private function bar()
    {
        new class {
            final public function baz()
            {
            }
        };
    }
}
',
            ],
        ];
    }
}
