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
    public function testFix(string $expected, ?string $input = null, array $config = []): void
    {
        $this->fixer->configure($config);

        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): iterable
    {
        // old style array
        yield [
            '<?php $x = array(1, "2",3);',
            '<?php $x = array(1 , "2",3);',
        ];

        // old style array with comments
        yield [
            '<?php $x = array /* comment */ (1,  "2", 3);',
            '<?php $x = array /* comment */ (1  ,  "2", 3);',
        ];

        // old style array with comments
        yield [
            '<?php $x = array(1#
,#
"2", 3);',
            '<?php $x = array(1#
,#
"2"  , 3);',
        ];

        // short array
        yield [
            '<?php $x = [1,  "2", 3,$y];',
            '<?php $x = [1 ,  "2", 3 ,$y];',
        ];

        // don't change function calls
        yield [
            '<?php $x = [ 1, "2",getValue(1,2  ,3 ),$y];',
            '<?php $x = [ 1 , "2",getValue(1,2  ,3 )   ,$y];',
        ];

        // don't change function declarations
        yield [
            '<?php $x = [1, "2", function( $x ,$y) { return $x + $y; }, $y];',
            '<?php $x = [1 , "2", function( $x ,$y) { return $x + $y; }, $y];',
        ];

        // don't change function declarations but change array inside
        yield [
            '<?php $x = [ 1,  "2","c" => function( $x ,$y) { return [$x, $y]; }, $y];',
            '<?php $x = [ 1 ,  "2","c" => function( $x ,$y) { return [$x , $y]; }, $y];',
        ];

        // don't change anonymous class implements list but change array inside
        yield [
            '<?php $x = [ 1,  "2","c" => new class implements Foo , Bar { const FOO = ["x", "y"]; }, $y];',
            '<?php $x = [ 1 ,  "2","c" => new class implements Foo , Bar { const FOO = ["x" , "y"]; }, $y];',
        ];

        // associative array (old)
        yield [
            '<?php $x = array( "a" => $a, "b" =>  "b",3=>$this->foo(), "d" => 30);',
            '<?php $x = array( "a" => $a , "b" =>  "b",3=>$this->foo()  , "d" => 30);',
        ];

        // associative array (short)
        yield [
            '<?php $x = [  "a" => $a, "b"=>"b",3 => $this->foo(), "d" =>30  ];',
            '<?php $x = [  "a" => $a , "b"=>"b",3 => $this->foo()    , "d" =>30  ];',
        ];

        // nested arrays
        yield [
            '<?php $x = ["a" => $a, "b" => "b", 3=> [5,6, 7], "d" => array(1, 2,3,4)];',
            '<?php $x = ["a" => $a , "b" => "b", 3=> [5 ,6, 7]  , "d" => array(1, 2,3 ,4)];',
        ];

        // multi line array
        yield [
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
        ];

        // multi line array
        yield [
            '<?php $a = [
                            "foo",
                            "bar",
                        ];',
            '<?php $a = [
                            "foo" ,
                            "bar"
                            ,
                        ];',
        ];

        // nested multiline
        yield [
            '<?php $a = array(array(
                                    array(T_OPEN_TAG),
                                    array(T_VARIABLE, "$x"),
                        ), 1);',
        ];

        yield [
            '<?php $a = array( // comment
                    123,
                );',
        ];

        yield [
            "<?php \$x = array(<<<'EOF'
<?php \$a = '\\foo\\bar\\\\';
EOF
                , <<<'EOF'
<?php \$a = \"\\foo\\bar\\\\\";
EOF
                    );",
        ];

        yield [
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
        ];

        yield [
            '<?php $x = array(...$foo, ...$bar);',
            '<?php $x = array(...$foo , ...$bar);',
        ];

        yield [
            '<?php $x = [...$foo, ...$bar];',
            '<?php $x = [...$foo , ...$bar];',
        ];
    }
}
