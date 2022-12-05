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

namespace PhpCsFixer\Tests\Fixer\Alias;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Alias\SetTypeToCastFixer
 */
final class SetTypeToCastFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): array
    {
        return [
            'null cast' => [
                '<?php $foo = null;',
                '<?php settype($foo, "null");',
            ],
            'null cast comments' => [
                '<?php
# 0
$foo = null# 1
# 2
# 3
# 4
# 5
# 6
# 7
# 8
# 9
# 10
;',
                '<?php
# 0
settype# 1
# 2
(# 3
# 4
$foo# 5
# 6
,# 7
# 8
"null"# 9
)# 10
;',
            ],
            'array + spacing' => [
                '<?php $foo = (array) $foo;',
                '<?php settype  (  $foo  , \'array\');',
            ],
            'bool + casing' => [
                '<?php $foo = (bool) $foo;',
                '<?php settype  (  $foo  , "Bool");',
            ],
            'boolean with extra space' => [
                '<?php $foo = (bool) $foo;',
                '<?php settype  (  $foo  , "boolean");',
            ],
            'double' => [
                '<?php $foo = (float) $foo;',
                '<?php settype($foo, "double");',
            ],
            'float' => [
                '<?php $foo = (float) $foo;',
                '<?php settype($foo, "float");',
            ],
            'float in loop' => [
                '<?php while(a()){$foo = (float) $foo;}',
                '<?php while(a()){settype($foo, "float");}',
            ],
            'int full caps' => [
                '<?php $foo = (int) $foo;',
                '<?php settype($foo, "INT");',
            ],
            'integer (simple)' => [
                '<?php $foo = (int) $foo;',
                '<?php settype($foo, "integer");',
            ],
            'object' => [
                '<?php echo 1; $foo = (object) $foo;',
                '<?php echo 1; settype($foo, "object");',
            ],
            'string' => [
                '<?php $foo = (string) $foo;',
                '<?php settype($foo, "string");',
            ],
            'string in function body' => [
                '<?php function A(){ $foo = (string) $foo; return $foo; }',
                '<?php function A(){ settype($foo, "string"); return $foo; }',
            ],
            'integer + no space' => [
                '<?php $foo = (int) $foo;',
                '<?php settype($foo,"integer");',
            ],
            'no space comments' => [
                '<?php /*0*//*1*/$foo = (int) $foo/*2*//*3*//*4*//*5*//*6*//*7*/;/*8*/',
                '<?php /*0*//*1*/settype/*2*/(/*3*/$foo/*4*/,/*5*/"integer"/*6*/)/*7*/;/*8*/',
            ],
            'comments with line breaks' => [
                '<?php #0
#1
$foo = (int) $foo#2
#3
#4
#5
#6
#7
#8
;#9',
                '<?php #0
#1
settype#2
#3
(#4
$foo#5
,#6
"integer"#7
)#8
;#9',
            ],
            // do not fix cases
            'first argument is not a variable' => [
                '<?php
                    namespace A\B;             // comment
                    function settype($a, $b){} // "

                    settype(1, "double");
                ',
            ],
            'first argument is variable followed by operation' => [
                '<?php
                    namespace A\B;                // comment
                    function settype($a, $b){}    // "

                    settype($foo + 1, "integer"); // function must be overridden, so do not fix it
                ',
            ],
            'wrong numbers of arguments' => [
                '<?php settype($foo, "integer", $a);',
            ],
            'other namespace I' => [
                '<?php a\b\settype($foo, "integer", $a);',
            ],
            'other namespace II' => [
                '<?php \b\settype($foo, "integer", $a);',
            ],
            'static call' => [
                '<?php A::settype($foo, "integer");',
            ],
            'member call' => [
                '<?php $a->settype($foo, "integer");',
            ],
            'unknown type' => [
                '<?php $a->settype($foo, "foo");',
            ],
            'return value used I' => [
                '<?php $a = settype($foo, "integer");',
            ],
            'return value used II' => [
                '<?php a(settype($foo, "integer"));',
            ],
            'return value used III' => [
                '<?php $a = "123"; $b = [settype($a, "integer")];',
            ],
            'return value used IV' => [
                '<?php $a = "123"; $b = [3 => settype($a, "integer")];',
            ],
            'return value used V' => [
                '<?= settype($foo, "object");',
            ],
            'wrapped statements, fixable after removing the useless parenthesis brace' => [
                '<?php
                    settype(/*1*//*2*/($a), \'int\');
                    settype($b, (\'int\'));
                    settype(($c), (\'int\'));
                    settype((($d)), ((\'int\')));
                ',
            ],
            'wrapped statements, not-fixable, even after removing the useless parenthesis brace' => [
                '<?php
                    namespace A\B;                // comment
                    function settype($a, $b){}    // "

                    settype($foo1, (("integer")."1"));
                    settype($foo2, ("1".("integer")));
                    settype($foo3, ((("integer")."1")));
                    settype((($foo)+1), "integer");
                ',
            ],
            'nested is not an issue for this fixer, as these non may be fixed' => [
                '<?php
                    settype($foo, settype($foo, "double"));
                    settype(settype($foo, "double"), "double");
                ',
            ],
            'unknown type II' => [
                '<?php settype($foo, "stringX");',
            ],
            'boolean' => [
                '<?php $foo = (bool) $foo;',
                '<?php settype($foo, "boolean", );',
            ],
            'comments with line breaks II' => [
                '<?php #0
#1
$foo = (int) $foo#2
#3
#4
#5
#6
#7
#8
#9
;#10',
                '<?php #0
#1
settype#2
#3
(#4
$foo#5
,#6
"integer"#7
,#8
)#9
;#10',
            ],
        ];
    }
}
