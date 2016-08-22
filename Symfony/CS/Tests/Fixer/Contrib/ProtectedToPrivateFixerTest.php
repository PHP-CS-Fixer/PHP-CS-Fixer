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

namespace Symfony\CS\Tests\Fixer\Contrib;

use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

/**
 * @author Filippo Tessarotto <zoeslam@gmail.com>
 *
 * @internal
 */
final class ProtectedToPrivateFixerTest extends AbstractFixerTestBase
{
    /**
     * @dataProvider provideCases
     */
    public function testFix($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    public function provideCases()
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
        $attributesAndMethodsFixed = str_replace('protected', 'private', $attributesAndMethodsOriginal);

        return array(
            'final-extends' => array(
                "<?php final class MyClass extends MyAbstractClass { $attributesAndMethodsOriginal }",
            ),
            'normal-extends' => array(
                "<?php class MyClass extends MyAbstractClass { $attributesAndMethodsOriginal }",
            ),
            'abstract' => array(
                "<?php abstract class MyAbstractClass { $attributesAndMethodsOriginal }",
            ),
            'normal' => array(
                "<?php class MyClass { $attributesAndMethodsOriginal }",
            ),
            'trait' => array(
                "<?php trait MyTrait { $attributesAndMethodsOriginal }",
            ),
            'final-with-trait' => array(
                "<?php final class MyClass { use MyTrait; $attributesAndMethodsOriginal }",
            ),
            'multiline-comment' => array(
                '<?php final class MyClass { /* public protected private */ }',
            ),
            'inline-comment' => array(
                "<?php final class MyClass { \n // public protected private \n }",
            ),
            'final' => array(
                "<?php final class MyClass { $attributesAndMethodsFixed }",
                "<?php final class MyClass { $attributesAndMethodsOriginal }",
            ),
            'final-implements' => array(
                "<?php final class MyClass implements MyInterface { $attributesAndMethodsFixed }",
                "<?php final class MyClass implements MyInterface { $attributesAndMethodsOriginal }",
            ),
            'final-with-use-before' => array(
                "<?php use stdClass; final class MyClass { $attributesAndMethodsFixed }",
                "<?php use stdClass; final class MyClass { $attributesAndMethodsOriginal }",
            ),
            'final-with-use-after' => array(
                "<?php final class MyClass { $attributesAndMethodsFixed } use stdClass;",
                "<?php final class MyClass { $attributesAndMethodsOriginal } use stdClass;",
            ),
            'multiple-classes' => array(
                "<?php final class MyFirstClass { $attributesAndMethodsFixed } class MySecondClass { $attributesAndMethodsOriginal } final class MyThirdClass { $attributesAndMethodsFixed } ",
                "<?php final class MyFirstClass { $attributesAndMethodsOriginal } class MySecondClass { $attributesAndMethodsOriginal } final class MyThirdClass { $attributesAndMethodsOriginal } ",
            ),
        );
    }
}
