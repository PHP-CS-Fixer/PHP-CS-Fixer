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

namespace PhpCsFixer\Tests\DocBlock;

use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\DocBlock\ShortDescription;
use PhpCsFixer\Tests\TestCase;

/**
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 *
 * @covers \PhpCsFixer\DocBlock\ShortDescription
 */
final class ShortDescriptionTest extends TestCase
{
    /**
     * @dataProvider provideGetEndCases
     */
    public function testGetEnd(?int $expected, string $input): void
    {
        $doc = new DocBlock($input);
        $shortDescription = new ShortDescription($doc);

        self::assertSame($expected, $shortDescription->getEnd());
    }

    /**
     * @return iterable<int, array{null|int, string}>
     */
    public static function provideGetEndCases(): iterable
    {
        yield [1, '/**
     * Test docblock.
     *
     * @param string $hello
     * @param bool $test Description
     *        extends over many lines
     *
     * @param adkjbadjasbdand $asdnjkasd
     *
     * @throws \Exception asdnjkasd
     * asdasdasdasdasdasdasdasd
     * kasdkasdkbasdasdasdjhbasdhbasjdbjasbdjhb
     *
     * @return void
     */'];

        yield [2, '/**
                  * This is a multi-line
                  * short description.
                  */'];

        yield [3, '/**
                  *
                  *
                  * There might be extra blank lines.
                  *
                  *
                  * And here is description...
                  */'];

        yield [null, '/** */'];

        yield [null, "/**\n * @test\n*/"];
    }
}
