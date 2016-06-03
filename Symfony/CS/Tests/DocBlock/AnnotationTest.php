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

namespace Symfony\CS\Tests\DocBlock;

use Symfony\CS\DocBlock\Annotation;
use Symfony\CS\DocBlock\DocBlock;
use Symfony\CS\DocBlock\Line;

/**
 * @author Graham Campbell <graham@alt-three.com>
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

    /**
     * @dataProvider provideTypesCases
     */
    public function testTypes($expected, $new, $input, $output)
    {
        $line = new Line($input);
        $tag = new Annotation(array($line));

        $this->assertSame($expected, $tag->getTypes());

        $tag->setTypes($new);

        $this->assertSame($new, $tag->getTypes());

        $this->assertSame($output, $line->getContent());
    }

    public function provideTypesCases()
    {
        return array(
            array(array('Foo', 'null'), array('Bar[]'), '     * @param Foo|null $foo', '     * @param Bar[] $foo'),
            array(array('false'), array('bool'), '*   @return            false', '*   @return            bool'),
            array(array('RUNTIMEEEEeXCEPTION'), array('Throwable'), "\t@throws\t  \t RUNTIMEEEEeXCEPTION\t\t\t\t\t\t\t\n\n\n", "\t@throws\t  \t Throwable\t\t\t\t\t\t\t\n\n\n"),
            array(array('string'), array('string', 'null'), ' * @method string getString()', ' * @method string|null getString()'),
        );
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage This tag does not support types
     */
    public function testGetTypesOnBadTag()
    {
        $tag = new Annotation(array(new Line(' * @deprecated since 1.2')));

        $tag->getTypes();
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage This tag does not support types
     */
    public function testSetTypesOnBadTag()
    {
        $tag = new Annotation(array(new Line(' * @author Chuck Norris')));

        $tag->setTypes(array('string'));
    }

    public function testGetTagsWithTypes()
    {
        $tags = Annotation::getTagsWithTypes();
        $this->assertInternalType('array', $tags);
        foreach ($tags as $tag) {
            $this->assertInternalType('string', $tag);
            $this->assertNotEmpty($tag);
        }
    }
}
