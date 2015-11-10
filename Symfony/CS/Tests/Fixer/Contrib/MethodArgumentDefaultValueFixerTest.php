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

final class MethodArgumentDefaultValueFixerTest extends AbstractFixerTestBase
{
    /**
     * @dataProvider provideExamples
     *
     * @param string      $expected
     * @param string|null $input
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
            array(
                '<?php function bFunction($foo, $bar) {}',
                '<?php function bFunction($foo = null, $bar) {}',
            ),
            array(
                '<?php function bFunction($foo, $bar) {}',
                '<?php function bFunction($foo = \'two words\', $bar) {}',
            ),
            array(
                '<?php function cFunction($foo, $bar, $baz) {}',
                '<?php function cFunction($foo = false, $bar = \'bar\', $baz) {}',
            ),
            array(
                '<?php function dFunction($foo, $bar, $baz) {}',
                '<?php function dFunction($foo = false, $bar, $baz) {}',
            ),
            array(
                '<?php function eFunction($foo, $bar, \SplFileInfo $baz, $x) {}',
                '<?php function eFunction($foo = PHP_EOL, $bar, \SplFileInfo $baz = null, $x) {}',
            ),
            array(
                '<?php function eFunction($foo, $bar, \SplFileInfo $baz, $x = \'default\') {}',
                '<?php function eFunction($foo, $bar = \'removedDefault\', \SplFileInfo $baz, $x = \'default\') {}',
            ),
            array(
                <<<'EOT'
                    <?php
                        function eFunction($foo, $bar, \SplFileInfo $baz, $x = 'default' {};

                        function fFunction($foo, $bar, \SplFileInfo $baz, $x = 'default') {};
EOT
                ,
                <<<'EOT'
                    <?php
                        function eFunction($foo, $bar, \SplFileInfo $baz, $x = 'default' {};

                        function fFunction($foo, $bar = 'removedValue', \SplFileInfo $baz, $x = 'default') {};
EOT
            ),
            array(
                '<?php function foo ($bar, $c) {}',
                '<?php function foo ($bar /* a */ = /* b */ 1, $c) {}',
            ),
            array(
                '<?php function hFunction($foo,$bar,\SplFileInfo $baz,$x = 5) {};',
                '<?php function hFunction($foo,$bar=\'removedValue\',\SplFileInfo $baz,$x = 5) {};',
            ),
        );
    }
}
