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

namespace PhpCsFixer\Tests\Fixer\ControlStructure;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Nat Zimmermann <nathanielzimmermann@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ControlStructure\NoEmptyBlockFixer
 */
final class NoEmptyBlockFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): iterable
    {
        yield 'if with side effect in body' => [
            '<?php if ($foo) { echo 1; }',
        ];

        yield 'if with side effect in braces' => [
            '<?php ',
            '<?php if ($foo->bar()) {}',
        ];

        yield 'if without side effect' => [
            '<?php ',
            '<?php if ($foo) {}',
        ];

        yield 'if without side effect but comment in body' => [
            '<?php if ($foo) { /* todo */ }',
        ];

        yield 'if without side effect but doc comment in body' => [
            '<?php if ($foo) { /** todo */ }',
        ];

        yield 'if without side effect but # comment in body' => [
            '<?php if ($foo) {
    # @todo
}',
        ];

        yield 'if without side effect but // comment in body' => [
            '<?php if ($foo) {
    // @todo
}',
        ];

        yield 'if without side effect with comments' => [
            '<?php /*1*//*2*//*3*//*4*//*5*//*6*/',
            '<?php /*1*/if/*2*/(/*3*/$foo/*4*/)/*5*/{}/*6*/',
        ];

        yield 'if without side effect with doc comments' => [
            '<?php /**1*//**2*//**3*//**4*//**5*//**6*/',
            '<?php /**1*/if/**2*/(/**3*/$foo/**4*/)/**5*/{}/**6*/',
        ];

        yield 'if with side effect in elseif body' => [
            '<?php if ($foo) {} elseif ($bar) { baz(); }',
        ];

        yield 'if with side effect in elseif braces' => [
            '<?php ',
            '<?php if ($foo) {} elseif ($foo->baz()) {}',
        ];

        yield 'if without side effect and elseif without side effect' => [
            '<?php ',
            '<?php if ($foo) {} elseif ($bar) {}',
        ];

        yield 'if without side effect and elseif without side effect with comments' => [
            '<?php /*1*//*2*//*3*//*4*//*5*//*6*//*7*//*8*//*9*//*10*//*11*/',
            '<?php /*1*/if/*2*/(/*3*/$foo/*4*/)/*5*/{}/*6*/elseif/*7*/(/*8*/$bar/*9*/)/*10*/{}/*11*/',
        ];

        yield 'if without side effect and elseif without side effect with doc comments' => [
            '<?php /**1*//**2*//**3*//**4*//**5*//**6*//**7*//**8*//**9*//**10*//**11*/',
            '<?php /**1*/if/**2*/(/**3*/$foo/**4*/)/**5*/{}/**6*/elseif/**7*/(/**8*/$bar/**9*/)/**10*/{}/**11*/',
        ];

        yield 'elseif without side effect but comment in body' => [
            '<?php if ($foo) {} elseif ($bar) { /* todo */ }',
        ];

        yield 'elseif without side effect but doc comment in body' => [
            '<?php if ($foo) {} elseif ($bar) { /** todo */ }',
        ];

        yield 'multiple elseif without side effect' => [
            '<?php ',
            '<?php if ($foo) {} elseif ($bar) {} elseif ($baz) {} elseif ($boz) {}',
        ];

        yield 'multiple elseif one with side effect' => [
            '<?php if ($foo) {} elseif ($bar) { foo(); } elseif ($baz) {} elseif ($boz) {}',
        ];

        yield 'if with side effect in else' => [
            '<?php if ($foo) {} else { bar(); }',
        ];

        yield 'if with else without side effect' => [
            '<?php ',
            '<?php if ($foo) {} else {}',
        ];

        yield 'if with else without side effect but comment in body' => [
            '<?php if ($foo) {} else { /* todo */ }',
        ];

        yield 'if with else without side effect but doc comment in body' => [
            '<?php if ($foo) {} else { /** todo */ }',
        ];

        yield 'if with else without side effect with comments' => [
            '<?php /*1*//*2*//*3*//*4*//*5*//*6*//*7*//*8*/',
            '<?php /*1*/if/*2*/(/*3*/$foo/*4*/)/*5*/{}/*6*/else/*7*/{}/*8*/',
        ];

        yield 'if with else without side effect with doc comments' => [
            '<?php /**1*//**2*//**3*//**4*//**5*//**6*//**7*//**8*/',
            '<?php /**1*/if/**2*/(/**3*/$foo/**4*/)/**5*/{}/**6*/else/**7*/{}/**8*/',
        ];

        yield 'if with side effect in braces and else without side effect' => [
            '<?php ',
            '<?php if ($foo->bar()) {} else {}',
        ];

        yield 'if with side effect in body and else without side effect' => [
            '<?php if ($foo) { bar(); } ',
            '<?php if ($foo) { bar(); } else {}',
        ];

        yield 'if compact' => [
            '<?php ',
            '<?php if($foo){}elseif($bar){}else{}',
        ];

        yield 'if spaced' => [
            '<?php ',
            '<?php if   (   $foo )  {     }   elseif   (  $bar )   { }',
        ];

        yield 'if nested' => [
            '<?php ',
            '<?php if ($foo) { if ($bar) {} }',
        ];

        yield 'alternative if with side effect in body' => [
            '<?php if ($foo): echo 1; endif;',
        ];

        yield 'alternative if with side effect in braces' => [
            '<?php ',
            '<?php if ($foo->bar()): endif;',
        ];

        yield 'alternative if without side effect' => [
            '<?php ',
            '<?php if ($foo): endif;',
        ];

        yield 'alternative if without side effect but comment in body' => [
            '<?php if ($foo): /* todo */ endif;',
        ];

        yield 'alternative if without side effect but doc comment in body' => [
            '<?php if ($foo): /** todo */ endif;',
        ];

        yield 'alternative if without side effect with comments' => [
            '<?php /*1*//*2*//*3*//*4*//*5*/',
            '<?php /*1*/if/*2*/(/*3*/$foo/*4*/): endif;/*5*/',
        ];

        yield 'alternative if without side effect with doc comments' => [
            '<?php /**1*//**2*//**3*//**4*//**5*/',
            '<?php /**1*/if/**2*/(/**3*/$foo/**4*/): endif;/**5*/',
        ];

        yield 'alternative if with side effect in elseif body' => [
            '<?php if ($foo): elseif ($bar): baz(); endif;',
        ];

        yield 'alternative if with side effect in elseif braces' => [
            '<?php ',
            '<?php if ($foo): elseif ($foo->baz()): endif;',
        ];

        yield 'alternative if without side effect and elseif without side effect' => [
            '<?php ',
            '<?php if ($foo): elseif ($bar): endif;',
        ];

        yield 'alternative if without side effect and elseif without side effect with comments' => [
            '<?php /*1*//*2*//*3*//*4*//*5*//*6*//*7*//*8*/',
            '<?php /*1*/if/*2*/(/*3*/$foo/*4*/): elseif/*5*/(/*6*/$bar/*7*/): endif;/*8*/',
        ];

        yield 'alternative if without side effect and elseif without side effect with doc comments' => [
            '<?php /**1*//**2*//**3*//**4*//**5*//**6*//**7*//**8*/',
            '<?php /**1*/if/**2*/(/**3*/$foo/**4*/): elseif/**5*/(/**6*/$bar/**7*/): endif;/**8*/',
        ];

        yield 'alternative elseif without side effect but comment in body' => [
            '<?php if ($foo): elseif ($bar): /* todo */ endif;',
        ];

        yield 'alternative elseif without side effect but doc comment in body' => [
            '<?php if ($foo): elseif ($bar): /** todo */ endif;',
        ];

        yield 'alternative multiple elseif without side effect' => [
            '<?php ',
            '<?php if ($foo): elseif ($bar): elseif ($baz): elseif ($boz): endif;',
        ];

        yield 'alternative multiple elseif one with side effect' => [
            '<?php if ($foo): elseif ($bar): foo(); elseif ($baz): elseif ($boz): endif;',
        ];

        yield 'alternative if with side effect in else' => [
            '<?php if ($foo): else: bar(); endif;',
        ];

        yield 'alternative if with else without side effect' => [
            '<?php ',
            '<?php if ($foo): else: endif;',
        ];

        yield 'alternative if with else without side effect but comment in body' => [
            '<?php if ($foo): else: /* todo */ endif;',
        ];

        yield 'alternative if with else without side effect but doc comment in body' => [
            '<?php if ($foo): else: /** todo */ endif;',
        ];

        yield 'alternative if with else without side effect with comments' => [
            '<?php /*1*//*2*//*3*//*4*//*5*/',
            '<?php /*1*/if/*2*/(/*3*/$foo/*4*/): else: endif;/*5*/',
        ];

        yield 'alternative if with else without side effect with doc comments' => [
            '<?php /**1*//**2*//**3*//**4*//**5*/',
            '<?php /**1*/if/**2*/(/**3*/$foo/**4*/): else: endif;/**5*/',
        ];

        yield 'alternative if with side effect in braces and else without side effect' => [
            '<?php ',
            '<?php if ($foo->bar()): else: endif;',
        ];

        yield 'alternative if with side effect in body and else without side effect' => [
            '<?php if ($foo): bar();  endif;',
            '<?php if ($foo): bar(); else: endif;',
        ];

        yield 'alternative if compact' => [
            '<?php ',
            '<?php if($foo):elseif($bar):else:endif;',
        ];

        yield 'alternative if spaced' => [
            '<?php ',
            '<?php if   (   $foo ):    elseif   (  $bar ):   endif;',
        ];

        yield 'alternative end tag if with side effect in body' => [
            '<?php if ($foo): echo 1; endif ?>',
        ];

        yield 'alternative end tag if with side effect in braces' => [
            '<?php ?>',
            '<?php if ($foo->bar()): endif ?>',
        ];

        yield 'alternative end tag if without side effect' => [
            '<?php ?>',
            '<?php if ($foo): endif ?>',
        ];

        yield 'alternative end tag if without side effect but comment in body' => [
            '<?php if ($foo): /* todo */ endif ?>',
        ];

        yield 'alternative end tag if without side effect but doc comment in body' => [
            '<?php if ($foo): /** todo */ endif ?>',
        ];

        yield 'alternative end tag if without side effect with comments' => [
            '<?php /*1*//*2*//*3*//*4*/?>',
            '<?php /*1*/if/*2*/(/*3*/$foo/*4*/): endif ?>',
        ];

        yield 'alternative end tag if without side effect with doc comments' => [
            '<?php /**1*//**2*//**3*//**4*/?>',
            '<?php /**1*/if/**2*/(/**3*/$foo/**4*/): endif ?>',
        ];

        yield 'alternative end tag if with side effect in elseif body' => [
            '<?php if ($foo): elseif ($bar): baz(); endif ?>',
        ];

        yield 'alternative end tag if with side effect in elseif braces' => [
            '<?php ?>',
            '<?php if ($foo): elseif ($foo->baz()): endif ?>',
        ];

        yield 'alternative end tag if without side effect and elseif without side effect' => [
            '<?php ?>',
            '<?php if ($foo): elseif ($bar): endif ?>',
        ];

        yield 'alternative end tag if without side effect and elseif without side effect with comments' => [
            '<?php /*1*//*2*//*3*//*4*//*5*//*6*//*7*/?>',
            '<?php /*1*/if/*2*/(/*3*/$foo/*4*/): elseif/*5*/(/*6*/$bar/*7*/): endif ?>',
        ];

        yield 'alternative end tag if without side effect and elseif without side effect with doc comments' => [
            '<?php /**1*//**2*//**3*//**4*//**5*//**6*//**7*/?>',
            '<?php /**1*/if/**2*/(/**3*/$foo/**4*/): elseif/**5*/(/**6*/$bar/**7*/): endif ?>',
        ];

        yield 'alternative end tag elseif without side effect but comment in body' => [
            '<?php if ($foo): elseif ($bar): /* todo */ endif ?>',
        ];

        yield 'alternative end tag elseif without side effect but doc comment in body' => [
            '<?php if ($foo): elseif ($bar): /** todo */ endif ?>',
        ];

        yield 'alternative end tag multiple elseif without side effect' => [
            '<?php ?>',
            '<?php if ($foo): elseif ($bar): elseif ($baz): elseif ($boz): endif ?>',
        ];

        yield 'alternative end tag multiple elseif one with side effect' => [
            '<?php if ($foo): elseif ($bar): foo(); elseif ($baz): elseif ($boz): endif ?>',
        ];

        yield 'alternative end tag if with side effect in else' => [
            '<?php if ($foo): else: bar(); endif ?>',
        ];

        yield 'alternative end tag if with else without side effect' => [
            '<?php ?>',
            '<?php if ($foo): else: endif ?>',
        ];

        yield 'alternative end tag if with else without side effect but comment in body' => [
            '<?php if ($foo): else: /* todo */ endif ?>',
        ];

        yield 'alternative end tag if with else without side effect but doc comment in body' => [
            '<?php if ($foo): else: /** todo */ endif ?>',
        ];

        yield 'alternative end tag if with else without side effect with comments' => [
            '<?php /*1*//*2*//*3*//*4*/?>',
            '<?php /*1*/if/*2*/(/*3*/$foo/*4*/): else: endif ?>',
        ];

        yield 'alternative end tag if with else without side effect with doc comments' => [
            '<?php /**1*//**2*//**3*//**4*/?>',
            '<?php /**1*/if/**2*/(/**3*/$foo/**4*/): else: endif ?>',
        ];

        yield 'alternative end tag if with side effect in braces and else without side effect' => [
            '<?php ?>',
            '<?php if ($foo->bar()): else: endif ?>',
        ];

        yield 'alternative end tag if with side effect in body and else without side effect' => [
            '<?php if ($foo): bar();  endif ?>',
            '<?php if ($foo): bar(); else: endif ?>',
        ];

        yield 'alternative end tag if compact' => [
            '<?php ?>',
            '<?php if($foo):elseif($bar):else:endif ?>',
        ];

        yield 'alternative end tag if spaced' => [
            '<?php ?>',
            '<?php if   (   $foo ):    elseif   (  $bar ):   endif ?>',
        ];

        yield 'do while with side effect in body' => [
            '<?php do { foo(); } while($foo);',
        ];

        yield 'do while with side effect in braces' => [
            '<?php ',
            '<?php do {} while (foo());',
        ];

        yield 'do while without side effects' => [
            '<?php ',
            '<?php do {} while ($foo);',
        ];

        yield 'do while without side effects but comment in body' => [
            '<?php do { /* todo */ } while ($foo);',
        ];

        yield 'do while without side effects but doc comment in body' => [
            '<?php do { /** todo */ } while ($foo);',
        ];

        yield 'do while without side effect with comments' => [
            '<?php /*1*//*2*//*3*//*4*//*5*//*6*//*7*//*8*/',
            '<?php /*1*/do/*2*/{}/*3*/while/*4*/(/*5*/$foo/*6*/)/*7*/;/*8*/',
        ];

        yield 'do while without side effect with doc comments' => [
            '<?php /**1*//**2*//**3*//**4*//**5*//**6*//**7*//**8*/',
            '<?php /**1*/do/**2*/{}/**3*/while/**4*/(/**5*/$foo/**6*/)/**7*/;/**8*/',
        ];

        yield 'do while nested' => [
            '<?php ',
            '<?php do { do {} while ($foo); } while ($bar);',
        ];

        yield 'do while end tag with side effect in body' => [
            '<?php do { foo(); } while($foo) ?>',
        ];

        yield 'do while end tag with side effect in braces' => [
            '<?php ?>',
            '<?php do {} while (foo()) ?>',
        ];

        yield 'do while end tag without side effects' => [
            '<?php ?>',
            '<?php do {} while ($foo) ?>',
        ];

        yield 'do while end tag without side effects but comment in body' => [
            '<?php do { /* todo */ } while ($foo) ?>',
        ];

        yield 'do while end tag without side effects but doc comment in body' => [
            '<?php do { /** todo */ } while ($foo) ?>',
        ];

        yield 'do while end tag without side effect with comments' => [
            '<?php /*1*//*2*//*3*//*4*//*5*//*6*//*7*//*8*/ ?>',
            '<?php /*1*/do/*2*/{}/*3*/while/*4*/(/*5*/$foo/*6*/)/*7*/;/*8*/ ?>',
        ];

        yield 'do while end tag without side effect with doc comments' => [
            '<?php /**1*//**2*//**3*//**4*//**5*//**6*//**7*//**8*/ ?>',
            '<?php /**1*/do/**2*/{}/**3*/while/**4*/(/**5*/$foo/**6*/)/**7*/;/**8*/ ?>',
        ];

        yield 'for with side effect in body' => [
            '<?php for (;;) { foo(); }',
        ];

        yield 'for with side effect in braces' => [
            '<?php ',
            '<?php for ($i = 0; $i < count($foo); ++$i) {}',
        ];

        yield 'for without side effects' => [
            '<?php ',
            '<?php for (;;) {}',
        ];

        yield 'for without side effects but comment in body' => [
            '<?php for (;;) { /* todo */ }',
        ];

        yield 'for without side effects but doc comment in body' => [
            '<?php for (;;) { /** todo */ }',
        ];

        yield 'for without side effect with comments' => [
            '<?php /*1*//*2*//*3*//*4*//*5*//*6*//*7*/',
            '<?php /*1*/for/*2*/(/*3*/;/*4*/;/*5*/)/*6*/{}/*7*/',
        ];

        yield 'for without side effect with doc comments' => [
            '<?php /**1*//**2*//**3*//**4*//**5*//**6*//**7*/',
            '<?php /**1*/for/**2*/(/**3*/;/**4*/;/**5*/)/**6*/{}/**7*/',
        ];

        yield 'for nested' => [
            '<?php ',
            '<?php for (;;) { for(;;) {} }',
        ];

        yield 'alternative for with side effect in body' => [
            '<?php for (;;): foo(); endfor;',
        ];

        yield 'alternative for with side effect in braces' => [
            '<?php ',
            '<?php for ($i = 0; $i < count($foo); ++$i): endfor;',
        ];

        yield 'alternative for without side effects' => [
            '<?php ',
            '<?php for (;;): endfor;',
        ];

        yield 'alternative for without side effects but comment in body' => [
            '<?php for (;;): /* todo */ endfor;',
        ];

        yield 'alternative for without side effects but doc comment in body' => [
            '<?php for (;;): /** todo */ endfor;',
        ];

        yield 'alternative for without side effect with comments' => [
            '<?php /*1*//*2*//*3*//*4*//*5*//*6*//*7*/',
            '<?php /*1*/for/*2*/(/*3*/;/*4*/;/*5*/)/*6*/: endfor;/*7*/',
        ];

        yield 'alternative for without side effect with doc comments' => [
            '<?php /**1*//**2*//**3*//**4*//**5*//**6*//**7*/',
            '<?php /**1*/for/**2*/(/**3*/;/**4*/;/**5*/)/**6*/: endfor;/**7*/',
        ];

        yield 'alternative for end tag with side effect in body' => [
            '<?php for (;;): foo(); endfor ?>',
        ];

        yield 'alternative for end tag with side effect in braces' => [
            '<?php ?>',
            '<?php for ($i = 0; $i < count($foo); ++$i): endfor ?>',
        ];

        yield 'alternative for end tag without side effects' => [
            '<?php ?>',
            '<?php for (;;): endfor ?>',
        ];

        yield 'alternative for end tag without side effects but comment in body' => [
            '<?php for (;;): /* todo */ endfor ?>',
        ];

        yield 'alternative for end tag without side effects but doc comment in body' => [
            '<?php for (;;): /** todo */ endfor ?>',
        ];

        yield 'alternative for end tag without side effect with comments' => [
            '<?php /*1*//*2*//*3*//*4*//*5*//*6*//*7*/ ?>',
            '<?php /*1*/for/*2*/(/*3*/;/*4*/;/*5*/)/*6*/: endfor;/*7*/ ?>',
        ];

        yield 'alternative for end tag without side effect with doc comments' => [
            '<?php /**1*//**2*//**3*//**4*//**5*//**6*//**7*/ ?>',
            '<?php /**1*/for/**2*/(/**3*/;/**4*/;/**5*/)/**6*/: endfor;/**7*/ ?>',
        ];

        yield 'switch with side effect in body' => [
            '<?php switch ($foo) { case 1: foo(); }',
        ];

        yield 'switch with side effect in braces' => [
            '<?php ',
            '<?php switch ($foo->bar()) {}',
        ];

        yield 'switch without side effects' => [
            '<?php ',
            '<?php switch ($foo) {}',
        ];

        yield 'switch without side effects with comments' => [
            '<?php /*1*//*2*//*3*//*4*//*5*//*6*/',
            '<?php /*1*/switch/*2*/(/*3*/$foo/*4*/)/*5*/{}/*6*/',
        ];

        yield 'switch without side effects with doc comments' => [
            '<?php /**1*//**2*//**3*//**4*//**5*//**6*/',
            '<?php /**1*/switch/**2*/(/**3*/$foo/**4*/)/**5*/{}/**6*/',
        ];

        yield 'switch without side effects but comment in body' => [
            '<?php switch ($foo) { /* todo */ }',
        ];

        yield 'switch without side effects but doc comment in body' => [
            '<?php switch ($foo) { /** todo */ }',
        ];

        yield 'alternative switch with side effect in body' => [
            '<?php switch ($foo): case 1: foo(); endswitch;',
        ];

        yield 'alternative switch with side effect in braces' => [
            '<?php ',
            '<?php switch ($foo->bar()): endswitch;',
        ];

        yield 'alternative switch without side effects' => [
            '<?php ',
            '<?php switch ($foo): endswitch;',
        ];

        yield 'alternative switch without side effects with comments' => [
            '<?php /*1*//*2*//*3*//*4*//*5*//*6*//*7*/',
            '<?php /*1*/switch/*2*/(/*3*/$foo/*4*/)/*5*/: endswitch/*6*/;/*7*/',
        ];

        yield 'alternative switch without side effects with doc comments' => [
            '<?php /**1*//**2*//**3*//**4*//**5*//**6*//**7*/',
            '<?php /**1*/switch/**2*/(/**3*/$foo/**4*/)/**5*/: endswitch/**6*/;/**7*/',
        ];

        yield 'alternative switch without side effects but comment in body' => [
            '<?php switch ($foo): /* todo */ endswitch;',
        ];

        yield 'alternative switch without side effects but doc comment in body' => [
            '<?php switch ($foo): /** todo */ endswitch;',
        ];

        yield 'alternative end tag switch with side effect in body' => [
            '<?php switch ($foo): case 1: foo(); endswitch ?>',
        ];

        yield 'alternative end tag switch with side effect in braces' => [
            '<?php ?>',
            '<?php switch ($foo->bar()): endswitch ?>',
        ];

        yield 'alternative end tag switch without side effects' => [
            '<?php ?>',
            '<?php switch ($foo): endswitch ?>',
        ];

        yield 'alternative end tag switch without side effects with comments' => [
            '<?php /*1*//*2*//*3*//*4*//*5*//*6*/ ?>',
            '<?php /*1*/switch/*2*/(/*3*/$foo/*4*/)/*5*/: endswitch/*6*/ ?>',
        ];

        yield 'alternative end tag switch without side effects with doc comments' => [
            '<?php /**1*//**2*//**3*//**4*//**5*//**6*/ ?>',
            '<?php /**1*/switch/**2*/(/**3*/$foo/**4*/)/**5*/: endswitch/**6*/ ?>',
        ];

        yield 'alternative end tag switch without side effects but comment in body' => [
            '<?php switch ($foo): /* todo */ endswitch ?>',
        ];

        yield 'alternative end tag switch without side effects but doc comment in body' => [
            '<?php switch ($foo): /** todo */ endswitch ?>',
        ];

        yield 'try with side effects' => [
            '<?php try { foo(); } catch (Exception $e) {}',
        ];

        yield 'try with side effects in catch' => [
            '<?php ',
            '<?php try {} catch (Exception $e) { foo(); }',
        ];

        yield 'try without side effects in catch' => [
            '<?php ',
            '<?php try {} catch (Exception $e) {}',
        ];

        yield 'try without side effects and finally with side effects' => [
            '<?php try {} finally { foo(); }',
        ];

        yield 'try and finally without side effects' => [
            '<?php ',
            '<?php try {} finally {}',
        ];

        yield 'try catch and finally without side effects' => [
            '<?php ',
            '<?php try {} catch (Exception $e) {} finally {}',
        ];

        yield 'try nested' => [
            '<?php ',
            '<?php try { try {} catch (Exception $e) {} } catch (Exception $e) {}',
        ];

        yield 'while with side effect in body' => [
            '<?php while ($foo) { foo(); }',
        ];

        yield 'while with side effect in braces' => [
            '<?php ',
            '<?php while (foo()) {}',
        ];

        yield 'while without side effects' => [
            '<?php ',
            '<?php while ($foo) {}',
        ];

        yield 'while without side effects but comment in body' => [
            '<?php while ($foo) { /* todo */ }',
        ];

        yield 'while without side effects but doc comment in body' => [
            '<?php while ($foo) { /** todo */ }',
        ];

        yield 'while without side effect with comments' => [
            '<?php /*1*//*2*//*3*//*4*//*5*//*6*/',
            '<?php /*1*/while/*2*/(/*3*/$foo/*4*/)/*5*/{}/*6*/',
        ];

        yield 'while without side effect with doc comments' => [
            '<?php /**1*//**2*//**3*//**4*//**5*//**6*/',
            '<?php /**1*/while/**2*/(/**3*/$foo/**4*/)/**5*/{}/**6*/',
        ];

        yield 'while nested' => [
            '<?php ',
            '<?php while ($bar) { while ($foo) {} }',
        ];

        yield 'while end tag with side effect in body' => [
            '<?php while($foo) { foo(); } ?>',
        ];

        yield 'while end tag with side effect in braces' => [
            '<?php ?>',
            '<?php while (foo()) {} ?>',
        ];

        yield 'while end tag without side effects' => [
            '<?php ?>',
            '<?php while ($foo) {} ?>',
        ];

        yield 'while end tag without side effects but comment in body' => [
            '<?php while ($foo) { /* todo */ } ?>',
        ];

        yield 'while end tag without side effects but doc comment in body' => [
            '<?php while ($foo) { /** todo */ } ?>',
        ];

        yield 'while end tag without side effect with comments' => [
            '<?php /*1*//*2*//*3*//*4*//*5*//*6*/ ?>',
            '<?php /*1*/while/*2*/(/*3*/$foo/*4*/)/*5*/{}/*6*/ ?>',
        ];

        yield 'while end tag without side effect with doc comments' => [
            '<?php /**1*//**2*//**3*//**4*//**5*//**6*/ ?>',
            '<?php /**1*/while/**2*/(/**3*/$foo/**4*/)/**5*/{}/**6*/ ?>',
        ];

        yield 'general test 0' => [
            '<?php

namespace Foo;

class Bar
{
    public function baz($bop): string
    {
        if (!$bop) {
            # todo
        } }
}
',
            '<?php

namespace Foo;

class Bar
{
    public function baz($bop): string
    {
        if (!$bop) {
            # todo
        } else {
        }
        try {
            while ($foo) {}
        } catch (\\Throwable $e) {
        } finally {
        }
        for ($i = 0; $i < strlen($bop); ++$i) {
            do {
                switch ($faz) {}
            } while ($bop);
        }
    }
}
',
        ];
    }
}
