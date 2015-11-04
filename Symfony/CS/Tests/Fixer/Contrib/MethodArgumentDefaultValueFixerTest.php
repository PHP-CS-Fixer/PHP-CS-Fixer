<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer\Contrib;

use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

class MethodArgumentDefaultValueFixerTest extends AbstractFixerTestBase
{
    /**
     * @dataProvider provideExamples
     *
     * @param string $expected
     * @param string $input
     */
    public function testFix($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }
    /**
     * @return array
     */
    public function provideExamples()
    {
        return array(
            array('<?php public function bFunction($foo, $bar)', '<?php public function bFunction($foo = null, $bar)'),
            array('<?php public function cFunction($foo, $bar, $baz)', '<?php public function cFunction($foo = false, $bar = \'bar\', $baz)'),
            array('<?php public function dFunction($foo, $bar, $baz)', '<?php public function dFunction($foo = false, $bar, $baz)'),
            array('<?php public function eFunction($foo, $bar, \SplFileInfo $baz, $x)', '<?php public function eFunction($foo = PHP_EOL, $bar, \SplFileInfo $baz = null, $x)'),
            array('<?php public function eFunction($foo, $bar, \SplFileInfo $baz, $x = \'default\')', '<?php public function eFunction($foo, $bar = \'removedDefault\', \SplFileInfo $baz, $x = \'default\')'),
        );
    }
}
