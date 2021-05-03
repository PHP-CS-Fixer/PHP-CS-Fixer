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

use PhpCsFixer\DocBlock\Annotation;
use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\DocBlock\Line;
use PhpCsFixer\DocBlock\TypeExpression;
use PhpCsFixer\Tests\TestCase;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\NamespaceAnalysis;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\NamespaceUseAnalysis;

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
    private static $content = [
        "     * @param string \$hello\n",
        "     * @param bool \$test Description\n     *        extends over many lines\n",
        "     * @param adkjbadjasbdand \$asdnjkasd\n",
        "     * @throws \\Exception asdnjkasd\n     *\n     * asdasdasdasdasdasdasdasd\n     * kasdkasdkbasdasdasdjhbasdhbasjdbjasbdjhb\n",
        "     * @return void\n",
    ];

    /**
     * This represents the start indexes of each annotation.
     *
     * @var int[]
     */
    private static $start = [3, 4, 7, 9, 14];

    /**
     * This represents the start indexes of each annotation.
     *
     * @var int[]
     */
    private static $end = [3, 5, 7, 12, 14];

    /**
     * This represents the tag type of each annotation.
     *
     * @var string[]
     */
    private static $tags = ['param', 'param', 'param', 'throws', 'return'];

    /**
     * @dataProvider provideGetContentCases
     */
    public function testGetContent(int $index, string $content): void
    {
        $doc = new DocBlock(self::$sample);
        $annotation = $doc->getAnnotation($index);

        static::assertSame($content, $annotation->getContent());
        static::assertSame($content, (string) $annotation);
    }

    public function provideGetContentCases()
    {
        $cases = [];

        foreach (self::$content as $index => $content) {
            $cases[] = [$index, $content];
        }

        return $cases;
    }

    /**
     * @dataProvider provideStartCases
     */
    public function testStart(int $index, int $start): void
    {
        $doc = new DocBlock(self::$sample);
        $annotation = $doc->getAnnotation($index);

        static::assertSame($start, $annotation->getStart());
    }

    public function provideStartCases()
    {
        $cases = [];

        foreach (self::$start as $index => $start) {
            $cases[] = [$index, $start];
        }

        return $cases;
    }

    /**
     * @dataProvider provideEndCases
     */
    public function testEnd(int $index, int $end): void
    {
        $doc = new DocBlock(self::$sample);
        $annotation = $doc->getAnnotation($index);

        static::assertSame($end, $annotation->getEnd());
    }

    public function provideEndCases()
    {
        $cases = [];

        foreach (self::$end as $index => $end) {
            $cases[] = [$index, $end];
        }

        return $cases;
    }

    /**
     * @dataProvider provideGetTagCases
     */
    public function testGetTag(int $index, string $tag): void
    {
        $doc = new DocBlock(self::$sample);
        $annotation = $doc->getAnnotation($index);

        static::assertSame($tag, $annotation->getTag()->getName());
    }

    public function provideGetTagCases()
    {
        $cases = [];

        foreach (self::$tags as $index => $tag) {
            $cases[] = [$index, $tag];
        }

        return $cases;
    }

    /**
     * @dataProvider provideRemoveCases
     */
    public function testRemove(int $index, int $start, int $end): void
    {
        $doc = new DocBlock(self::$sample);
        $annotation = $doc->getAnnotation($index);

        $annotation->remove();
        static::assertSame('', $annotation->getContent());
        static::assertSame('', $doc->getLine($start)->getContent());
        static::assertSame('', $doc->getLine($end)->getContent());
    }

    public function provideRemoveCases()
    {
        $cases = [];

        foreach (self::$start as $index => $start) {
            $cases[] = [$index, $start, self::$end[$index]];
        }

        return $cases;
    }

    /**
     * @dataProvider provideRemoveEdgeCasesCases
     */
    public function testRemoveEdgeCases(string $expected, string $input): void
    {
        $doc = new DocBlock($input);
        $annotation = $doc->getAnnotation(0);

        $annotation->remove();
        static::assertSame($expected, $doc->getContent());
    }

    public function provideRemoveEdgeCasesCases()
    {
        return [
            // Single line
            ['', '/** @return null*/'],
            ['', '/** @return null */'],
            ['', '/** @return null  */'],

            // Multi line, annotation on start line
            [
                '/**
                 */',
                '/** @return null
                 */',
            ],
            [
                '/**
                 */',
                '/** @return null '.'
                 */',
            ],
            // Multi line, annotation on end line
            [
                '/**
                 */',
                '/**
                 * @return null*/',
            ],
            [
                '/**
                 */',
                '/**
                 * @return null */',
            ],
        ];
    }

    /**
     * @param string[] $expected
     *
     * @dataProvider provideTypeParsingCases
     */
    public function testTypeParsing(string $input, array $expected): void
    {
        $tag = new Annotation([new Line($input)]);

        static::assertSame($expected, $tag->getTypes());
    }

    public function provideTypeParsingCases()
    {
        return [
            [
                ' * @method int method()',
                ['int'],
            ],
            [
                " * @return int[]\r",
                ['int[]'],
            ],
            [
                " * @return int[]\r\n",
                ['int[]'],
            ],
            [
                ' * @method Foo[][] method()',
                ['Foo[][]'],
            ],
            [
                ' * @method int[] method()',
                ['int[]'],
            ],
            [
                ' * @method int[]|null method()',
                ['int[]', 'null'],
            ],
            [
                ' * @method int[]|null|?int|array method()',
                ['int[]', 'null', '?int', 'array'],
            ],
            [
                ' * @method null|Foo\Bar|\Baz\Bax|int[] method()',
                ['null', 'Foo\Bar', '\Baz\Bax', 'int[]'],
            ],
            [
                ' * @method gen<int> method()',
                ['gen<int>'],
            ],
            [
                ' * @method int|gen<int> method()',
                ['int', 'gen<int>'],
            ],
            [
                ' * @method \int|\gen<\int, \bool> method()',
                ['\int', '\gen<\int, \bool>'],
            ],
            [
                ' * @method gen<int,  int> method()',
                ['gen<int,  int>'],
            ],
            [
                ' * @method gen<int,  bool|string> method()',
                ['gen<int,  bool|string>'],
            ],
            [
                ' * @method gen<int,  string[]> method() <> a',
                ['gen<int,  string[]>'],
            ],
            [
                ' * @method gen<int,  gener<string, bool>> method() foo <a >',
                ['gen<int,  gener<string, bool>>'],
            ],
            [
                ' * @method gen<int,  gener<string, null|bool>> method()',
                ['gen<int,  gener<string, null|bool>>'],
            ],
            [
                ' * @method null|gen<int,  gener<string, bool>>|int|string[] method() foo <a >',
                ['null', 'gen<int,  gener<string, bool>>', 'int', 'string[]'],
            ],
            [
                ' * @method null|gen<int,  gener<string, bool>>|int|array<int, string>|string[] method() foo <a >',
                ['null', 'gen<int,  gener<string, bool>>', 'int', 'array<int, string>', 'string[]'],
            ],
            [
                '/** @return    this */',
                ['this'],
            ],
            [
                '/** @return    @this */',
                ['@this'],
            ],
            [
                '/** @return $SELF|int */',
                ['$SELF', 'int'],
            ],
            [
                '/** @var array<string|int, string>',
                ['array<string|int, string>'],
            ],
            [
                " * @return int\n",
                ['int'],
            ],
            [
                " * @return int\r\n",
                ['int'],
            ],
        ];
    }

    /**
     * @param string[] $expected
     * @param string[] $new
     *
     * @dataProvider provideTypesCases
     */
    public function testTypes(array $expected, array $new, string $input, string $output): void
    {
        $line = new Line($input);
        $tag = new Annotation([$line]);

        static::assertSame($expected, $tag->getTypes());

        $tag->setTypes($new);

        static::assertSame($new, $tag->getTypes());

        static::assertSame($output, $line->getContent());
    }

    public function provideTypesCases()
    {
        return [
            [['Foo', 'null'], ['Bar[]'], '     * @param Foo|null $foo', '     * @param Bar[] $foo'],
            [['false'], ['bool'], '*   @return            false', '*   @return            bool'],
            [['RUNTIMEEEEeXCEPTION'], [\Throwable::class], "* \t@throws\t  \t RUNTIMEEEEeXCEPTION\t\t\t\t\t\t\t\n\n\n", "* \t@throws\t  \t Throwable\t\t\t\t\t\t\t\n\n\n"],
            [['RUNTIMEEEEeXCEPTION'], [\Throwable::class], "*\t@throws\t  \t RUNTIMEEEEeXCEPTION\t\t\t\t\t\t\t\n\n\n", "*\t@throws\t  \t Throwable\t\t\t\t\t\t\t\n\n\n"],
            [['RUNTIMEEEEeXCEPTION'], [\Throwable::class], "*@throws\t  \t RUNTIMEEEEeXCEPTION\t\t\t\t\t\t\t\n\n\n", "*@throws\t  \t Throwable\t\t\t\t\t\t\t\n\n\n"],
            [['string'], ['string', 'null'], ' * @method string getString()', ' * @method string|null getString()'],
        ];
    }

    /**
     * @param string[] $expected
     *
     * @dataProvider provideNormalizedTypesCases
     */
    public function testNormalizedTypes(array $expected, string $input): void
    {
        $line = new Line($input);
        $tag = new Annotation([$line]);

        static::assertSame($expected, $tag->getNormalizedTypes());
    }

    public function provideNormalizedTypesCases()
    {
        return [
            [['null', 'string'], '* @param StRiNg|NuLl $foo'],
            [['void'], '* @return Void'],
            [['bar', 'baz', 'foo', 'null', 'qux'], '* @return Foo|Bar|Baz|Qux|null'],
        ];
    }

    public function testGetTypesOnBadTag(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('This tag does not support types');

        $tag = new Annotation([new Line(' * @deprecated since 1.2')]);

        $tag->getTypes();
    }

    public function testSetTypesOnBadTag(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('This tag does not support types');

        $tag = new Annotation([new Line(' * @author Chuck Norris')]);

        $tag->setTypes(['string']);
    }

    public function testGetTagsWithTypes(): void
    {
        $tags = Annotation::getTagsWithTypes();
        static::assertIsArray($tags);
        foreach ($tags as $tag) {
            static::assertIsString($tag);
            static::assertNotEmpty($tag);
        }
    }

    /**
     * @param Line[]                 $lines
     * @param null|NamespaceAnalysis $namespace
     * @param NamespaceUseAnalysis[] $namespaceUses
     * @param null|string            $expectedCommonType
     *
     * @dataProvider provideTypeExpressionCases
     */
    public function testGetTypeExpression(array $lines, $namespace, array $namespaceUses, $expectedCommonType): void
    {
        $annotation = new Annotation($lines, $namespace, $namespaceUses);
        $result = $annotation->getTypeExpression();

        static::assertInstanceOf(TypeExpression::class, $result);
        static::assertSame($expectedCommonType, $result->getCommonType());
    }

    public function provideTypeExpressionCases()
    {
        $appNamespace = new NamespaceAnalysis('App', 'App', 0, 999, 0, 999);
        $useTraversable = new NamespaceUseAnalysis('Traversable', 'Traversable', false, 0, 999, NamespaceUseAnalysis::TYPE_CLASS);

        yield [[new Line('* @param array|Traversable $foo')], null, [], 'iterable'];
        yield [[new Line('* @param array|Traversable $foo')], $appNamespace, [], null];
        yield [[new Line('* @param array|Traversable $foo')], $appNamespace, [$useTraversable], 'iterable'];
    }

    /**
     * @param Line[]      $lines
     * @param null|string $expectedVariableName
     *
     * @dataProvider provideGetVariableCases
     */
    public function testGetVariableName(array $lines, $expectedVariableName): void
    {
        $annotation = new Annotation($lines);
        static::assertSame($expectedVariableName, $annotation->getVariableName());
    }

    public function provideGetVariableCases()
    {
        yield [[new Line('* @param int $foo')], '$foo'];
        yield [[new Line('* @param int $foo some description')], '$foo'];
        yield [[new Line('/** @param int $foo*/')], '$foo'];
        yield [[new Line('* @param int')], null];
        yield [[new Line('* @var int $foo')], '$foo'];
        yield [[new Line('* @var int $foo some description')], '$foo'];
        yield [[new Line('/** @var int $foo*/')], '$foo'];
        yield [[new Line('* @var int')], null];
    }
}
