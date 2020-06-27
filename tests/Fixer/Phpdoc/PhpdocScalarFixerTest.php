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

namespace PhpCsFixer\Tests\Fixer\Phpdoc;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Graham Campbell <graham@alt-three.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\AbstractPhpdocTypesFixer
 * @covers \PhpCsFixer\Fixer\Phpdoc\PhpdocScalarFixer
 */
final class PhpdocScalarFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input = null, array $config = [])
    {
        $this->fixer->configure($config);
        $this->doTest($expected, $input);
    }

    public static function provideFixCases()
    {
        yield 'basic fix' => [
            '<?php
            /**
             * @return int
             */
             ',
            '<?php
            /**
             * @return integer
             */
             ',
        ];

        yield 'property fix' => [
            '<?php
            /**
             * @method int foo()
             * @property int $foo
             * @property callback $foo
             * @property-read bool $bar
             * @property-write float $baz
             */
             ',
            '<?php
            /**
             * @method integer foo()
             * @property integer $foo
             * @property callback $foo
             * @property-read boolean $bar
             * @property-write double $baz
             */
             ',
        ];

        yield 'do not modify variables' => [
            '<?php
            /**
             * @param int $integer
             */
             ',
            '<?php
            /**
             * @param integer $integer
             */
             ',
        ];

        yield 'fix with tabs on one line' => [
            "<?php /**\t@return\tbool\t*/",
            "<?php /**\t@return\tboolean\t*/",
        ];

        yield 'fix more things' => [
            '<?php
            /**
             * Hello there mr integer!
             *
             * @param int|float $integer
             * @param int|int[] $foo
             * @param string|null $bar
             *
             * @return string|bool
             */
             ',
            '<?php
            /**
             * Hello there mr integer!
             *
             * @param integer|real $integer
             * @param int|integer[] $foo
             * @param str|null $bar
             *
             * @return string|boolean
             */
             ',
        ];

        yield 'fix var' => [
            '<?php
            /**
             * @var int Some integer value.
             */
             ',
            '<?php
            /**
             * @var integer Some integer value.
             */
             ',
        ];

        yield 'fix var with more stuff' => [
            '<?php
            /**
             * @var bool|int|Double Booleans, integers and doubles.
             */
             ',
            '<?php
            /**
             * @var boolean|integer|Double Booleans, integers and doubles.
             */
             ',
        ];

        yield 'fix type' => [
            '<?php
            /**
             * @type float
             */
             ',
            '<?php
            /**
             * @type real
             */
             ',
        ];

        yield 'do not fix' => [
            '<?php
            /**
             * @var notaboolean
             */
             ',
        ];

        yield 'complex mix' => [
            '<?php
            /**
             * @var notabooleanthistime|bool|integerr
             */
             ',
            '<?php
            /**
             * @var notabooleanthistime|boolean|integerr
             */
             ',
        ];

        yield 'do not modify complex tag' => [
            '<?php
            /**
             * @Type("boolean")
             */
             ',
        ];

        yield 'do not modify strings' => [
            "<?php
            \$string = '
                /**
                 * @var boolean
                 */
            ';
             ",
        ];

        yield 'empty DocBlock' => [
            '<?php
            /**
             *
             */
             ',
        ];

        yield 'wrong cased Phpdoc tag is not altered' => [
            '<?php
            /**
             * @Param boolean
             *
             * @Return int
             */
             ',
        ];

        yield 'inline doc' => [
            '<?php
            /**
             * Does stuffs with stuffs.
             *
             * @param array $stuffs {
             *     @type bool $foo
             *     @type int $bar
             * }
             */
             ',
            '<?php
            /**
             * Does stuffs with stuffs.
             *
             * @param array $stuffs {
             *     @type boolean $foo
             *     @type integer $bar
             * }
             */
             ',
        ];

        yield 'fix callback' => [
            '<?php
            /**
             * @method int foo()
             * @property int $foo
             * @property callable $foo
             * @property-read bool $bar
             * @property-write float $baz
             */
             ',
            '<?php
            /**
             * @method integer foo()
             * @property integer $foo
             * @property callback $foo
             * @property-read boolean $bar
             * @property-write double $baz
             */
             ',
            ['types' => ['boolean', 'callback', 'double', 'integer', 'real', 'str']],
        ];

        yield 'fix Windows line endings' => [
            str_replace("\n", "\r\n", '<?php
            /**
             * @return int
             */
             '),
            str_replace("\n", "\r\n", '<?php
            /**
             * @return integer
             */
             '),
        ];
    }
}
