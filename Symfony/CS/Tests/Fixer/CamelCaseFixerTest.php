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

use Symfony\CS\Fixer\CamelCaseFixer as Fixer;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class CamelCaseFixerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideCases
     */
    public function testFix($expected, $input, $file)
    {
        $this->expectOutputString($expected);

        $fixer = new Fixer();

        $fixer->fix($file, $input);
    }

    public function provideCases()
    {
        $file = $this->getTestFile();
        $filePath = strtr($file->getRealPath(), '\\', '/');

        return array(
            array(
                '',
                '<?php function fooBar() {};',
                $file,
            ),
            array(
                '',
                '<?php function foo_bar() {};',
                $file,
            ),
            array(
                '',
                '<?php class Foo { public function bar() {} }',
                $file,
            ),
            array(
                '! File '.$filePath.' contains method not in camelCase: Bar'.PHP_EOL,
                '<?php class Foo { public function Bar() {} }',
                $file,
            ),
            array(
                '',
                '<?php class Foo { public function barBaz() {} }',
                $file,
            ),
            array(
                '! File '.$filePath.' contains method not in camelCase: bar_baz'.PHP_EOL,
                '<?php class Foo { public function bar_baz() {} }',
                $file,
            ),
            array(
                '',
                '<?php class Foo { public function tokenGetAll($baz) { return token_get_all($baz); } }',
                $file,
            ),
            array(
                '',
                '<?php class Foo { public function __construct() { } }',
                $file,
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
