<?php

declare(strict_types=1);

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

use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Operator\ConcatSpaceFixer
 */
final class ConcatSpaceFixerTest extends AbstractFixerTestCase
{
    public function testInvalidConfigMissingKey(): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageMatches('#^\[concat_space\] Invalid configuration: The option "a" does not exist\. Defined options are: "spacing"\.$#');

        $this->fixer->configure(['a' => 1]);
    }

    public function testInvalidConfigValue(): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageMatches('#^\[concat_space\] Invalid configuration: The option "spacing" with value "tabs" is invalid\. Accepted values are: "one", "none"\.$#');

        $this->fixer->configure(['spacing' => 'tabs']);
    }

    /**
     * @dataProvider provideWithoutSpaceCases
     */
    public function testFixWithoutSpace(string $expected, ?string $input = null): void
    {
        $this->fixer->configure(['spacing' => 'none']);
        $this->doTest($expected, $input);
    }

    public static function provideWithoutSpaceCases(): array
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
     * @dataProvider provideWithSpaceCases
     */
    public function testFixWithSpace(string $expected, ?string $input = null): void
    {
        $this->fixer->configure(['spacing' => 'one']);
        $this->doTest($expected, $input);
    }

    public static function provideWithSpaceCases(): array
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
