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

namespace PhpCsFixer\Tests\Fixer\Phpdoc;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 *
 * @internal
 *
 * @covers \PhpCsFixer\AbstractPhpdocTypesFixer
 * @covers \PhpCsFixer\Fixer\Phpdoc\PhpdocScalarFixer
 */
final class PhpdocScalarFixerTest extends AbstractFixerTestCase
{
    /**
     * @param array<string, mixed> $config
     *
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null, array $config = []): void
    {
        $this->fixer->configure($config);
        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): iterable
    {
        yield 'basic fix' => [
            '<?php
            /**
             * @return int
             */'."\n             ",
            '<?php
            /**
             * @return integer
             */'."\n             ",
        ];

        yield 'property fix' => [
            '<?php
            /**
             * @method int foo()
             * @property int $foo
             * @property callable $foo
             * @property-read bool $bar
             * @property-write float $baz
             */'."\n             ",
            '<?php
            /**
             * @method integer foo()
             * @property integer $foo
             * @property callback $foo
             * @property-read boolean $bar
             * @property-write double $baz
             */'."\n             ",
        ];

        yield 'do not modify variables' => [
            '<?php
            /**
             * @param int $integer
             */'."\n             ",
            '<?php
            /**
             * @param integer $integer
             */'."\n             ",
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
             */'."\n             ",
            '<?php
            /**
             * Hello there mr integer!
             *
             * @param integer|real $integer
             * @param int|integer[] $foo
             * @param str|null $bar
             *
             * @return string|boolean
             */'."\n             ",
        ];

        yield 'fix var' => [
            '<?php
            /**
             * @var int Some integer value.
             */'."\n             ",
            '<?php
            /**
             * @var integer Some integer value.
             */'."\n             ",
        ];

        yield 'fix var with more stuff' => [
            '<?php
            /**
             * @var bool|int|Double Booleans, integers and doubles.
             */'."\n             ",
            '<?php
            /**
             * @var boolean|integer|Double Booleans, integers and doubles.
             */'."\n             ",
        ];

        yield 'fix type' => [
            '<?php
            /**
             * @type float
             */'."\n             ",
            '<?php
            /**
             * @type real
             */'."\n             ",
        ];

        yield 'do not fix' => [
            '<?php
            /**
             * @var notaboolean
             */'."\n             ",
        ];

        yield 'complex mix' => [
            '<?php
            /**
             * @var notabooleanthistime|bool|integerr
             */'."\n             ",
            '<?php
            /**
             * @var notabooleanthistime|boolean|integerr
             */'."\n             ",
        ];

        yield 'do not modify complex tag' => [
            '<?php
            /**
             * @Type("boolean")
             */'."\n             ",
        ];

        yield 'do not modify strings' => [
            "<?php
            \$string = '
                /**
                 * @var boolean
                 */
            ';"."\n             ",
        ];

        yield 'empty DocBlock' => [
            '<?php
            /**
             *
             */'."\n             ",
        ];

        yield 'wrong cased Phpdoc tag is not altered' => [
            '<?php
            /**
             * @Param boolean
             *
             * @Return int
             */'."\n             ",
        ];

        yield 'inline doc' => [
            '<?php
            /**
             * Does stuff with stuffs.
             *
             * @param array $stuffs {
             *     @type bool $foo
             *     @type int $bar
             * }
             */'."\n             ",
            '<?php
            /**
             * Does stuff with stuffs.
             *
             * @param array $stuffs {
             *     @type boolean $foo
             *     @type integer $bar
             * }
             */'."\n             ",
        ];

        yield 'fix callback' => [
            '<?php
            /**
             * @method int foo()
             * @property int $foo
             * @property callable $foo
             * @property-read bool $bar
             * @property-write float $baz
             */'."\n             ",
            '<?php
            /**
             * @method integer foo()
             * @property integer $foo
             * @property callback $foo
             * @property-read boolean $bar
             * @property-write double $baz
             */'."\n             ",
            ['types' => ['boolean', 'callback', 'double', 'integer', 'real', 'str']],
        ];

        yield 'fix Windows line endings' => [
            str_replace("\n", "\r\n", '<?php
            /**
             * @return int
             */'."\n             "),
            str_replace("\n", "\r\n", '<?php
            /**
             * @return integer
             */'."\n             "),
        ];
    }
}
