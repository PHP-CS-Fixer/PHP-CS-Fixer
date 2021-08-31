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
 * @author Matteo Beccati <matteo@beccati.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Alias\EregToPregFixer
 */
final class EregToPregFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases(): array
    {
        return [
            ['<?php $x = 1;'],
            ['<?php $x = "ereg";'],

            ['<?php $x = ereg("[A-Z]"."foo", $m);'],

            ['<?php $x = ereg("^*broken", $m);'],

            ['<?php $x = Foo::split("[A-Z]", $m);'],
            ['<?php $x = $foo->split("[A-Z]", $m);'],
            ['<?php $x = Foo\split("[A-Z]", $m);'],

            ['<?php $x = preg_match(\'/[A-Z]/D\');', '<?php $x = ereg(\'[A-Z]\');'],
            ['<?php $x = preg_match(\'/[A-Z]/D\', $m);', '<?php $x = ereg(\'[A-Z]\', $m);'],

            ['<?php $x = preg_match("/[A-Z]/D", $m);', '<?php $x = ereg("[A-Z]", $m);'],
            ['<?php $x = preg_match("/[A-Z]/Di", $m);', '<?php $x = eregi("[A-Z]", $m);'],
            ['<?php $x = preg_match("#/[AZ]#D", $m);', '<?php $x = ereg("/[AZ]", $m);'],
            ['<?php $x = preg_match("#[AZ]/#D", $m);', '<?php $x = ereg("[AZ]/", $m);'],
            ['<?php $x = preg_match("!#[A]/!D", $m);', '<?php $x = ereg("#[A]/", $m);'],
            ['<?php $x = preg_match("!##[A\!]//!D", $m);', '<?php $x = ereg("##[A!]//", $m);'],
            ['<?php $x = preg_match("/##[A!!]\/\//D", $m);', '<?php $x = ereg("##[A!!]//", $m);'],
            ['<?php $x = preg_match("#\#\#[A!!]///#D", $m);', '<?php $x = ereg("##[A!!]///", $m);'],

            ['<?php $x = preg_replace("/[A-Z]/D", "", $m);', '<?php $x = ereg_replace("[A-Z]", "", $m);'],
            ['<?php $x = preg_replace("/[A-Z]/Di", "", $m);', '<?php $x = eregi_replace("[A-Z]", "", $m);'],
            ['<?php $x = preg_split("/[A-Z]/D", $m);', '<?php $x = split("[A-Z]", $m);'],
            ['<?php $x = preg_split("/[A-Z]/Di", $m);', '<?php $x = spliti("[A-Z]", $m);'],
        ];
    }
}
