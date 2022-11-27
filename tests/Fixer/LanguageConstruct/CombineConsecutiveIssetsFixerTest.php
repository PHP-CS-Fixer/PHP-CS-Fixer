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

namespace PhpCsFixer\Tests\Fixer\LanguageConstruct;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\LanguageConstruct\CombineConsecutiveIssetsFixer
 */
final class CombineConsecutiveIssetsFixerTest extends AbstractFixerTestCase
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
            [
                '<?php $a = isset($a, $b)  ;',
                '<?php $a = isset($a) && isset($b);',
            ],
            [
                '<?php $a = isset($a, $b,$c)  ;',
                '<?php $a = isset($a) && isset($b,$c);',
            ],
            [
                '<?php $a = isset($a,$c, $b,$c)  ;',
                '<?php $a = isset($a,$c) && isset($b,$c);',
            ],
            [
                '<?php $a = isset($a,$c, $b)  ;',
                '<?php $a = isset($a,$c) && isset($b);',
            ],
            [
                '<?php $a = isset($a, $b)   || isset($c, $e)  ?>',
                '<?php $a = isset($a) && isset($b) || isset($c) && isset($e)?>',
            ],
            [
                '<?php $a = isset($a[a() ? b() : $d], $b)  ;',
                '<?php $a = isset($a[a() ? b() : $d]) && isset($b);',
            ],
            [
                '<?php $a = isset($a[$b], $b/**/)  ;',
                '<?php $a = isset($a[$b]/**/) && isset($b);',
            ],
            [
                '<?php $a = isset ( $a, $c, $d /*1*/ )                 ;',
                '<?php $a = isset ( $a /*1*/ )    &&    isset ( $c   ) && isset( $d );',
            ],
            'minimal fix case' => [
                '<?php {{isset($a, $b);}}',
                '<?php {{isset($a)&&isset($b);}}',
            ],
            [
                '<?php foo(isset($a, $b, $c)    );',
                '<?php foo(isset($a) && isset($b) && isset($c));',
            ],
            [
                '<?php isset($a, $b)   && !isset($c) ?>',
                '<?php isset($a) && isset($b) && !isset($c) ?>',
            ],
            [
                '<?php $a = isset($a,$c, $b,$c, $b,$c,$d,$f, $b)      ;',
                '<?php $a = isset($a,$c) && isset($b,$c) && isset($b,$c,$d,$f) && isset($b);',
            ],
            'comments' => [
                '<?php

$a =#0
isset#1
(#2
$a, $b,$c, $d#3
)#4
#5

 #6
 #7
  #8
  #9
 /*10*/     /**11
*/
 '.'
;',
                '<?php

$a =#0
isset#1
(#2
$a#3
)#4
&&#5
isset
 #6
 #7
 ( #8
 $b #9
 /*10*/,     $c/**11
*/
)&& isset($d)
;',
            ],
            [
                '<?php
                    $a = isset($a, $b, $c, $d, $e, $f)          ;
                    echo 1; echo 1; echo 1; echo 1; echo 1; echo 1; echo 1;
                    echo 1; echo 1; echo 1; echo 1; echo 1; echo 1; echo 1;
                    echo 1; echo 1; echo 1; echo 1; echo 1; echo 1; echo 1;
                    echo 1; echo 1; echo 1; echo 1; echo 1; echo 1; echo 1;
                    $a = isset($a, $b)  ;
                ',
                '<?php
                    $a = isset($a) && isset($b) && isset($c) && isset($d) && isset($e) && isset($f);
                    echo 1; echo 1; echo 1; echo 1; echo 1; echo 1; echo 1;
                    echo 1; echo 1; echo 1; echo 1; echo 1; echo 1; echo 1;
                    echo 1; echo 1; echo 1; echo 1; echo 1; echo 1; echo 1;
                    echo 1; echo 1; echo 1; echo 1; echo 1; echo 1; echo 1;
                    $a = isset($a) && isset($b);
                ',
            ],
            [
                '<?php $d = isset($z[1], $z[2], $z[3])     || false;',
                '<?php $d = isset($z[1]) && isset($z[2]) && isset($z[3]) || false;',
            ],
            [
                '<?php
                    $a = isset($a, $b)   && isset($c) === false;
                    $a = isset($a, $b)   && isset($c) | false;
                    $a = isset($a, $b)   && isset($c) ^ false;
                ',
                '<?php
                    $a = isset($a) && isset($b) && isset($c) === false;
                    $a = isset($a) && isset($b) && isset($c) | false;
                    $a = isset($a) && isset($b) && isset($c) ^ false;
                ',
            ],
            // don't fix cases
            [
                '<?php $a = isset($a) && $a->isset(); $b=isset($d);',
            ],
            [
                '<?php
                    $a = !isset($a) && isset($b);
                    $a = !isset($a) && !isset($b);
                    $a = isset($a) && !isset($b);
                    //
                    $a = isset($b) && isset($c) === false;
                    $a = isset($b) && isset($c) | false;
                    $a = isset($b) && isset($c) ^ false;
                    //
                    $a = false === isset($b) && isset($c);
                    $a = false | isset($b) && isset($c);
                    $a = false ^ isset($b) && isset($c);
                ',
            ],
            [
                '<?php $a = !isset($container[$a]) && isset($container[$b]) && !isset($container[$c]) && isset($container[$d]);',
            ],
        ];
    }

    public function testAnonymousClass(): void
    {
        $this->doTest(
            '<?php
                class A {function isset(){}} // isset($b) && isset($c)
                $a = new A(); /** isset($b) && isset($c) */
                if (isset($b) && $a->isset()) {}
            '
        );
    }
}
