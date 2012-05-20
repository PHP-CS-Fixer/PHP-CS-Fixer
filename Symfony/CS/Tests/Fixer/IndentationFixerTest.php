<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer;

use Symfony\CS\Fixer\IndentationFixer;

class IndentationFixerTest extends \PHPUnit_Framework_TestCase
{
    public function testFix()
    {
        $fixer = new IndentationFixer();
        $file = new \SplFileInfo(__FILE__);

        $this->assertEquals('           FOO', $fixer->fix($file, " \t \t FOO"));
    }
}
