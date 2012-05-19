<?php

/*
 * This file is part of the Symfony CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer;

use Symfony\CS\Fixer\ExtraEmptyLinesFixer;

class ExtraEmptyLinesFixerTest extends \PHPUnit_Framework_TestCase
{
    public function testFix()
    {
        $fixer = new ExtraEmptyLinesFixer();
        $file = new \SplFileInfo(__FILE__);

        $expected = <<<'EOF'
$a = new Bar();

$a = new FooBaz();
EOF;

        $input = <<<'EOF'
$a = new Bar();


$a = new FooBaz();
EOF;

        $this->assertEquals($expected, $fixer->fix($file, $input));
    }
}
