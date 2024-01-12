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
            <<<'EOD'
                <?php
                            /**
                             * @return int
                             */
                EOD."\n             ",
            <<<'EOD'
                <?php
                            /**
                             * @return integer
                             */
                EOD."\n             ",
        ];

        yield 'property fix' => [
            <<<'EOD'
                <?php
                            /**
                             * @method int foo()
                             * @property int $foo
                             * @property callable $foo
                             * @property-read bool $bar
                             * @property-write float $baz
                             */
                EOD."\n             ",
            <<<'EOD'
                <?php
                            /**
                             * @method integer foo()
                             * @property integer $foo
                             * @property callback $foo
                             * @property-read boolean $bar
                             * @property-write double $baz
                             */
                EOD."\n             ",
        ];

        yield 'do not modify variables' => [
            <<<'EOD'
                <?php
                            /**
                             * @param int $integer
                             */
                EOD."\n             ",
            <<<'EOD'
                <?php
                            /**
                             * @param integer $integer
                             */
                EOD."\n             ",
        ];

        yield 'fix with tabs on one line' => [
            "<?php /**\t@return\tbool\t*/",
            "<?php /**\t@return\tboolean\t*/",
        ];

        yield 'fix more things' => [
            <<<'EOD'
                <?php
                            /**
                             * Hello there mr integer!
                             *
                             * @param int|float $integer
                             * @param int|int[] $foo
                             * @param string|null $bar
                             *
                             * @return string|bool
                             */
                EOD."\n             ",
            <<<'EOD'
                <?php
                            /**
                             * Hello there mr integer!
                             *
                             * @param integer|real $integer
                             * @param int|integer[] $foo
                             * @param str|null $bar
                             *
                             * @return string|boolean
                             */
                EOD."\n             ",
        ];

        yield 'fix var' => [
            <<<'EOD'
                <?php
                            /**
                             * @var int Some integer value.
                             */
                EOD."\n             ",
            <<<'EOD'
                <?php
                            /**
                             * @var integer Some integer value.
                             */
                EOD."\n             ",
        ];

        yield 'fix var with more stuff' => [
            <<<'EOD'
                <?php
                            /**
                             * @var bool|int|Double Booleans, integers and doubles.
                             */
                EOD."\n             ",
            <<<'EOD'
                <?php
                            /**
                             * @var boolean|integer|Double Booleans, integers and doubles.
                             */
                EOD."\n             ",
        ];

        yield 'fix type' => [
            <<<'EOD'
                <?php
                            /**
                             * @type float
                             */
                EOD."\n             ",
            <<<'EOD'
                <?php
                            /**
                             * @type real
                             */
                EOD."\n             ",
        ];

        yield 'do not fix' => [
            <<<'EOD'
                <?php
                            /**
                             * @var notaboolean
                             */
                EOD."\n             ",
        ];

        yield 'complex mix' => [
            <<<'EOD'
                <?php
                            /**
                             * @var notabooleanthistime|bool|integerr
                             */
                EOD."\n             ",
            <<<'EOD'
                <?php
                            /**
                             * @var notabooleanthistime|boolean|integerr
                             */
                EOD."\n             ",
        ];

        yield 'do not modify complex tag' => [
            <<<'EOD'
                <?php
                            /**
                             * @Type("boolean")
                             */
                EOD."\n             ",
        ];

        yield 'do not modify strings' => [
            <<<'EOD'
                <?php
                            $string = '
                                /**
                                 * @var boolean
                                 */
                            ';
                EOD."\n             ",
        ];

        yield 'empty DocBlock' => [
            <<<'EOD'
                <?php
                            /**
                             *
                             */
                EOD."\n             ",
        ];

        yield 'wrong cased Phpdoc tag is not altered' => [
            <<<'EOD'
                <?php
                            /**
                             * @Param boolean
                             *
                             * @Return int
                             */
                EOD."\n             ",
        ];

        yield 'inline doc' => [
            <<<'EOD'
                <?php
                            /**
                             * Does stuff with stuffs.
                             *
                             * @param array $stuffs {
                             *     @type bool $foo
                             *     @type int $bar
                             * }
                             */
                EOD."\n             ",
            <<<'EOD'
                <?php
                            /**
                             * Does stuff with stuffs.
                             *
                             * @param array $stuffs {
                             *     @type boolean $foo
                             *     @type integer $bar
                             * }
                             */
                EOD."\n             ",
        ];

        yield 'fix callback' => [
            <<<'EOD'
                <?php
                            /**
                             * @method int foo()
                             * @property int $foo
                             * @property callable $foo
                             * @property-read bool $bar
                             * @property-write float $baz
                             */
                EOD."\n             ",
            <<<'EOD'
                <?php
                            /**
                             * @method integer foo()
                             * @property integer $foo
                             * @property callback $foo
                             * @property-read boolean $bar
                             * @property-write double $baz
                             */
                EOD."\n             ",
            ['types' => ['boolean', 'callback', 'double', 'integer', 'real', 'str']],
        ];

        yield 'fix Windows line endings' => [
            str_replace("\n", "\r\n", <<<'EOD'
                <?php
                            /**
                             * @return int
                             */
                EOD."\n             "),
            str_replace("\n", "\r\n", <<<'EOD'
                <?php
                            /**
                             * @return integer
                             */
                EOD."\n             "),
        ];
    }
}
