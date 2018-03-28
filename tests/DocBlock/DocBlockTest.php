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

namespace PhpCsFixer\Tests\DocBlock;

use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\Tests\TestCase;

/**
 * @author Graham Campbell <graham@alt-three.com>
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

    public function testContent()
    {
        $doc = new DocBlock(self::$sample);

        $this->assertSame(self::$sample, $doc->getContent());
        $this->assertSame(self::$sample, (string) $doc);
    }

    public function testEmptyContent()
    {
        $doc = new DocBlock('');

        $this->assertSame('', $doc->getContent());
    }

    public function testGetLines()
    {
        $doc = new DocBlock(self::$sample);

        $this->assertInternalType('array', $doc->getLines());
        $this->assertCount(15, $doc->getLines());

        foreach ($doc->getLines() as $index => $line) {
            $this->assertInstanceOf(\PhpCsFixer\DocBlock\Line::class, $line);
            $this->assertSame($doc->getLine($index), $line);
        }

        $this->assertEmpty($doc->getLine(15));
    }

    public function testGetAnnotations()
    {
        $doc = new DocBlock(self::$sample);

        $this->assertInternalType('array', $doc->getAnnotations());
        $this->assertCount(5, $doc->getAnnotations());

        foreach ($doc->getAnnotations() as $index => $annotations) {
            $this->assertInstanceOf(\PhpCsFixer\DocBlock\Annotation::class, $annotations);
            $this->assertSame($doc->getAnnotation($index), $annotations);
        }

        $this->assertEmpty($doc->getAnnotation(5));
    }

    public function testGetAnnotationsOfTypeParam()
    {
        $doc = new DocBlock(self::$sample);

        $annotations = $doc->getAnnotationsOfType('param');

        $this->assertInternalType('array', $annotations);
        $this->assertCount(3, $annotations);

        $first = '     * @param string $hello
';
        $second = '     * @param bool $test Description
     *        extends over many lines
';
        $third = '     * @param adkjbadjasbdand $asdnjkasd
';

        $this->assertSame($first, $annotations[0]->getContent());
        $this->assertSame($second, $annotations[1]->getContent());
        $this->assertSame($third, $annotations[2]->getContent());
    }

    public function testGetAnnotationsOfTypeThrows()
    {
        $doc = new DocBlock(self::$sample);

        $annotations = $doc->getAnnotationsOfType('throws');

        $this->assertInternalType('array', $annotations);
        $this->assertCount(1, $annotations);

        $content = '     * @throws \Exception asdnjkasd
     * asdasdasdasdasdasdasdasd
     * kasdkasdkbasdasdasdjhbasdhbasjdbjasbdjhb
';

        $this->assertSame($content, $annotations[0]->getContent());
    }

    public function testGetAnnotationsOfTypeReturn()
    {
        $doc = new DocBlock(self::$sample);

        $annotations = $doc->getAnnotationsOfType('return');

        $this->assertInternalType('array', $annotations);
        $this->assertCount(1, $annotations);

        $content = '     * @return void
';

        $this->assertSame($content, $annotations[0]->getContent());
    }

    public function testGetAnnotationsOfTypeFoo()
    {
        $doc = new DocBlock(self::$sample);

        $annotations = $doc->getAnnotationsOfType('foo');

        $this->assertInternalType('array', $annotations);
        $this->assertCount(0, $annotations);
    }
}
