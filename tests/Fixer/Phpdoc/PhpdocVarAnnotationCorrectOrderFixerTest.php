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
 * @internal
 *
 * @author Kuba Werłos <werlos@gmail.com>
 *
 * @covers \PhpCsFixer\Fixer\Phpdoc\PhpdocVarAnnotationCorrectOrderFixer
 */
final class PhpdocVarAnnotationCorrectOrderFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): iterable
    {
        yield [ // It's @param, we care only about @var
            '<?php /** @param $foo Foo */',
        ];

        yield [ // This is already fine
            '<?php /** @var Foo $foo */ ',
        ];

        yield [ // What? Two variables, I'm not touching this
            '<?php /** @var $foo $bar */',
        ];

        yield [ // Two classes are not to touch either
            '<?php /** @var Foo Bar */',
        ];

        yield ['<?php /** @var */'];

        yield ['<?php /** @var $foo */'];

        yield ['<?php /** @var Bar */'];

        yield [
            <<<'EOD'
                <?php
                /**
                 * @var Foo $foo
                 * @var Bar $bar
                 */

                EOD,
            <<<'EOD'
                <?php
                /**
                 * @var $foo Foo
                 * @var $bar Bar
                 */

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                /**
                 * @var Foo $foo Some description
                 */

                EOD,
            <<<'EOD'
                <?php
                /**
                 * @var $foo Foo Some description
                 */

                EOD,
        ];

        yield [
            '<?php /** @var Foo $foo */',
            '<?php /** @var $foo Foo */',
        ];

        yield [
            '<?php /** @type Foo $foo */',
            '<?php /** @type $foo Foo */',
        ];

        yield [
            '<?php /** @var Foo $foo*/',
            '<?php /** @var $foo Foo*/',
        ];

        yield [
            '<?php /** @var Foo[] $foos */',
            '<?php /** @var $foos Foo[] */',
        ];

        yield [
            '<?php /** @Var Foo $foo */',
            '<?php /** @Var $foo Foo */',
        ];

        yield [
            <<<'EOD'
                <?php
                /** @var Foo|Bar|mixed|int $someWeirdLongNAME__123 */

                EOD,
            <<<'EOD'
                <?php
                /** @var $someWeirdLongNAME__123 Foo|Bar|mixed|int */

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                /**
                 * @var Foo $bar long description
                 *               goes here
                 */

                EOD,
            <<<'EOD'
                <?php
                /**
                 * @var $bar Foo long description
                 *               goes here
                 */

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                /** @var array<int, int> $foo */

                EOD,
            <<<'EOD'
                <?php
                /** @var $foo array<int, int> */

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                /** @var array<int, int> $foo Array of something */

                EOD,
            <<<'EOD'
                <?php
                /** @var $foo array<int, int> Array of something */

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                /** @var Foo|array<int, int>|null $foo */

                EOD,
            <<<'EOD'
                <?php
                /** @var $foo Foo|array<int, int>|null */

                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                                class Foo
                                {
                                    /**
                                     * @var $bar
                                     */
                                    private $bar;
                                }
                EOD."\n            ",
        ];
    }
}
