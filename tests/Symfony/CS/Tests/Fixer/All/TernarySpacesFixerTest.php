<?php

/*
 * This file is part of the Symfony CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer\All;

use Symfony\CS\Fixer\All\TernarySpacesFixer as Fixer;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class TernarySpacesFixerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideCases
     */
    public function testFix($expected, $input)
    {
        $fixer = new Fixer();
        $file = $this->getTestFile();

        $this->assertSame($expected, $fixer->fix($file, $input));
        $this->assertSame($expected, $fixer->fix($file, $expected));
    }

    public function provideCases()
    {
        return array(
            array(
                '<?php $val = (1===1) ? true : false;',
                '<?php $val = (1===1)?true:false;',
            ),
            array(
                '<?php $val = 1===1 ? true : false;',
                '<?php $val = 1===1?true:false;',
            ),
            array(
                '<?php
$a = $b  ? 2 : 3;
$a = $bc ? 2 : 3;',
                '<?php
$a = $b  ? 2 : 3;
$a = $bc ? 2 : 3;',
            ),
            array(
                '<?php $config = $config ?: new Config();',
                '<?php $config = $config ? : new Config();',
            ),
            array(
                '<?php
$a = $b ? (
        $c + 1
    ) : (
        $d + 1
    );',
                '<?php
$a = $b ? (
        $c + 1
    ) : (
        $d + 1
    );',
            ),
            array(
                '<?php
$a = $b
    ? $c
    : $d;',
                '<?php
$a = $b
    ?$c
    :$d;',
            ),
            array(
                '<?php
$a = ($b
    ? $c
    : ($d
        ? $e
        : $f
    )
);',
                '<?php
$a = ($b
    ? $c
    : ($d
        ? $e
        : $f
    )
);',
            ),
            array(
                '<?php
$a = ($b
    ? ($c1 ? $c2 : ($c3a ?: $c3b))
    : ($d1 ? $d2 : $d3)
);',
                '<?php
$a = ($b
    ? ($c1?$c2:($c3a? :$c3b))
    : ($d1?$d2:$d3)
);',
            ),
        );
    }

    private function getTestFile($filename = __FILE__)
    {
        static $files = array();

        if (!isset($files[$filename])) {
            $files[$filename] = new \SplFileInfo($filename);
        }

        return $files[$filename];
    }
}
