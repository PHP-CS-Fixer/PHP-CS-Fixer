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

use Symfony\CS\Fixer\ObjectOperatorFixer as Fixer;

/**
 * @author Farhad Safarov <farhad.safarov@gmail.com>
 */
class ObjectOperatorFixerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider testFixObjectOperatorSpaces
     */
    public function testFixControlsWithParenthesesAndSuffixBrace($toBeFixed, $expected)
    {
        $fixer = new Fixer();

        $this->assertSame($expected, $fixer->fix($this->getTestFile(), $toBeFixed));
    }

    public function testFixObjectOperatorSpaces()
    {
        return array(
            array('<?php $object   ->method();', '<?php $object->method();'),
            array('<?php $object   ->   method();', '<?php $object->method();'),
            array('<?php $object->   method();', '<?php $object->method();'),
            array('<?php $object	->method();', '<?php $object->method();'),
            array('<?php $object->	method();', '<?php $object->method();'),
            array('<?php $object	->	method();', '<?php $object->method();'),
            array('<?php $object->method();', '<?php $object->method();'),
            array('<?php echo "use it as you want";', '<?php echo "use it as you want";'),
            // Ensure that doesn't break chained multi-line statements
            array('<?php $object->method()
                        ->method2()
                        ->method3();',
                    '<?php $object->method()
                        ->method2()
                        ->method3();',
            ),
            array(
                '<?php $this
             ->add()
             // Some comment
             ->delete();',
                 '<?php $this
             ->add()
             // Some comment
             ->delete();',
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
