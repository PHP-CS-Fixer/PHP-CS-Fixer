<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer\PSR2;

use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

/**
 * @author SpacePossum <possumfromspace@gmail.com>
 *
 * @internal
 */
class ForLoopSemicolonFixerTest extends AbstractFixerTestBase
{
    /**
     * @dataProvider provideCases
     */
    public function testFixes($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    public function provideCases()
    {
        return array(
            array(
                '<?php
for(; ; --$p){
    //
}',
                '<?php
for(;       ;     --$p){
    //
}',
            ),
            array(
                '<?php
for(; ; --$p1){
    //
}',
                '<?php
for(;;--$p1){
    //
}',
            ),
            array(
                '<?php
for($z=0/**/; $z<4; --$z){
    //
}',
                '<?php
for($z=0/**/   ;       $z<4;     --$z){
    //
}',
            ),
            array(
                '<?php
for($k=0; $k<4; --$k){
    //
}',
                '<?php
for($k=0;$k<4;--$k){
    //
}',
            ),
            array(
                '<?php
for($t=0; $t<4; --$t){
    //
}',
                '<?php
for($t=0;       $t<4;     --$t){
    //
}',
            ),
            array(
                '<?php
for(; ; --$n){
    //
}',
                '<?php
for(    ;;--$n){
    //
}',
            ),
            array(
                '<?php
for(; /*;*/$m<4; --$m){
    //
}',
                '<?php
for(;/*;*/$m<4;     --$m){
    //
}',
            ),
            array(
                '<?php
for ($c=";", $uu = 0; $uu < 4;) {
}',
                '<?php
for ($c=";", $uu = 0;$uu < 4;) {
}',
            ),
        );
    }
}
