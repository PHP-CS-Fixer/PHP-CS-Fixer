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

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author SpacePossum
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Operator\ConcatSpaceFixer
 */
final class ConcatSpaceFixerTest extends AbstractFixerTestCase
{
    public function testInvalidConfigMissingKey()
    {
        $this->expectException(\PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageRegExp('#^\[concat_space\] Invalid configuration: The option "a" does not exist\. Defined options are: "spacing"\.$#');

        $this->fixer->configure(['a' => 1]);
    }

    public function testInvalidConfigValue()
    {
        $this->expectException(\PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageRegExp('#^\[concat_space\] Invalid configuration: The option "spacing" with value "tabs" is invalid\. Accepted values are: "one", "none"\.$#');

        $this->fixer->configure(['spacing' => 'tabs']);
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideWithoutSpaceCases
     */
    public function testFixWithoutSpace($expected, $input = null)
    {
        $this->fixer->configure(['spacing' => 'none']);
        $this->doTest($expected, $input);
    }

    public function provideWithoutSpaceCases()
    {
        return [
            [
                '<?php $foo = "a".\'b\'."c"."d".$e.($f + 1);',
                '<?php $foo = "a" . \'b\' ."c". "d" . $e.($f + 1);',
            ],
            [
                '<?php $foo = 1 ."foo";',
                '<?php $foo = 1 . "foo";',
            ],
            [
                '<?php $foo = "foo". 1;',
                '<?php $foo = "foo" . 1;',
            ],
            [
                '<?php $foo = "a".
"b";',
                '<?php $foo = "a" .
"b";',
            ],
            [
                '<?php $a = "foobar"
    ."baz";',
            ],
            [
                '<?php $a = "foobar"
                     //test
                     ."baz";',
            ],
            [
                '<?php $a = "foobar"
                     /* test */
                     ."baz";',
            ],
            [
                '<?php $a = "foobar" //
    ."baz";',
            ],
            [
                '<?php $a = "foobar" //
                            ."baz"//
                            ."cex"/**/
                            ."dev"/**  */
                            ."baz"      //
                            ."cex"      /**/
                            ."ewer23"           '.'
                            ."dev"      /**  */
                    ;',
            ],
            [
                '<?php $a = "foobar" //
    ."baz" /**/
    ."something";',
            ],
            [
                '<?php $a = "foobar"
    ."baz".      //
    "something";',
            ],
            [
                '<?php $a = "foobar"
    ."baz".      /**  */
    "something";',
            ],
            [
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
            ],
        ];
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideWithSpaceCases
     */
    public function testFixWithSpace($expected, $input = null)
    {
        $this->fixer->configure(['spacing' => 'one']);
        $this->doTest($expected, $input);
    }

    public function provideWithSpaceCases()
    {
        return [
            [
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
            ],
            [
                '<?php $foo = "a" . \'b\' . "c" . "d" . $e . ($f + 1);',
                '<?php $foo = "a" . \'b\' ."c". "d"    .  $e.($f + 1);',
            ],
            [
                '<?php $foo = "a" .
"b";',
                '<?php $foo = "a".
"b";',
            ],
            [
                '<?php $a = "foobar"
    . "baz";',
                '<?php $a = "foobar"
    ."baz";',
            ],
            [
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
            ],
        ];
    }
}
