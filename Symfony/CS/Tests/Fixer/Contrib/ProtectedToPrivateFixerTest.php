<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
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
        $from = function ($text, $traitText = '') {
            return sprintf('<?php

%s
{
    %s

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
    // public $v1;
    // protected $v2;
    // private $v3;
    // public static $v4;
    // protected static $v5;
    // private static $v6;
    // public function f1(){}
    // protected function f2(){}
    // private function f3(){}
    // public static function f4(){}
    // protected static function f5(){}
    // private static function f6(){}
}', $text, $traitText);
        };

        $to = function ($text, $traitText = '') {
            return sprintf('<?php

%s
{
    %s

    public $v1;
    private $v2;
    private $v3;
    public static $v4;
    private static $v5;
    private static $v6;
    public function f1(){}
    private function f2(){}
    private function f3(){}
    public static function f4(){}
    private static function f5(){}
    private static function f6(){}
    // public $v1;
    // protected $v2;
    // private $v3;
    // public static $v4;
    // protected static $v5;
    // private static $v6;
    // public function f1(){}
    // protected function f2(){}
    // private function f3(){}
    // public static function f4(){}
    // protected static function f5(){}
    // private static function f6(){}
}', $text, $traitText);
        };

        return array(
            'final-extends' => array(
                $from('final class MyClass extends MyAbstractClass'),
            ),
            'normal-extends' => array(
                $from('class MyClass extends MyAbstractClass'),
            ),
            'abstract' => array(
                $from('abstract class MyAbstractClass'),
            ),
            'normal' => array(
                $from('class MyClass'),
            ),
            'trait' => array(
                $from('trait MyTrait'),
            ),
            'final-with-trait' => array(
                $from('final class MyClass', 'use MyTrait;'),
            ),
            'final' => array(
                $to('final class MyClass'),
                $from('final class MyClass'),
            ),
            'final-implements' => array(
                $to('final class MyClass implements MyInterface'),
                $from('final class MyClass implements MyInterface'),
            ),
            'final-use' => array(
                $to("use stdClass;\nfinal class MyClass"),
                $from("use stdClass;\nfinal class MyClass"),
            ),
        );
    }
}
