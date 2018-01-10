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

use PhpCsFixer\DocBlock\Annotation;
use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\DocBlock\Line;
use PhpCsFixer\Tests\TestCase;

/**
 * @author Graham Campbell <graham@alt-three.com>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\DocBlock\Annotation
 */
final class AnnotationTest extends TestCase
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
        "     * @throws \\Exception asdnjkasd\n     *\n     * asdasdasdasdasdasdasdasd\n     * kasdkasdkbasdasdasdjhbasdhbasjdbjasbdjhb\n",
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
     * @var string[]
     */
    private static $tags = array('param', 'param', 'param', 'throws', 'return');

    /**
     * @param int    $index
     * @param string $content
     *
     * @dataProvider provideGetContentCases
     */
    public function testGetContent($index, $content)
    {
        $doc = new DocBlock(self::$sample);
        $annotation = $doc->getAnnotation($index);

        $this->assertSame($content, $annotation->getContent());
        $this->assertSame($content, (string) $annotation);
    }

    public function provideGetContentCases()
    {
        $cases = array();

        foreach (self::$content as $index => $content) {
            $cases[] = array($index, $content);
        }

        return $cases;
    }

    /**
     * @param int $index
     * @param int $start
     *
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
     * @param int $index
     * @param int $end
     *
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
     * @param int    $index
     * @param string $tag
     *
     * @dataProvider provideGetTagCases
     */
    public function testGetTag($index, $tag)
    {
        $doc = new DocBlock(self::$sample);
        $annotation = $doc->getAnnotation($index);

        $this->assertSame($tag, $annotation->getTag()->getName());
    }

    public function provideGetTagCases()
    {
        $cases = array();

        foreach (self::$tags as $index => $tag) {
            $cases[] = array($index, $tag);
        }

        return $cases;
    }

    /**
     * @param int $index
     * @param int $start
     * @param int $end
     *
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
     * @param string   $input
     * @param string[] $expected
     *
     * @dataProvider provideTypeParsingCases
     */
    public function testTypeParsing($input, array $expected)
    {
        $tag = new Annotation(array(new Line($input)));

        $this->assertSame($expected, $tag->getTypes());
    }

    public function provideTypeParsingCases()
    {
        return array(
            array(
                ' * @method int method()',
                array('int'),
            ),
            array(
                ' * @method int[] method()',
                array('int[]'),
            ),
            array(
                ' * @method int[]|null method()',
                array('int[]', 'null'),
            ),
            array(
                ' * @method int[]|null|?int|array method()',
                array('int[]', 'null', '?int', 'array'),
            ),
            array(
                ' * @method null|Foo\Bar|\Baz\Bax|int[] method()',
                array('null', 'Foo\Bar', '\Baz\Bax', 'int[]'),
            ),
            array(
                ' * @method gen<int> method()',
                array('gen<int>'),
            ),
            array(
                ' * @method int|gen<int> method()',
                array('int', 'gen<int>'),
            ),
            array(
                ' * @method \int|\gen<\int, \bool> method()',
                array('\int', '\gen<\int, \bool>'),
            ),
            array(
                ' * @method gen<int,  int> method()',
                array('gen<int,  int>'),
            ),
            array(
                ' * @method gen<int,  bool|string> method()',
                array('gen<int,  bool|string>'),
            ),
            array(
                ' * @method gen<int,  string[]> method() <> a',
                array('gen<int,  string[]>'),
            ),
            array(
                ' * @method gen<int,  gener<string, bool>> method() foo <a >',
                array('gen<int,  gener<string, bool>>'),
            ),
            array(
                ' * @method gen<int,  gener<string, null|bool>> method()',
                array('gen<int,  gener<string, null|bool>>'),
            ),
            array(
                ' * @method null|gen<int,  gener<string, bool>>|int|string[] method() foo <a >',
                array('null', 'gen<int,  gener<string, bool>>', 'int', 'string[]'),
            ),
            array(
                ' * @method null|gen<int,  gener<string, bool>>|int|array<int, string>|string[] method() foo <a >',
                array('null', 'gen<int,  gener<string, bool>>', 'int', 'array<int, string>', 'string[]'),
            ),
            array(
                '/** @return    this */',
                array('this'),
            ),
            array(
                '/** @return    @this */',
                array('@this'),
            ),
            array(
                '/** @return $SELF|int */',
                array('$SELF', 'int'),
            ),
            array(
                '/** @var array<string|int, string>',
                array('array<string|int, string>'),
            ),
        );
    }

    /**
     * @param string[] $expected
     * @param string[] $new
     * @param string   $input
     * @param string   $output
     *
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
            array(array('RUNTIMEEEEeXCEPTION'), array('Throwable'), "* \t@throws\t  \t RUNTIMEEEEeXCEPTION\t\t\t\t\t\t\t\n\n\n", "* \t@throws\t  \t Throwable\t\t\t\t\t\t\t\n\n\n"),
            array(array('RUNTIMEEEEeXCEPTION'), array('Throwable'), "*\t@throws\t  \t RUNTIMEEEEeXCEPTION\t\t\t\t\t\t\t\n\n\n", "*\t@throws\t  \t Throwable\t\t\t\t\t\t\t\n\n\n"),
            array(array('RUNTIMEEEEeXCEPTION'), array('Throwable'), "*@throws\t  \t RUNTIMEEEEeXCEPTION\t\t\t\t\t\t\t\n\n\n", "*@throws\t  \t Throwable\t\t\t\t\t\t\t\n\n\n"),
            array(array('string'), array('string', 'null'), ' * @method string getString()', ' * @method string|null getString()'),
        );
    }

    public function testGetTypesOnBadTag()
    {
        $this->setExpectedException(
            'RuntimeException',
            'This tag does not support types'
        );

        $tag = new Annotation(array(new Line(' * @deprecated since 1.2')));

        $tag->getTypes();
    }

    public function testSetTypesOnBadTag()
    {
        $this->setExpectedException(
            'RuntimeException',
            'This tag does not support types'
        );

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
