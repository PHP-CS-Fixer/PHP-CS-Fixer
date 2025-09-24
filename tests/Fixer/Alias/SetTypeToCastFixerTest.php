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
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\Alias\SetTypeToCastFixer>
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

    /**
     * @return iterable<string, array{0: string, 1?: string}>
     */
    public static function provideFixCases(): iterable
    {
        yield 'null cast' => [
            '<?php $foo = null;',
            '<?php settype($foo, "null");',
        ];

        yield 'null cast comments' => [
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
        ];

        yield 'array + spacing' => [
            '<?php $foo = (array) $foo;',
            '<?php settype  (  $foo  , \'array\');',
        ];

        yield 'bool + casing' => [
            '<?php $foo = (bool) $foo;',
            '<?php settype  (  $foo  , "Bool");',
        ];

        yield 'boolean with extra space' => [
            '<?php $foo = (bool) $foo;',
            '<?php settype  (  $foo  , "boolean");',
        ];

        yield 'double' => [
            '<?php $foo = (float) $foo;',
            '<?php settype($foo, "double");',
        ];

        yield 'float' => [
            '<?php $foo = (float) $foo;',
            '<?php settype($foo, "float");',
        ];

        yield 'float in loop' => [
            '<?php while(a()){$foo = (float) $foo;}',
            '<?php while(a()){settype($foo, "float");}',
        ];

        yield 'int full caps' => [
            '<?php $foo = (int) $foo;',
            '<?php settype($foo, "INT");',
        ];

        yield 'integer (simple)' => [
            '<?php $foo = (int) $foo;',
            '<?php settype($foo, "integer");',
        ];

        yield 'object' => [
            '<?php echo 1; $foo = (object) $foo;',
            '<?php echo 1; settype($foo, "object");',
        ];

        yield 'string' => [
            '<?php $foo = (string) $foo;',
            '<?php settype($foo, "string");',
        ];

        yield 'string in function body' => [
            '<?php function A(){ $foo = (string) $foo; return $foo; }',
            '<?php function A(){ settype($foo, "string"); return $foo; }',
        ];

        yield 'integer + no space' => [
            '<?php $foo = (int) $foo;',
            '<?php settype($foo,"integer");',
        ];

        yield 'no space comments' => [
            '<?php /*0*//*1*/$foo = (int) $foo/*2*//*3*//*4*//*5*//*6*//*7*/;/*8*/',
            '<?php /*0*//*1*/settype/*2*/(/*3*/$foo/*4*/,/*5*/"integer"/*6*/)/*7*/;/*8*/',
        ];

        yield 'comments with line breaks' => [
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
        ];

        // do not fix cases
        yield 'first argument is not a variable' => [
            '<?php
                    namespace A\B;             // comment
                    function settype($a, $b){} // "

                    settype(1, "double");
                ',
        ];

        yield 'first argument is variable followed by operation' => [
            '<?php
                    namespace A\B;                // comment
                    function settype($a, $b){}    // "

                    settype($foo + 1, "integer"); // function must be overridden, so do not fix it
                ',
        ];

        yield 'wrong numbers of arguments' => [
            '<?php settype($foo, "integer", $a);',
        ];

        yield 'other namespace I' => [
            '<?php a\b\settype($foo, "integer", $a);',
        ];

        yield 'other namespace II' => [
            '<?php \b\settype($foo, "integer", $a);',
        ];

        yield 'static call' => [
            '<?php A::settype($foo, "integer");',
        ];

        yield 'member call' => [
            '<?php $a->settype($foo, "integer");',
        ];

        yield 'unknown type' => [
            '<?php $a->settype($foo, "foo");',
        ];

        yield 'return value used I' => [
            '<?php $a = settype($foo, "integer");',
        ];

        yield 'return value used II' => [
            '<?php a(settype($foo, "integer"));',
        ];

        yield 'return value used III' => [
            '<?php $a = "123"; $b = [settype($a, "integer")];',
        ];

        yield 'return value used IV' => [
            '<?php $a = "123"; $b = [3 => settype($a, "integer")];',
        ];

        yield 'return value used V' => [
            '<?= settype($foo, "object");',
        ];

        yield 'wrapped statements, fixable after removing the useless parenthesis brace' => [
            '<?php
                    settype(/*1*//*2*/($a), \'int\');
                    settype($b, (\'int\'));
                    settype(($c), (\'int\'));
                    settype((($d)), ((\'int\')));
                ',
        ];

        yield 'wrapped statements, not-fixable, even after removing the useless parenthesis brace' => [
            '<?php
                    namespace A\B;                // comment
                    function settype($a, $b){}    // "

                    settype($foo1, (("integer")."1"));
                    settype($foo2, ("1".("integer")));
                    settype($foo3, ((("integer")."1")));
                    settype((($foo)+1), "integer");
                ',
        ];

        yield 'nested is not an issue for this fixer, as these non may be fixed' => [
            '<?php
                    settype($foo, settype($foo, "double"));
                    settype(settype($foo, "double"), "double");
                ',
        ];

        yield 'unknown type II' => [
            '<?php settype($foo, "stringX");',
        ];

        yield 'boolean' => [
            '<?php $foo = (bool) $foo;',
            '<?php settype($foo, "boolean", );',
        ];

        yield 'comments with line breaks II' => [
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
        ];
    }
}
