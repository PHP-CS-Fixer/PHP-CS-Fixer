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
use PhpCsFixer\Tests\TestCase;

/**
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 *
 * @internal
 *
 * @covers \PhpCsFixer\DocBlock\DocBlock
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class DocBlockTest extends TestCase
{
    /**
     * This represents the content an entire docblock.
     */
    private static string $sample = '/**
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

    public function testContent(): void
    {
        $doc = new DocBlock(self::$sample);

        self::assertSame(self::$sample, $doc->getContent());
        self::assertSame(self::$sample, (string) $doc);
    }

    public function testEmptyContent(): void
    {
        $doc = new DocBlock('');

        self::assertSame('', $doc->getContent());
    }

    public function testGetLines(): void
    {
        $doc = new DocBlock(self::$sample);
        $lines = $doc->getLines();

        self::assertCount(15, $lines);

        foreach ($lines as $index => $line) {
            self::assertSame($doc->getLine($index), $line);
        }

        self::assertEmpty($doc->getLine(15));
    }

    public function testGetAnnotations(): void
    {
        $doc = new DocBlock(self::$sample);
        $annotations = $doc->getAnnotations();

        self::assertCount(5, $annotations);

        foreach ($annotations as $index => $annotation) {
            self::assertSame($doc->getAnnotation($index), $annotation);
        }

        self::assertEmpty($doc->getAnnotation(5));
    }

    public function testGetAnnotationsOfTypeParam(): void
    {
        $doc = new DocBlock(self::$sample);
        $annotations = $doc->getAnnotationsOfType('param');

        self::assertCount(3, $annotations);

        $first = '     * @param string $hello
';
        $second = '     * @param bool $test Description
     *        extends over many lines
';
        $third = '     * @param adkjbadjasbdand $asdnjkasd
';

        self::assertSame($first, $annotations[0]->getContent());
        self::assertSame($second, $annotations[1]->getContent());
        self::assertSame($third, $annotations[2]->getContent());
    }

    public function testGetAnnotationsOfTypeThrows(): void
    {
        $doc = new DocBlock(self::$sample);
        $annotations = $doc->getAnnotationsOfType('throws');

        self::assertCount(1, $annotations);

        $content = '     * @throws \Exception asdnjkasd
     * asdasdasdasdasdasdasdasd
     * kasdkasdkbasdasdasdjhbasdhbasjdbjasbdjhb
';

        self::assertSame($content, $annotations[0]->getContent());
    }

    public function testGetAnnotationsOfTypeReturn(): void
    {
        $doc = new DocBlock(self::$sample);
        $annotations = $doc->getAnnotationsOfType('return');

        self::assertCount(1, $annotations);

        $content = '     * @return void
';

        self::assertSame($content, $annotations[0]->getContent());
    }

    public function testGetAnnotationsOfTypeFoo(): void
    {
        $doc = new DocBlock(self::$sample);
        $annotations = $doc->getAnnotationsOfType('foo');

        self::assertCount(0, $annotations);
    }

    public function testIsMultiLine(): void
    {
        $doc = new DocBlock(self::$sample);

        self::assertTrue($doc->isMultiLine());
    }

    /**
     * @dataProvider provideMakeMultiLineCases
     */
    public function testMakeMultiLine(string $inputDocBlock, ?string $outputDocBlock = null, string $indent = '', string $newLine = "\n"): void
    {
        $doc = new DocBlock($inputDocBlock);
        $doc->makeMultiLine($indent, $newLine);

        if (null === $outputDocBlock) {
            $outputDocBlock = $inputDocBlock;
        }

        self::assertSame($outputDocBlock, $doc->getContent());
    }

    /**
     * @return iterable<string, array{0: string, 1?: string, 2?: string, 3?: string}>
     */
    public static function provideMakeMultiLineCases(): iterable
    {
        yield 'It keeps a multi line doc block as is' => [
            "/**\n * Hello\n */",
        ];

        yield 'It keeps a multi line doc block as is with multiple annotations' => [
            "/**\n * @foo\n *@bar\n */",
        ];

        yield 'It keeps a multi line doc block with indentation' => [
            "/**\n\t *@foo\n\t */",
        ];

        yield 'It Converts a single line to multi line with no indentation' => [
            '/** Hello */',
            "/**\n * Hello\n */",
        ];

        yield 'It Converts a single line to multi line with correct indentation' => [
            '/** Hello */',
            "/**\n     * Hello\n     */",
            '    ',
        ];

        yield 'It Converts a single line to multi line with correct indentation and Line ending' => [
            '/** Hello */',
            "/**\r\n     * Hello\r\n     */",
            '    ',
            "\r\n",
        ];
    }

    /**
     * @dataProvider provideMakeSingleLineCases
     */
    public function testMakeSingleLine(string $inputDocBlock, ?string $outputDocBlock = null): void
    {
        $doc = new DocBlock($inputDocBlock);
        $doc->makeSingleLine();

        if (null === $outputDocBlock) {
            $outputDocBlock = $inputDocBlock;
        }

        self::assertSame($outputDocBlock, $doc->getContent());
    }

    /**
     * @return iterable<string, array{0: string, 1?: string}>
     */
    public static function provideMakeSingleLineCases(): iterable
    {
        yield 'It keeps a single line doc block as is' => [
            '/** Hello */',
        ];

        yield 'It converts a multi line doc block to a single line' => [
            "/**\n * Hello\n */",
            '/** Hello */',
        ];

        yield 'It converts a multi line doc block to a single line with annotation' => [
            "/**\n * @foo\n */",
            '/** @foo */',
        ];

        yield 'It converts a multi line doc block to a single line multiple empty lines' => [
            "/**\n * @foo\n *\n *\n *\n * */",
            '/** @foo */',
        ];

        yield 'It changes an empty doc block to single line' => [
            "/**\n *\n */",
            '/**  */',
        ];

        yield 'It does not change a multi line doc block if it can\'t' => [
            self::$sample,
        ];
    }
}
