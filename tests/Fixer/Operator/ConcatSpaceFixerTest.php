<?php

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Tests\Fixer\Operator;

use PhpCsFixer\Test\AbstractFixerTestCase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author SpacePossum
 *
 * @internal
 */
final class ConcatSpaceFixerTest extends AbstractFixerTestCase
{
    public function testInvalidConfigMissingKey()
    {
        $this->setExpectedExceptionRegExp(
            'PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException',
            '#^\[concat_space\] Missing "spacing" configuration.$#'
        );

        $this->fixer->configure(array('a' => 1));
    }

    public function testInvalidConfigValue()
    {
        $this->setExpectedExceptionRegExp(
            'PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException',
            '#^\[concat_space\] "spacing" configuration must be "one" or "none".$#'
        );

        $this->fixer->configure(array('spacing' => 'tabs'));
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideWithoutSpaceCases
     */
    public function testFixWithoutSpace($expected, $input = null)
    {
        $this->fixer->configure(array('spacing' => 'none'));
        $this->doTest($expected, $input);
    }

    public function provideWithoutSpaceCases()
    {
        return array(
            array(
                '<?php $foo = "a".\'b\'."c"."d".$e.($f + 1);',
                '<?php $foo = "a" . \'b\' ."c". "d" . $e.($f + 1);',
            ),
            array(
                '<?php $foo = 1 ."foo";',
                '<?php $foo = 1 . "foo";',
            ),
            array(
                '<?php $foo = "foo". 1;',
                '<?php $foo = "foo" . 1;',
            ),
            array(
                '<?php $foo = "a".
"b";',
                '<?php $foo = "a" .
"b";',
            ),
            array(
                '<?php $a = "foobar"
    ."baz";',
            ),
            array(
                '<?php $a = "foobar"
                     //test
                     ."baz";',
            ),
            array(
                '<?php $a = "foobar"
                     /* test */
                     ."baz";',
            ),
            array(
                '<?php $a = "foobar" //
    ."baz";',
            ),
            array(
                '<?php $a = "foobar" //
                            ."baz"//
                            ."cex"/**/
                            ."dev"/**  */
                            ."baz"      //
                            ."cex"      /**/
                            ."ewer23"           '.'
                            ."dev"      /**  */
                    ;',
            ),
            array(
                '<?php $a = "foobar" //
    ."baz" /**/
    ."something";',
            ),
            array(
                '<?php $a = "foobar"
    ."baz".      //
    "something";',
            ),
            array(
                '<?php $a = "foobar"
    ."baz".      /**  */
    "something";',
            ),
            array(
                "<?php
                \$longString = '*'
                    .'*****'
                    .'*****'
                    .'*****'
                    // Comment about next line
                    .'*****'
                    // Other comment
                    .'*****';
                ",
                "<?php
                \$longString = '*'
                    . '*****'
                    .  '*****'
                    .   '*****'
                    // Comment about next line
                    .  '*****'
                    // Other comment
                    .  '*****';
                ",
            ),
        );
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideWithSpaceCases
     */
    public function testFixWithSpace($expected, $input = null)
    {
        $this->fixer->configure(array('spacing' => 'one'));
        $this->doTest($expected, $input);
    }

    public function provideWithSpaceCases()
    {
        return array(
            array(
                '<?php
                    $a =   //
                    $c .   /**/
                    $d     #
                    . $e   /**  */
                    . $f . //
                    $z;
                ',
                '<?php
                    $a =   //
                    $c   .   /**/
                    $d     #
                    .   $e   /**  */
                    .   $f   . //
                    $z;
                ',
            ),
            array(
                '<?php $foo = "a" . \'b\' . "c" . "d" . $e . ($f + 1);',
                '<?php $foo = "a" . \'b\' ."c". "d"    .  $e.($f + 1);',
            ),
            array(
                '<?php $foo = "a" .
"b";',
                '<?php $foo = "a".
"b";',
            ),
            array(
                '<?php $a = "foobar"
    . "baz";',
                '<?php $a = "foobar"
    ."baz";',
            ),
            array(
                '<?php echo $a . $b;
                    echo $d . $e .   //
                        $f;
                    echo $a . $b?>
                 <?php
                    echo $c;
                ',
                '<?php echo $a.$b;
                    echo $d    .            $e          .   //
                        $f;
                    echo $a   .                  $b?>
                 <?php
                    echo $c;
                ',
            ),
        );
    }
}
