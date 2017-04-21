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

use PhpCsFixer\Test\AbstractFixerTestCase;

/**
 * @author Filippo Tessarotto <zoeslam@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ClassNotation\ProtectedToPrivateFixer
 */
final class ProtectedToPrivateFixerTest extends AbstractFixerTestCase
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
        $attributesAndMethodsOriginal = $this->getAttributesAndMethods(true);
        $attributesAndMethodsFixed = $this->getAttributesAndMethods(false);

        return [
            'final-extends' => [
                "<?php final class MyClass extends MyAbstractClass { $attributesAndMethodsOriginal }",
            ],
            'normal-extends' => [
                "<?php class MyClass extends MyAbstractClass { $attributesAndMethodsOriginal }",
            ],
            'abstract' => [
                "<?php abstract class MyAbstractClass { $attributesAndMethodsOriginal }",
            ],
            'normal' => [
                "<?php class MyClass { $attributesAndMethodsOriginal }",
            ],
            'trait' => [
                "<?php trait MyTrait { $attributesAndMethodsOriginal }",
            ],
            'final-with-trait' => [
                "<?php final class MyClass { use MyTrait; $attributesAndMethodsOriginal }",
            ],
            'multiline-comment' => [
                '<?php final class MyClass { /* public protected private */ }',
            ],
            'inline-comment' => [
                "<?php final class MyClass { \n // public protected private \n }",
            ],
            'final' => [
                "<?php final class MyClass { $attributesAndMethodsFixed }",
                "<?php final class MyClass { $attributesAndMethodsOriginal }",
            ],
            'final-implements' => [
                "<?php final class MyClass implements MyInterface { $attributesAndMethodsFixed }",
                "<?php final class MyClass implements MyInterface { $attributesAndMethodsOriginal }",
            ],
            'final-with-use-before' => [
                "<?php use stdClass; final class MyClass { $attributesAndMethodsFixed }",
                "<?php use stdClass; final class MyClass { $attributesAndMethodsOriginal }",
            ],
            'final-with-use-after' => [
                "<?php final class MyClass { $attributesAndMethodsFixed } use stdClass;",
                "<?php final class MyClass { $attributesAndMethodsOriginal } use stdClass;",
            ],
            'multiple-classes' => [
                "<?php final class MyFirstClass { $attributesAndMethodsFixed } class MySecondClass { $attributesAndMethodsOriginal } final class MyThirdClass { $attributesAndMethodsFixed } ",
                "<?php final class MyFirstClass { $attributesAndMethodsOriginal } class MySecondClass { $attributesAndMethodsOriginal } final class MyThirdClass { $attributesAndMethodsOriginal } ",
            ],
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provide70Cases
     * @requires PHP 7.0
     */
    public function test70Fix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provide70Cases()
    {
        $attributesAndMethodsOriginal = $this->getAttributesAndMethods(true);
        $attributesAndMethodsFixed = $this->getAttributesAndMethods(false);

        return [
            'anonymous-class-inside' => [
                "<?php
final class Foo
{
    $attributesAndMethodsFixed

    private function bar()
    {
        new class {
            $attributesAndMethodsOriginal
        };
    }
}
",
                "<?php
final class Foo
{
    $attributesAndMethodsOriginal

    protected function bar()
    {
        new class {
            $attributesAndMethodsOriginal
        };
    }
}
",
            ],
        ];
    }

    private function getAttributesAndMethods($original)
    {
        $attributesAndMethodsOriginal = '
public $v1;
protected $v2;
private $v3;
public static $v4;
protected static $v5;
private static $v6;
public function f1(){}
protected function f2(){}
private function f3(){}
public static function f4(){}
protected static function f5(){}
private static function f6(){}
';
        if ($original) {
            return $attributesAndMethodsOriginal;
        }

        return str_replace('protected', 'private', $attributesAndMethodsOriginal);
    }
}
