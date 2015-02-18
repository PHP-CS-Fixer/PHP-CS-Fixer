<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\DocBlock;

use Symfony\CS\DocBlock\Annotation;
use Symfony\CS\DocBlock\DocBlock;

/**
 * @author Graham Campbell <graham@mineuk.com>
 */
class AnnotationTest extends \PHPUnit_Framework_TestCase
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
     *
     * asdasdasdasdasdasdasdasd
     * kasdkasdkbasdasdasdjhbasdhbasjdbjasbdjhb
     *
     * @return void
     */';

    /**
     * This represents the content of each annotation.
     *
     * @var string[]
     */
    private static $content = array(
        "     * @param string \$hello\n",
        "     * @param bool \$test Description\n     *        extends over many lines\n",
        "     * @param adkjbadjasbdand \$asdnjkasd\n",
        "     * @throws \Exception asdnjkasd\n     *\n     * asdasdasdasdasdasdasdasd\n     * kasdkasdkbasdasdasdjhbasdhbasjdbjasbdjhb\n",
        "     * @return void\n",
    );

    /**
     * This represents the start indexes of each annotation.
     *
     * @var int[]
     */
    private static $start = array(3, 4, 7, 9, 14);

    /**
     * This represents the start indexes of each annotation.
     *
     * @var int[]
     */
    private static $end = array(3, 5, 7, 12, 14);

    /**
     * This represents the tag type of each annotation.
     *
     * @var int[]
     */
    private static $tags = array('param', 'param', 'param', 'throws', 'return');

    /**
     * @dataProvider provideContent
     */
    public function testGetContent($index, $content)
    {
        $doc = new DocBlock(self::$sample);
        $annotation = $doc->getAnnotation($index);

        $this->assertSame($content, $annotation->getContent());
        $this->assertSame($content, (string) $annotation);
    }

    public function provideContent()
    {
        $cases = array();

        foreach (self::$content as $index => $content) {
            $cases[] = array($index, $content);
        }

        return $cases;
    }

    /**
     * @dataProvider provideStartCases
     */
    public function testStart($index, $start)
    {
        $doc = new DocBlock(self::$sample);
        $annotation = $doc->getAnnotation($index);

        $this->assertSame($start, $annotation->getStart());
    }

    public function provideStartCases()
    {
        $cases = array();

        foreach (self::$start as $index => $start) {
            $cases[] = array($index, $start);
        }

        return $cases;
    }

    /**
     * @dataProvider provideEndCases
     */
    public function testEnd($index, $end)
    {
        $doc = new DocBlock(self::$sample);
        $annotation = $doc->getAnnotation($index);

        $this->assertSame($end, $annotation->getEnd());
    }

    public function provideEndCases()
    {
        $cases = array();

        foreach (self::$end as $index => $end) {
            $cases[] = array($index, $end);
        }

        return $cases;
    }

    /**
     * @dataProvider provideTags
     */
    public function testGetTag($index, $tag)
    {
        $doc = new DocBlock(self::$sample);
        $annotation = $doc->getAnnotation($index);

        $this->assertSame($tag, $annotation->getTag()->getName());
    }

    public function provideTags()
    {
        $cases = array();

        foreach (self::$tags as $index => $tag) {
            $cases[] = array($index, $tag);
        }

        return $cases;
    }

    /**
     * @dataProvider provideRemoveCases
     */
    public function testRemove($index, $start, $end)
    {
        $doc = new DocBlock(self::$sample);
        $annotation = $doc->getAnnotation($index);

        $annotation->remove();
        $this->assertSame('', $annotation->getContent());
        $this->assertSame('', $doc->getLine($start)->getContent());
        $this->assertSame('', $doc->getLine($end)->getContent());
    }

    public function provideRemoveCases()
    {
        $cases = array();

        foreach (self::$start as $index => $start) {
            $cases[] = array($index, $start, self::$end[$index]);
        }

        return $cases;
    }
}
