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
use PhpCsFixer\DocBlock\Line;
use PhpCsFixer\Tests\TestCase;

/**
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 *
 * @internal
 *
 * @covers \PhpCsFixer\DocBlock\Line
 */
final class LineTest extends TestCase
{
    /**
     * This represents the content an entire docblock.
     *
     * @var string
     */
    private static $sample = '/**
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
     */';

    /**
     * This represents the content of each line.
     *
     * @var string[]
     */
    private static $content = [
        "/**\n",
        "     * Test docblock.\n",
        "     *\n",
        "     * @param string \$hello\n",
        "     * @param bool \$test Description\n",
        "     *        extends over many lines\n",
        "     *\n",
        "     * @param adkjbadjasbdand \$asdnjkasd\n",
        "     *\n",
        "     * @throws \\Exception asdnjkasd\n",
        "     * asdasdasdasdasdasdasdasd\n",
        "     * kasdkasdkbasdasdasdjhbasdhbasjdbjasbdjhb\n",
        "     *\n",
        "     * @return void\n",
        '     */',
    ];

    /**
     * This represents the if each line is "useful".
     *
     * @var bool[]
     */
    private static $useful = [
        false,
        true,
        false,
        true,
        true,
        true,
        false,
        true,
        false,
        true,
        true,
        true,
        false,
        true,
        false,
    ];

    /**
     * This represents the if each line "contains a tag".
     *
     * @var bool[]
     */
    private static $tag = [
        false,
        false,
        false,
        true,
        true,
        false,
        false,
        true,
        false,
        true,
        false,
        false,
        false,
        true,
        false,
    ];

    /**
     * @dataProvider provideLinesCases
     */
    public function testPosAndContent(int $pos, string $content): void
    {
        $doc = new DocBlock(self::$sample);
        $line = $doc->getLine($pos);

        self::assertSame($content, $line->getContent());
        self::assertSame($content, (string) $line);
    }

    /**
     * @dataProvider provideLinesCases
     */
    public function testStartOrEndPos(int $pos): void
    {
        $doc = new DocBlock(self::$sample);
        $line = $doc->getLine($pos);

        self::assertSame(0 === $pos, $line->isTheStart());
        self::assertSame(14 === $pos, $line->isTheEnd());
    }

    public static function provideLinesCases(): iterable
    {
        foreach (self::$content as $index => $content) {
            yield [$index, $content];
        }
    }

    /**
     * @dataProvider provideUsefulCases
     */
    public function testUseful(int $pos, bool $useful): void
    {
        $doc = new DocBlock(self::$sample);
        $line = $doc->getLine($pos);

        self::assertSame($useful, $line->containsUsefulContent());
    }

    public static function provideUsefulCases(): iterable
    {
        foreach (self::$useful as $index => $useful) {
            yield [$index, $useful];
        }
    }

    /**
     * @dataProvider provideTagCases
     */
    public function testTag(int $pos, bool $tag): void
    {
        $doc = new DocBlock(self::$sample);
        $line = $doc->getLine($pos);

        self::assertSame($tag, $line->containsATag());
    }

    public static function provideTagCases(): iterable
    {
        foreach (self::$tag as $index => $tag) {
            yield [$index, $tag];
        }
    }

    public function testSetContent(): void
    {
        $line = new Line("     * @param \$foo Hi!\n");

        self::assertSame("     * @param \$foo Hi!\n", $line->getContent());

        $line->addBlank();
        self::assertSame("     * @param \$foo Hi!\n     *\n", $line->getContent());

        $line->setContent("\t * test\r\n");
        self::assertSame("\t * test\r\n", $line->getContent());

        $line->addBlank();
        self::assertSame("\t * test\r\n\t *\r\n", $line->getContent());

        $line->remove();
        self::assertSame('', $line->getContent());
    }
}
