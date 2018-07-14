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

namespace PhpCsFixer\Tests\Fixer\ControlStructure;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author ntzm
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
     * @dataProvider provideTestFixCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideTestFixCases()
    {
        return [
            'if with side effect in body' => [
                '<?php if ($foo) { echo 1; }',
            ],
            'if with side effect in braces' => [
                '<?php ',
                '<?php if ($foo->bar()) {}',
            ],
            'if without side effect' => [
                '<?php ',
                '<?php if ($foo) {}',
            ],
            'if without side effect but comment in body' => [
                '<?php if ($foo) { /* todo */ }',
            ],
            'if without side effect but doc comment in body' => [
                '<?php if ($foo) { /** todo */ }',
            ],
            'if without side effect with comments' => [
                '<?php /*1*//*2*//*3*//*4*//*5*//*6*/',
                '<?php /*1*/if/*2*/(/*3*/$foo/*4*/)/*5*/{}/*6*/',
            ],
            'if without side effect with doc comments' => [
                '<?php /**1*//**2*//**3*//**4*//**5*//**6*/',
                '<?php /**1*/if/**2*/(/**3*/$foo/**4*/)/**5*/{}/**6*/',
            ],
            'if with side effect in elseif body' => [
                '<?php if ($foo) {} elseif ($bar) { baz(); }',
            ],
            'if with side effect in elseif braces' => [
                '<?php ',
                '<?php if ($foo) {} elseif ($foo->baz()) {}',
            ],
            'if without side effect and elseif without side effect' => [
                '<?php ',
                '<?php if ($foo) {} elseif ($bar) {}',
            ],
            'if without side effect and elseif without side effect with comments' => [
                '<?php /*1*//*2*//*3*//*4*//*5*//*6*//*7*//*8*//*9*//*10*//*11*/',
                '<?php /*1*/if/*2*/(/*3*/$foo/*4*/)/*5*/{}/*6*/elseif/*7*/(/*8*/$bar/*9*/)/*10*/{}/*11*/',
            ],
            'if without side effect and elseif without side effect with doc comments' => [
                '<?php /**1*//**2*//**3*//**4*//**5*//**6*//**7*//**8*//**9*//**10*//**11*/',
                '<?php /**1*/if/**2*/(/**3*/$foo/**4*/)/**5*/{}/**6*/elseif/**7*/(/**8*/$bar/**9*/)/**10*/{}/**11*/',
            ],
            'elseif without side effect but comment in body' => [
                '<?php if ($foo) {} elseif ($bar) { /* todo */ }',
            ],
            'elseif without side effect but doc comment in body' => [
                '<?php if ($foo) {} elseif ($bar) { /** todo */ }',
            ],
            'multiple elseif without side effect' => [
                '<?php ',
                '<?php if ($foo) {} elseif ($bar) {} elseif ($baz) {} elseif ($boz) {}',
            ],
            'multiple elseif one with side effect' => [
                '<?php if ($foo) {} elseif ($bar) { foo(); } elseif ($baz) {} elseif ($boz) {}',
            ],
            'if with side effect in else' => [
                '<?php if ($foo) {} else { bar(); }',
            ],
            'if with else without side effect' => [
                '<?php ',
                '<?php if ($foo) {} else {}',
            ],
            'if with else without side effect but comment in body' => [
                '<?php if ($foo) {} else { /* todo */ }',
            ],
            'if with else without side effect but doc comment in body' => [
                '<?php if ($foo) {} else { /** todo */ }',
            ],
            'if with else without side effect with comments' => [
                '<?php /*1*//*2*//*3*//*4*//*5*//*6*//*7*//*8*/',
                '<?php /*1*/if/*2*/(/*3*/$foo/*4*/)/*5*/{}/*6*/else/*7*/{}/*8*/',
            ],
            'if with else without side effect with doc comments' => [
                '<?php /**1*//**2*//**3*//**4*//**5*//**6*//**7*//**8*/',
                '<?php /**1*/if/**2*/(/**3*/$foo/**4*/)/**5*/{}/**6*/else/**7*/{}/**8*/',
            ],
            'if with side effect in braces and else without side effect' => [
                '<?php ',
                '<?php if ($foo->bar()) {} else {}',
            ],
            'if with side effect in body and else without side effect' => [
                '<?php if ($foo) { bar(); } ',
                '<?php if ($foo) { bar(); } else {}',
            ],
            'if compact' => [
                '<?php ',
                '<?php if($foo){}elseif($bar){}else{}',
            ],
            'if spaced' => [
                '<?php ',
                '<?php if   (   $foo )  {     }   elseif   (  $bar )   { }',
            ],
            'alternate if with side effect in body' => [
                '<?php if ($foo): echo 1; endif;',
            ],
            'alternate if with side effect in braces' => [
                '<?php ',
                '<?php if ($foo->bar()): endif;',
            ],
            'alternate if without side effect' => [
                '<?php ',
                '<?php if ($foo): endif;',
            ],
            'alternate if without side effect but comment in body' => [
                '<?php if ($foo): /* todo */ endif;',
            ],
            'alternate if without side effect but doc comment in body' => [
                '<?php if ($foo): /** todo */ endif;',
            ],
            'alternate if without side effect with comments' => [
                '<?php /*1*//*2*//*3*//*4*//*5*/',
                '<?php /*1*/if/*2*/(/*3*/$foo/*4*/): endif;/*5*/',
            ],
            'alternate if without side effect with doc comments' => [
                '<?php /**1*//**2*//**3*//**4*//**5*/',
                '<?php /**1*/if/**2*/(/**3*/$foo/**4*/): endif;/**5*/',
            ],
            'alternate if with side effect in elseif body' => [
                '<?php if ($foo): elseif ($bar): baz(); endif;',
            ],
            'alternate if with side effect in elseif braces' => [
                '<?php ',
                '<?php if ($foo): elseif ($foo->baz()): endif;',
            ],
            'alternate if without side effect and elseif without side effect' => [
                '<?php ',
                '<?php if ($foo): elseif ($bar): endif;',
            ],
            'alternate if without side effect and elseif without side effect with comments' => [
                '<?php /*1*//*2*//*3*//*4*//*5*//*6*//*7*//*8*/',
                '<?php /*1*/if/*2*/(/*3*/$foo/*4*/): elseif/*5*/(/*6*/$bar/*7*/): endif;/*8*/',
            ],
            'alternate if without side effect and elseif without side effect with doc comments' => [
                '<?php /**1*//**2*//**3*//**4*//**5*//**6*//**7*//**8*/',
                '<?php /**1*/if/**2*/(/**3*/$foo/**4*/): elseif/**5*/(/**6*/$bar/**7*/): endif;/**8*/',
            ],
            'alternate elseif without side effect but comment in body' => [
                '<?php if ($foo): elseif ($bar): /* todo */ endif;',
            ],
            'alternate elseif without side effect but doc comment in body' => [
                '<?php if ($foo): elseif ($bar): /** todo */ endif;',
            ],
            'alternate multiple elseif without side effect' => [
                '<?php ',
                '<?php if ($foo): elseif ($bar): elseif ($baz): elseif ($boz): endif;',
            ],
            'alternate multiple elseif one with side effect' => [
                '<?php if ($foo): elseif ($bar): foo(); elseif ($baz): elseif ($boz): endif;',
            ],
            'alternate if with side effect in else' => [
                '<?php if ($foo): else: bar(); endif;',
            ],
            'alternate if with else without side effect' => [
                '<?php ',
                '<?php if ($foo): else: endif;',
            ],
            'alternate if with else without side effect but comment in body' => [
                '<?php if ($foo): else: /* todo */ endif;',
            ],
            'alternate if with else without side effect but doc comment in body' => [
                '<?php if ($foo): else: /** todo */ endif;',
            ],
            'alternate if with else without side effect with comments' => [
                '<?php /*1*//*2*//*3*//*4*//*5*/',
                '<?php /*1*/if/*2*/(/*3*/$foo/*4*/): else: endif;/*5*/',
            ],
            'alternate if with else without side effect with doc comments' => [
                '<?php /**1*//**2*//**3*//**4*//**5*/',
                '<?php /**1*/if/**2*/(/**3*/$foo/**4*/): else: endif;/**5*/',
            ],
            'alternate if with side effect in braces and else without side effect' => [
                '<?php ',
                '<?php if ($foo->bar()): else: endif;',
            ],
            'alternate if with side effect in body and else without side effect' => [
                '<?php if ($foo): bar();  endif;',
                '<?php if ($foo): bar(); else: endif;',
            ],
            'alternate if compact' => [
                '<?php ',
                '<?php if($foo):elseif($bar):else:endif;',
            ],
            'alternate if spaced' => [
                '<?php ',
                '<?php if   (   $foo ):    elseif   (  $bar ):   endif;',
            ],
            'alternate end tag if with side effect in body' => [
                '<?php if ($foo): echo 1; endif ?>',
            ],
            'alternate end tag if with side effect in braces' => [
                '<?php ?>',
                '<?php if ($foo->bar()): endif ?>',
            ],
            'alternate end tag if without side effect' => [
                '<?php ?>',
                '<?php if ($foo): endif ?>',
            ],
            'alternate end tag if without side effect but comment in body' => [
                '<?php if ($foo): /* todo */ endif ?>',
            ],
            'alternate end tag if without side effect but doc comment in body' => [
                '<?php if ($foo): /** todo */ endif ?>',
            ],
            'alternate end tag if without side effect with comments' => [
                '<?php /*1*//*2*//*3*//*4*/?>',
                '<?php /*1*/if/*2*/(/*3*/$foo/*4*/): endif ?>',
            ],
            'alternate end tag if without side effect with doc comments' => [
                '<?php /**1*//**2*//**3*//**4*/?>',
                '<?php /**1*/if/**2*/(/**3*/$foo/**4*/): endif ?>',
            ],
            'alternate end tag if with side effect in elseif body' => [
                '<?php if ($foo): elseif ($bar): baz(); endif ?>',
            ],
            'alternate end tag if with side effect in elseif braces' => [
                '<?php ?>',
                '<?php if ($foo): elseif ($foo->baz()): endif ?>',
            ],
            'alternate end tag if without side effect and elseif without side effect' => [
                '<?php ?>',
                '<?php if ($foo): elseif ($bar): endif ?>',
            ],
            'alternate end tag if without side effect and elseif without side effect with comments' => [
                '<?php /*1*//*2*//*3*//*4*//*5*//*6*//*7*/?>',
                '<?php /*1*/if/*2*/(/*3*/$foo/*4*/): elseif/*5*/(/*6*/$bar/*7*/): endif ?>',
            ],
            'alternate end tag if without side effect and elseif without side effect with doc comments' => [
                '<?php /**1*//**2*//**3*//**4*//**5*//**6*//**7*/?>',
                '<?php /**1*/if/**2*/(/**3*/$foo/**4*/): elseif/**5*/(/**6*/$bar/**7*/): endif ?>',
            ],
            'alternate end tag elseif without side effect but comment in body' => [
                '<?php if ($foo): elseif ($bar): /* todo */ endif ?>',
            ],
            'alternate end tag elseif without side effect but doc comment in body' => [
                '<?php if ($foo): elseif ($bar): /** todo */ endif ?>',
            ],
            'alternate end tag multiple elseif without side effect' => [
                '<?php ?>',
                '<?php if ($foo): elseif ($bar): elseif ($baz): elseif ($boz): endif ?>',
            ],
            'alternate end tag multiple elseif one with side effect' => [
                '<?php if ($foo): elseif ($bar): foo(); elseif ($baz): elseif ($boz): endif ?>',
            ],
            'alternate end tag if with side effect in else' => [
                '<?php if ($foo): else: bar(); endif ?>',
            ],
            'alternate end tag if with else without side effect' => [
                '<?php ?>',
                '<?php if ($foo): else: endif ?>',
            ],
            'alternate end tag if with else without side effect but comment in body' => [
                '<?php if ($foo): else: /* todo */ endif ?>',
            ],
            'alternate end tag if with else without side effect but doc comment in body' => [
                '<?php if ($foo): else: /** todo */ endif ?>',
            ],
            'alternate end tag if with else without side effect with comments' => [
                '<?php /*1*//*2*//*3*//*4*/?>',
                '<?php /*1*/if/*2*/(/*3*/$foo/*4*/): else: endif ?>',
            ],
            'alternate end tag if with else without side effect with doc comments' => [
                '<?php /**1*//**2*//**3*//**4*/?>',
                '<?php /**1*/if/**2*/(/**3*/$foo/**4*/): else: endif ?>',
            ],
            'alternate end tag if with side effect in braces and else without side effect' => [
                '<?php ?>',
                '<?php if ($foo->bar()): else: endif ?>',
            ],
            'alternate end tag if with side effect in body and else without side effect' => [
                '<?php if ($foo): bar();  endif ?>',
                '<?php if ($foo): bar(); else: endif ?>',
            ],
            'alternate end tag if compact' => [
                '<?php ?>',
                '<?php if($foo):elseif($bar):else:endif ?>',
            ],
            'alternate end tag if spaced' => [
                '<?php ?>',
                '<?php if   (   $foo ):    elseif   (  $bar ):   endif ?>',
            ],
            'do while with side effect in body' => [
                '<?php do { foo(); } while($foo);',
            ],
            'do while with side effect in braces' => [
                '<?php ',
                '<?php do {} while (foo());',
            ],
            'do while without side effects' => [
                '<?php ',
                '<?php do {} while ($foo);',
            ],
            'do while without side effects but comment in body' => [
                '<?php do { /* todo */ } while ($foo);',
            ],
            'do while without side effects but doc comment in body' => [
                '<?php do { /** todo */ } while ($foo);',
            ],
            'do while without side effect with comments' => [
                '<?php /*1*//*2*//*3*//*4*//*5*//*6*//*7*//*8*/',
                '<?php /*1*/do/*2*/{}/*3*/while/*4*/(/*5*/$foo/*6*/)/*7*/;/*8*/',
            ],
            'do while without side effect with doc comments' => [
                '<?php /**1*//**2*//**3*//**4*//**5*//**6*//**7*//**8*/',
                '<?php /**1*/do/**2*/{}/**3*/while/**4*/(/**5*/$foo/**6*/)/**7*/;/**8*/',
            ],
            'do while end tag with side effect in body' => [
                '<?php do { foo(); } while($foo) ?>',
            ],
            'do while end tag with side effect in braces' => [
                '<?php ?>',
                '<?php do {} while (foo()) ?>',
            ],
            'do while end tag without side effects' => [
                '<?php ?>',
                '<?php do {} while ($foo) ?>',
            ],
            'do while end tag without side effects but comment in body' => [
                '<?php do { /* todo */ } while ($foo) ?>',
            ],
            'do while end tag without side effects but doc comment in body' => [
                '<?php do { /** todo */ } while ($foo) ?>',
            ],
            'do while end tag without side effect with comments' => [
                '<?php /*1*//*2*//*3*//*4*//*5*//*6*//*7*//*8*/ ?>',
                '<?php /*1*/do/*2*/{}/*3*/while/*4*/(/*5*/$foo/*6*/)/*7*/;/*8*/ ?>',
            ],
            'do while end tag without side effect with doc comments' => [
                '<?php /**1*//**2*//**3*//**4*//**5*//**6*//**7*//**8*/ ?>',
                '<?php /**1*/do/**2*/{}/**3*/while/**4*/(/**5*/$foo/**6*/)/**7*/;/**8*/ ?>',
            ],
            'for with side effect in body' => [
                '<?php for (;;) { foo(); }',
            ],
            'for with side effect in braces' => [
                '<?php ',
                '<?php for ($i = 0; $i < count($foo); ++$i) {}',
            ],
            'for without side effects' => [
                '<?php ',
                '<?php for (;;) {}',
            ],
            'for without side effects but comment in body' => [
                '<?php for (;;) { /* todo */ }',
            ],
            'for without side effects but doc comment in body' => [
                '<?php for (;;) { /** todo */ }',
            ],
            'for without side effect with comments' => [
                '<?php /*1*//*2*//*3*//*4*//*5*//*6*//*7*/',
                '<?php /*1*/for/*2*/(/*3*/;/*4*/;/*5*/)/*6*/{}/*7*/',
            ],
            'for without side effect with doc comments' => [
                '<?php /**1*//**2*//**3*//**4*//**5*//**6*//**7*/',
                '<?php /**1*/for/**2*/(/**3*/;/**4*/;/**5*/)/**6*/{}/**7*/',
            ],
            'for alternate with side effect in body' => [
                '<?php for (;;): foo(); endfor;',
            ],
            'for alternate with side effect in braces' => [
                '<?php ',
                '<?php for ($i = 0; $i < count($foo); ++$i): endfor;',
            ],
            'for alternate without side effects' => [
                '<?php ',
                '<?php for (;;): endfor;',
            ],
            'for alternate without side effects but comment in body' => [
                '<?php for (;;): /* todo */ endfor;',
            ],
            'for alternate without side effects but doc comment in body' => [
                '<?php for (;;): /** todo */ endfor;',
            ],
            'for alternate without side effect with comments' => [
                '<?php /*1*//*2*//*3*//*4*//*5*//*6*//*7*/',
                '<?php /*1*/for/*2*/(/*3*/;/*4*/;/*5*/)/*6*/: endfor;/*7*/',
            ],
            'for alternate without side effect with doc comments' => [
                '<?php /**1*//**2*//**3*//**4*//**5*//**6*//**7*/',
                '<?php /**1*/for/**2*/(/**3*/;/**4*/;/**5*/)/**6*/: endfor;/**7*/',
            ],
            'for alternate end tag with side effect in body' => [
                '<?php for (;;): foo(); endfor ?>',
            ],
            'for alternate end tag with side effect in braces' => [
                '<?php ?>',
                '<?php for ($i = 0; $i < count($foo); ++$i): endfor ?>',
            ],
            'for alternate end tag without side effects' => [
                '<?php ?>',
                '<?php for (;;): endfor ?>',
            ],
            'for alternate end tag without side effects but comment in body' => [
                '<?php for (;;): /* todo */ endfor ?>',
            ],
            'for alternate end tag without side effects but doc comment in body' => [
                '<?php for (;;): /** todo */ endfor ?>',
            ],
            'for alternate end tag without side effect with comments' => [
                '<?php /*1*//*2*//*3*//*4*//*5*//*6*//*7*/ ?>',
                '<?php /*1*/for/*2*/(/*3*/;/*4*/;/*5*/)/*6*/: endfor;/*7*/ ?>',
            ],
            'for alternate end tag without side effect with doc comments' => [
                '<?php /**1*//**2*//**3*//**4*//**5*//**6*//**7*/ ?>',
                '<?php /**1*/for/**2*/(/**3*/;/**4*/;/**5*/)/**6*/: endfor;/**7*/ ?>',
            ],
            'switch with side effect in body' => [
                '<?php switch ($foo) { case 1: foo(); }',
            ],
            'switch with side effect in braces' => [
                '<?php ',
                '<?php switch ($foo->bar()) {}',
            ],
            'switch without side effects' => [
                '<?php ',
                '<?php switch ($foo) {}',
            ],
            'switch without side effects with comments' => [
                '<?php /*1*//*2*//*3*//*4*//*5*//*6*/',
                '<?php /*1*/switch/*2*/(/*3*/$foo/*4*/)/*5*/{}/*6*/',
            ],
            'switch without side effects with doc comments' => [
                '<?php /**1*//**2*//**3*//**4*//**5*//**6*/',
                '<?php /**1*/switch/**2*/(/**3*/$foo/**4*/)/**5*/{}/**6*/',
            ],
            'switch without side effects but comment in body' => [
                '<?php switch ($foo) { /* todo */ }',
            ],
            'switch without side effects but doc comment in body' => [
                '<?php switch ($foo) { /** todo */ }',
            ],
            'alternate switch with side effect in body' => [
                '<?php switch ($foo): case 1: foo(); endswitch;',
            ],
            'alternate switch with side effect in braces' => [
                '<?php ',
                '<?php switch ($foo->bar()): endswitch;',
            ],
            'alternate switch without side effects' => [
                '<?php ',
                '<?php switch ($foo): endswitch;',
            ],
            'alternate switch without side effects with comments' => [
                '<?php /*1*//*2*//*3*//*4*//*5*//*6*//*7*/',
                '<?php /*1*/switch/*2*/(/*3*/$foo/*4*/)/*5*/: endswitch/*6*/;/*7*/',
            ],
            'alternate switch without side effects with doc comments' => [
                '<?php /**1*//**2*//**3*//**4*//**5*//**6*//**7*/',
                '<?php /**1*/switch/**2*/(/**3*/$foo/**4*/)/**5*/: endswitch/**6*/;/**7*/',
            ],
            'alternate switch without side effects but comment in body' => [
                '<?php switch ($foo): /* todo */ endswitch;',
            ],
            'alternate switch without side effects but doc comment in body' => [
                '<?php switch ($foo): /** todo */ endswitch;',
            ],
            'alternate end tag switch with side effect in body' => [
                '<?php switch ($foo): case 1: foo(); endswitch ?>',
            ],
            'alternate end tag switch with side effect in braces' => [
                '<?php ?>',
                '<?php switch ($foo->bar()): endswitch ?>',
            ],
            'alternate end tag switch without side effects' => [
                '<?php ?>',
                '<?php switch ($foo): endswitch ?>',
            ],
            'alternate end tag switch without side effects with comments' => [
                '<?php /*1*//*2*//*3*//*4*//*5*//*6*/ ?>',
                '<?php /*1*/switch/*2*/(/*3*/$foo/*4*/)/*5*/: endswitch/*6*/ ?>',
            ],
            'alternate end tag switch without side effects with doc comments' => [
                '<?php /**1*//**2*//**3*//**4*//**5*//**6*/ ?>',
                '<?php /**1*/switch/**2*/(/**3*/$foo/**4*/)/**5*/: endswitch/**6*/ ?>',
            ],
            'alternate end tag switch without side effects but comment in body' => [
                '<?php switch ($foo): /* todo */ endswitch ?>',
            ],
            'alternate end tag switch without side effects but doc comment in body' => [
                '<?php switch ($foo): /** todo */ endswitch ?>',
            ],

            'try with side effects' => [
                '<?php try { foo(); } catch (Exception $e) {}',
            ],
            'try with side effects in catch' => [
                '<?php ',
                '<?php try {} catch (Exception $e) { foo(); }',
            ],
            'try without side effects in catch' => [
                '<?php ',
                '<?php try {} catch (Exception $e) {}',
            ],
            'try without side effects and finally with side effects' => [
                '<?php try {} finally { foo(); }'
            ],

            // while

            // modifications
        ];
    }
}
