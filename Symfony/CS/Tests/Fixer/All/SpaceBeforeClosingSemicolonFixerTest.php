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

use Symfony\CS\Fixer\All\SpaceBeforeClosingSemicolonFixer as Fixer;

/**
 * @author John Kelly <johnmkelly86@gmail.com>
 */
class SpaceBeforeClosingSemicolonFixerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideCases
     */
    public function testOneLineFix($expected, $input)
    {
        $fixer = new Fixer();
        $file = $this->getTestFile();

        $this->assertSame($expected, $fixer->fix($file, $input));
    }

    public function provideCases()
    {
        return array(
            array(
                '<?php
$this
    ->setName(\'readme\')
    ->setDescription(\'Generates the README content, based on the fix command help\')
;
?>',
                '<?php
$this
    ->setName(\'readme\')
    ->setDescription(\'Generates the README content, based on the fix command help\')
;
?>'),
            array(
                '<?php
$this
    ->setName(\'readme\')
    ->setDescription(\'Generates the README content, based on the fix command help\')
    ;
?>',
                '<?php
$this
    ->setName(\'readme\')
    ->setDescription(\'Generates the README content, based on the fix command help\')
    ;
?>'),
            array(
                '<?php echo "$this->foo(\'with param containing ;\') ;"; ?>',
                '<?php echo "$this->foo(\'with param containing ;\') ;" ; ?>',
            ),
            array(
                '<?php $this->foo(); ?>',
                '<?php $this->foo() ; ?>',
            ),
            array(
                '<?php $this->foo(\'with param containing ;\'); ?>',
                '<?php $this->foo(\'with param containing ;\') ; ?>',
            ),
            array(
                '<?php $this->foo(\'with param containing ) ; \'); ?>',
                '<?php $this->foo(\'with param containing ) ; \') ; ?>',
            ),
            array(
                '<?php $this->foo("with param containing ) ; "); ?>',
                '<?php $this->foo("with param containing ) ; ") ; ?>',
            ),
            array(
                '<?php $this->foo(); ?>',
                '<?php $this->foo(); ?>',
            ),
            array(
                '<?php $this->foo("with semicolon in string) ; "); ?>',
                '<?php $this->foo("with semicolon in string) ; "); ?>',
            ),
            array('<?php

$this->foo();

?>
',
                '<?php

$this->foo() ;

?>
',
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
