<?php declare(strict_types=1);

/*
 * This file is part of PHP CS Fixer: custom fixers.
 *
 * (c) 2018 Kuba Werłos
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace PhpCsFixer\Tests\Fixer\Comment;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Comment\CommentSurroundedBySpacesFixer
 */
final class CommentSurroundedBySpacesFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<array{0: string, 1?: string}>
     */
    public static function provideFixCases(): iterable
    {
        yield ['<?php $a; //'];
        yield ['<?php $a; ////'];
        yield ['<?php $a; /**/'];
        yield ['<?php $a; /**  foo  */'];
        yield ["<?php AA; /**\tfoo\t*/"];

        yield [
            '<?php
                /*
                 * foo
                 */',
        ];

        yield [
            '<?php $a; //  foo',
        ];

        yield [
            '<?php $a; // foo',
            '<?php $a; //foo',
        ];

        yield [
            '<?php $a; # foo',
            '<?php $a; #foo',
        ];

        yield [
            '<?php $a; #',
        ];

        yield [
            '<?php #[AnAttribute]
              function doFoo() {}',
        ];

        yield [
            '<?php $a; /* foo */',
            '<?php $a; /*foo */',
        ];

        yield [
            '<?php $a; /* foo */',
            '<?php $a; /* foo*/',
        ];

        yield [
            '<?php $a; /* foo */',
            '<?php $a; /*foo*/',
        ];

        yield [
            '<?php $a; /* foo  */',
            '<?php $a; /*foo  */',
        ];

        yield [
            '<?php $a; /*  foo */',
            '<?php $a; /*  foo*/',
        ];

        yield [
            '<?php $a; /** foo */',
            '<?php $a; /**foo*/',
        ];

        yield [
            '<?php $a; /** foo */',
            '<?php $a; /** foo*/',
        ];

        yield [
            '<?php $a; /**** foo ****/',
            '<?php $a; /****foo****/',
        ];

        yield [
            '<?php $a; /* foo */// bar',
            '<?php $a; /*foo*///bar',
        ];

        yield [
            '<?php
                // foo
                //
                // bar
            ',
            '<?php
                //foo
                //
                //bar
            ',
        ];
    }
}
