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
use PhpCsFixer\DocBlock\Line;
use PHPUnit\Framework\TestCase;

/**
 * @author Graham Campbell <graham@alt-three.com>
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
     * @param int    $pos
     * @param string $content
     *
     * @dataProvider provideLinesCases
     */
    public function testPosAndContent($pos, $content)
    {
        $doc = new DocBlock(self::$sample);
        $line = $doc->getLine($pos);

        $this->assertSame($content, $line->getContent());
    }

    /**
     * @param int $pos
     *
     * @dataProvider provideLinesCases
     */
    public function testStartOrEndPos($pos)
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

    public function provideLinesCases()
    {
        $cases = array();

        foreach (self::$content as $index => $content) {
            $cases[] = array($index, $content);
        }

        return $cases;
    }

    /**
     * @param int  $pos
     * @param bool $useful
     *
     * @dataProvider provideLinesWithUsefulCases
     */
    public function testUseful($pos, $useful)
    {
        $doc = new DocBlock(self::$sample);
        $line = $doc->getLine($pos);

        $this->assertSame($useful, $line->containsUsefulContent());
    }

    public function provideLinesWithUsefulCases()
    {
        $cases = array();

        foreach (self::$useful as $index => $useful) {
            $cases[] = array($index, $useful);
        }

        return $cases;
    }

    /**
     * @param int  $pos
     * @param bool $tag
     *
     * @dataProvider provideLinesWithTagCases
     */
    public function testTag($pos, $tag)
    {
        $doc = new DocBlock(self::$sample);
        $line = $doc->getLine($pos);

        $this->assertSame($tag, $line->containsATag());
    }

    public function provideLinesWithTagCases()
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

        $line->setContent("\t * test\r\n");
        $this->assertSame("\t * test\r\n", $line->getContent());

        $line->addBlank();
        $this->assertSame("\t * test\r\n\t *\r\n", $line->getContent());

        $line->remove();
        $this->assertSame('', $line->getContent());
    }
}
