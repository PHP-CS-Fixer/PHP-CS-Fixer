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
 */
final class DocBlockTest extends TestCase
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

    public function testContent(): void
    {
        $doc = new DocBlock(self::$sample);

        static::assertSame(self::$sample, $doc->getContent());
        static::assertSame(self::$sample, (string) $doc);
    }

    public function testEmptyContent(): void
    {
        $doc = new DocBlock('');

        static::assertSame('', $doc->getContent());
    }

    public function testGetLines(): void
    {
        $doc = new DocBlock(self::$sample);
        $lines = $doc->getLines();

        static::assertCount(15, $lines);

        foreach ($lines as $index => $line) {
            static::assertInstanceOf(\PhpCsFixer\DocBlock\Line::class, $line);
            static::assertSame($doc->getLine($index), $line);
        }

        static::assertEmpty($doc->getLine(15));
    }

    public function testGetAnnotations(): void
    {
        $doc = new DocBlock(self::$sample);
        $annotations = $doc->getAnnotations();

        static::assertCount(5, $annotations);

        foreach ($annotations as $index => $annotation) {
            static::assertInstanceOf(\PhpCsFixer\DocBlock\Annotation::class, $annotation);
            static::assertSame($doc->getAnnotation($index), $annotation);
        }

        static::assertEmpty($doc->getAnnotation(5));
    }

    public function testGetAnnotationsOfTypeParam(): void
    {
        $doc = new DocBlock(self::$sample);
        $annotations = $doc->getAnnotationsOfType('param');

        static::assertCount(3, $annotations);

        $first = '     * @param string $hello
';
        $second = '     * @param bool $test Description
     *        extends over many lines
';
        $third = '     * @param adkjbadjasbdand $asdnjkasd
';

        static::assertSame($first, $annotations[0]->getContent());
        static::assertSame($second, $annotations[1]->getContent());
        static::assertSame($third, $annotations[2]->getContent());
    }

    public function testGetAnnotationsOfTypeThrows(): void
    {
        $doc = new DocBlock(self::$sample);
        $annotations = $doc->getAnnotationsOfType('throws');

        static::assertCount(1, $annotations);

        $content = '     * @throws \Exception asdnjkasd
     * asdasdasdasdasdasdasdasd
     * kasdkasdkbasdasdasdjhbasdhbasjdbjasbdjhb
';

        static::assertSame($content, $annotations[0]->getContent());
    }

    public function testGetAnnotationsOfTypeReturn(): void
    {
        $doc = new DocBlock(self::$sample);
        $annotations = $doc->getAnnotationsOfType('return');

        static::assertCount(1, $annotations);

        $content = '     * @return void
';

        static::assertSame($content, $annotations[0]->getContent());
    }

    public function testGetAnnotationsOfTypeFoo(): void
    {
        $doc = new DocBlock(self::$sample);
        $annotations = $doc->getAnnotationsOfType('foo');

        static::assertCount(0, $annotations);
    }

    public function testIsMultiLIne(): void
    {
        $doc = new DocBlock(self::$sample);

        static::assertTrue($doc->isMultiLine());
    }

    /**
     * @dataProvider provideDocBlocksToConvertToMultiLineCases
     */
    public function testMakeMultiLIne(string $inputDocBlock, string $outputDocBlock = null, string $indent = '', string $newLine = "\n"): void
    {
        $doc = new DocBlock($inputDocBlock);
        $doc->makeMultiLine($indent, $newLine);

        if (null === $outputDocBlock) {
            $outputDocBlock = $inputDocBlock;
        }

        static::assertSame($outputDocBlock, $doc->getContent());
    }

    public static function provideDocBlocksToConvertToMultiLineCases(): array
    {
        return [
            'It keeps a multi line doc block as is' => [
                "/**\n * Hello\n */",
            ],
            'It keeps a multi line doc block as is with multiple annotations' => [
                "/**\n * @foo\n *@bar\n */",
            ],
            'It keeps a multi line doc block with indentation' => [
                "/**\n\t *@foo\n\t */",
            ],
            'It Converts a single line to multi line with no indentation' => [
                '/** Hello */',
                "/**\n * Hello\n */",
            ],
            'It Converts a single line to multi line with correct indentation' => [
                '/** Hello */',
                "/**\n     * Hello\n     */",
                '    ',
            ],
            'It Converts a single line to multi line with correct indentation and Line ending' => [
                '/** Hello */',
                "/**\r\n     * Hello\r\n     */",
                '    ',
                "\r\n",
            ],
        ];
    }

    /**
     * @dataProvider provideDocBlocksToConvertToSingleLineCases
     */
    public function testMakeSingleLine(string $inputDocBlock, string $outputDocBlock = null): void
    {
        $doc = new DocBlock($inputDocBlock);
        $doc->makeSingleLine();

        if (null === $outputDocBlock) {
            $outputDocBlock = $inputDocBlock;
        }

        static::assertSame($outputDocBlock, $doc->getContent());
    }

    public static function provideDocBlocksToConvertToSingleLineCases(): array
    {
        return [
            'It keeps a single line doc block as is' => [
                '/** Hello */',
            ],
            'It converts a multi line doc block to a single line' => [
                "/**\n * Hello\n */",
                '/** Hello */',
            ],
            'It converts a multi line doc block to a single line with annotation' => [
                "/**\n * @foo\n */",
                '/** @foo */',
            ],
            'It converts a multi line doc block to a single line multiple empty lines' => [
                "/**\n * @foo\n *\n *\n *\n * */",
                '/** @foo */',
            ],
            'It changes an empty doc block to single line' => [
                "/**\n *\n */",
                '/**  */',
            ],
            'It does not change a multi line doc block if it can\'t' => [
                self::$sample,
            ],
        ];
    }
}
