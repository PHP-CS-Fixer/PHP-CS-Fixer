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

    public function provideFixCases(): iterable
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
            '<?php
/**
 * @var Foo $foo
 * @var Bar $bar
 */
',
            '<?php
/**
 * @var $foo Foo
 * @var $bar Bar
 */
',
        ];

        yield [
            '<?php
/**
 * @var Foo $foo Some description
 */
',
            '<?php
/**
 * @var $foo Foo Some description
 */
',
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
            '<?php
/** @var Foo|Bar|mixed|int $someWeirdLongNAME__123 */
',
            '<?php
/** @var $someWeirdLongNAME__123 Foo|Bar|mixed|int */
',
        ];

        yield [
            '<?php
/**
 * @var Foo $bar long description
 *               goes here
 */
',
            '<?php
/**
 * @var $bar Foo long description
 *               goes here
 */
',
        ];

        yield [
            '<?php
/** @var array<int, int> $foo */
',
            '<?php
/** @var $foo array<int, int> */
',
        ];

        yield [
            '<?php
/** @var array<int, int> $foo Array of something */
',
            '<?php
/** @var $foo array<int, int> Array of something */
',
        ];

        yield [
            '<?php
/** @var Foo|array<int, int>|null $foo */
',
            '<?php
/** @var $foo Foo|array<int, int>|null */
',
        ];

        yield [
            '<?php
                class Foo
                {
                    /**
                     * @var $bar
                     */
                    private $bar;
                }
            ',
        ];
    }
}
