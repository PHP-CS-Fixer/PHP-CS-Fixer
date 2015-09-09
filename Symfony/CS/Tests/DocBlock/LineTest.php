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

use Symfony\CS\DocBlock\DocBlock;
use Symfony\CS\DocBlock\Line;

/**
 * @author Graham Campbell <graham@mineuk.com>
 *
 * @internal
 */
final class LineTest extends \PHPUnit_Framework_TestCase
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
    private static $content = array(
        "/**\n",
        "     * Test docblock.\n",
        "     *\n",
        "     * @param string \$hello\n",
        "     * @param bool \$test Description\n",
        "     *        extends over many lines\n",
        "     *\n",
        "     * @param adkjbadjasbdand \$asdnjkasd\n",
        "     *\n",
        "     * @throws \Exception asdnjkasd\n",
        "     * asdasdasdasdasdasdasdasd\n",
        "     * kasdkasdkbasdasdasdjhbasdhbasjdbjasbdjhb\n",
        "     *\n",
        "     * @return void\n",
        '     */',
    );

    /**
     * This represents the if each line is "useful".
     *
     * @var bool[]
     */
    private static $useful = array(
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
    );

    /**
     * This represents the if each line "contains a tag".
     *
     * @var bool[]
     */
    private static $tag = array(
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
    );

    /**
     * @dataProvider provideLines
     */
    public function testPosAndContent($pos, $content)
    {
        $doc = new DocBlock(self::$sample);
        $line = $doc->getLine($pos);

        $this->assertSame($content, $line->getContent());
    }

    /**
     * @dataProvider provideLines
     */
    public function testStarOrEndPos($pos)
    {
        $doc = new DocBlock(self::$sample);
        $line = $doc->getLine($pos);

        switch ($pos) {
            case 0:
                $this->assertTrue($line->isTheStart());
                $this->assertFalse($line->isTheEnd());
                break;
            case 14:
                $this->assertFalse($line->isTheStart());
                $this->assertTrue($line->isTheEnd());
                break;
            default:
                $this->assertFalse($line->isTheStart());
                $this->assertFalse($line->isTheEnd());
        }
    }

    public function provideLines()
    {
        $cases = array();

        foreach (self::$content as $index => $content) {
            $cases[] = array($index, $content);
        }

        return $cases;
    }

    /**
     * @dataProvider provideLinesWithUseful
     */
    public function testUseful($pos, $useful)
    {
        $doc = new DocBlock(self::$sample);
        $line = $doc->getLine($pos);

        $this->assertSame($useful, $line->containsUsefulContent());
    }

    public function provideLinesWithUseful()
    {
        $cases = array();

        foreach (self::$useful as $index => $useful) {
            $cases[] = array($index, $useful);
        }

        return $cases;
    }

    /**
     * @dataProvider provideLinesWithTag
     */
    public function testTag($pos, $tag)
    {
        $doc = new DocBlock(self::$sample);
        $line = $doc->getLine($pos);

        $this->assertSame($tag, $line->containsATag());
    }

    public function provideLinesWithTag()
    {
        $cases = array();

        foreach (self::$tag as $index => $tag) {
            $cases[] = array($index, $tag);
        }

        return $cases;
    }

    public function testSetContent()
    {
        $line = new Line("     * @param \$foo Hi!\n");

        $this->assertSame("     * @param \$foo Hi!\n", $line->getContent());

        $line->addBlank();
        $this->assertSame("     * @param \$foo Hi!\n     *\n", $line->getContent());

        $line->setContent("     * test\n");
        $this->assertSame("     * test\n", $line->getContent());

        $line->remove();
        $this->assertSame('', $line->getContent());
    }
}
