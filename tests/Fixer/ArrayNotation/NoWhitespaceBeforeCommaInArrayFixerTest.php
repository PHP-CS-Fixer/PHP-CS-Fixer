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

namespace PhpCsFixer\Tests\Fixer\ArrayNotation;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Adam Marczuk <adam@marczuk.info>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ArrayNotation\NoWhitespaceBeforeCommaInArrayFixer
 */
final class NoWhitespaceBeforeCommaInArrayFixerTest extends AbstractFixerTestCase
{
    /**
     * @param array<string, mixed> $config
     *
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, string $input = null, array $config = []): void
    {
        $this->fixer->configure($config);

        $this->doTest($expected, $input);
    }

    public function provideFixCases(): array
    {
        return [
            // old style array
            [
                '<?php $x = array(1, "2",3);',
                '<?php $x = array(1 , "2",3);',
            ],
            // old style array with comments
            [
                '<?php $x = array /* comment */ (1,  "2", 3);',
                '<?php $x = array /* comment */ (1  ,  "2", 3);',
            ],
            // old style array with comments
            [
                '<?php $x = array(1#
,#
"2", 3);',
                '<?php $x = array(1#
,#
"2"  , 3);',
            ],

            // short array
            [
                '<?php $x = [1,  "2", 3,$y];',
                '<?php $x = [1 ,  "2", 3 ,$y];',
            ],
            // don't change function calls
            [
                '<?php $x = [ 1, "2",getValue(1,2  ,3 ),$y];',
                '<?php $x = [ 1 , "2",getValue(1,2  ,3 )   ,$y];',
            ],
            // don't change function declarations
            [
                '<?php $x = [1, "2", function( $x ,$y) { return $x + $y; }, $y];',
                '<?php $x = [1 , "2", function( $x ,$y) { return $x + $y; }, $y];',
            ],
            // don't change function declarations but change array inside
            [
                '<?php $x = [ 1,  "2","c" => function( $x ,$y) { return [$x, $y]; }, $y];',
                '<?php $x = [ 1 ,  "2","c" => function( $x ,$y) { return [$x , $y]; }, $y];',
            ],
            // don't change anonymous class implements list but change array inside
            [
                '<?php $x = [ 1,  "2","c" => new class implements Foo , Bar { const FOO = ["x", "y"]; }, $y];',
                '<?php $x = [ 1 ,  "2","c" => new class implements Foo , Bar { const FOO = ["x" , "y"]; }, $y];',
            ],
            // associative array (old)
            [
                '<?php $x = array( "a" => $a, "b" =>  "b",3=>$this->foo(), "d" => 30);',
                '<?php $x = array( "a" => $a , "b" =>  "b",3=>$this->foo()  , "d" => 30);',
            ],
            // associative array (short)
            [
                '<?php $x = [  "a" => $a, "b"=>"b",3 => $this->foo(), "d" =>30  ];',
                '<?php $x = [  "a" => $a , "b"=>"b",3 => $this->foo()    , "d" =>30  ];',
            ],
            // nested arrays
            [
                '<?php $x = ["a" => $a, "b" => "b", 3=> [5,6, 7], "d" => array(1, 2,3,4)];',
                '<?php $x = ["a" => $a , "b" => "b", 3=> [5 ,6, 7]  , "d" => array(1, 2,3 ,4)];',
            ],
            // multi line array
            [
                '<?php $x = [  "a" =>$a,
                    "b"=>
                "b",
                    3 => $this->foo(),
                    "d" => 30  ];',
                '<?php $x = [  "a" =>$a ,
                    "b"=>
                "b",
                    3 => $this->foo()  ,
                    "d" => 30  ];',
            ],
            // multi line array
            [
                '<?php $a = [
                            "foo",
                            "bar",
                        ];',
                '<?php $a = [
                            "foo" ,
                            "bar"
                            ,
                        ];',
            ],
            // nested multiline
            [
                '<?php $a = array(array(
                                    array(T_OPEN_TAG),
                                    array(T_VARIABLE, "$x"),
                        ), 1);',
            ],
            [
                '<?php $a = array( // comment
                    123,
                );',
            ],
            [
                "<?php \$x = array(<<<'EOF'
<?php \$a = '\\foo\\bar\\\\';
EOF
                , <<<'EOF'
<?php \$a = \"\\foo\\bar\\\\\";
EOF
                    );",
            ],
            [
                "<?php \$x = array(<<<'EOF'
<?php \$a = '\\foo\\bar\\\\';
EOF, <<<'EOF'
<?php \$a = \"\\foo\\bar\\\\\";
EOF
                    );",
                "<?php \$x = array(<<<'EOF'
<?php \$a = '\\foo\\bar\\\\';
EOF
                , <<<'EOF'
<?php \$a = \"\\foo\\bar\\\\\";
EOF
                    );",
                ['after_heredoc' => true],
            ],
            [
                '<?php $x = array(...$foo, ...$bar);',
                '<?php $x = array(...$foo , ...$bar);',
            ],
            [
                '<?php $x = [...$foo, ...$bar];',
                '<?php $x = [...$foo , ...$bar];',
            ],
        ];
    }
}
