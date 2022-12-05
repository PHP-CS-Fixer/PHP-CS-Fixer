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
use PhpCsFixer\Tests\TestCase;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\NamespaceAnalysis;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\NamespaceUseAnalysis;

/**
 * @author Graham Campbell <hello@gjcampbell.co.uk>
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

    public static function provideGetContentCases(): iterable
    {
        foreach (self::$content as $index => $content) {
            yield [$index, $content];
        }
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

    public static function provideStartCases(): iterable
    {
        foreach (self::$start as $index => $start) {
            yield [$index, $start];
        }
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

    public static function provideEndCases(): iterable
    {
        foreach (self::$end as $index => $end) {
            yield [$index, $end];
        }
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

    public static function provideGetTagCases(): iterable
    {
        foreach (self::$tags as $index => $tag) {
            yield [$index, $tag];
        }
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

    public static function provideRemoveCases(): iterable
    {
        foreach (self::$start as $index => $start) {
            yield [$index, $start, self::$end[$index]];
        }
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

    public static function provideRemoveEdgeCasesCases(): array
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
    public function testTypeParsing(array $expected, string $input): void
    {
        $tag = new Annotation([new Line($input)]);

        static::assertSame($expected, $tag->getTypes());
    }

    public static function provideTypeParsingCases(): array
    {
        return [
            [
                ['int'],
                ' * @method int method()',
            ],
            [
                ['int[]'],
                " * @return int[]\r",
            ],
            [
                ['int[]'],
                " * @return int[]\r\n",
            ],
            [
                ['Foo[][]'],
                ' * @method Foo[][] method()',
            ],
            [
                ['int[]'],
                ' * @method int[] method()',
            ],
            [
                ['int[]', 'null'],
                ' * @method int[]|null method()',
            ],
            [
                ['int[]', 'null', '?int', 'array'],
                ' * @method int[]|null|?int|array method()',
            ],
            [
                ['null', 'Foo\Bar', '\Baz\Bax', 'int[]'],
                ' * @method null|Foo\Bar|\Baz\Bax|int[] method()',
            ],
            [
                ['gen<int>'],
                ' * @method gen<int> method()',
            ],
            [
                ['int', 'gen<int>'],
                ' * @method int|gen<int> method()',
            ],
            [
                ['\int', '\gen<\int, \bool>'],
                ' * @method \int|\gen<\int, \bool> method()',
            ],
            [
                ['gen<int,  int>'],
                ' * @method gen<int,  int> method()',
            ],
            [
                ['gen<int,  bool|string>'],
                ' * @method gen<int,  bool|string> method()',
            ],
            [
                ['gen<int,  string[]>'],
                ' * @method gen<int,  string[]> method() <> a',
            ],
            [
                ['gen<int,  gener<string, bool>>'],
                ' * @method gen<int,  gener<string, bool>> method() foo <a >',
            ],
            [
                ['gen<int,  gener<string, null|bool>>'],
                ' * @method gen<int,  gener<string, null|bool>> method()',
            ],
            [
                ['null', 'gen<int,  gener<string, bool>>', 'int', 'string[]'],
                ' * @method null|gen<int,  gener<string, bool>>|int|string[] method() foo <a >',
            ],
            [
                ['null', 'gen<int,  gener<string, bool>>', 'int', 'array<int, string>', 'string[]'],
                ' * @method null|gen<int,  gener<string, bool>>|int|array<int, string>|string[] method() foo <a >',
            ],
            [
                ['this'],
                '/** @return    this */',
            ],
            [
                ['@this'],
                '/** @return    @this */',
            ],
            [
                ['$SELF', 'int'],
                '/** @return $SELF|int */',
            ],
            [
                ['array<string|int, string>'],
                '/** @var array<string|int, string>',
            ],
            [
                ['int'],
                " * @return int\n",
            ],
            [
                ['int'],
                " * @return int\r\n",
            ],
            [
                ['Collection<Foo<Bar>, Foo<Baz>>'],
                '/** @var Collection<Foo<Bar>, Foo<Baz>>',
            ],
            [
                ['int', 'string'],
                '/** @var int | string',
            ],
            [
                ['Foo::*'],
                '/** @var Foo::*',
            ],
            [
                ['Foo::A'],
                '/** @var Foo::A',
            ],
            [
                ['Foo::A', 'Foo::B'],
                '/** @var Foo::A|Foo::B',
            ],
            [
                ['Foo::A*'],
                '/** @var Foo::A*',
            ],
            [
                ['array<Foo::A*>', 'null'],
                '/** @var array<Foo::A*>|null',
            ],
            [
                ['null', 'true', 'false', '1', '-1', '1.5', '-1.5', '.5', '1.', "'a'", '"b"'],
                '/** @var null|true|false|1|-1|1.5|-1.5|.5|1.|\'a\'|"b"',
            ],
            [
                ['int', '"a"', 'A<B<C, D>, E<F::*|G[]>>'],
                '/** @param int | "a" | A<B<C, D>, E<F::*|G[]>> $foo */',
            ],
            [
                ['class-string<Foo>'],
                '/** @var class-string<Foo> */',
            ],
            [
                ['A', 'B'],
                '/** @var A&B */',
            ],
            [
                ['A', 'B'],
                '/** @var A & B */',
            ],
            [
                ['array{1: bool, 2: bool}'],
                '/** @var array{1: bool, 2: bool} */',
            ],
            [
                ['array{a: int|string, b?: bool}'],
                '/** @var array{a: int|string, b?: bool} */',
            ],
            [
                ['array{\'a\': "a", "b"?: \'b\'}'],
                '/** @var array{\'a\': "a", "b"?: \'b\'} */',
            ],
            [
                ['array { a : int | string , b ? : A<B, C> }'],
                '/** @var array { a : int | string , b ? : A<B, C> } */',
            ],
            [
                ['callable(string)'],
                '/** @param callable(string) $function',
            ],
            [
                ['callable(string): bool'],
                '/** @param callable(string): bool $function',
            ],
            [
                ['callable(array<int, string>, array<int, Foo>): bool'],
                '/** @param callable(array<int, string>, array<int, Foo>): bool $function',
            ],
            [
                ['array<int, callable(string): bool>'],
                '/** @param array<int, callable(string): bool> $function',
            ],
            [
                ['callable(string): callable(int)'],
                '/** @param callable(string): callable(int) $function',
            ],
            [
                ['callable(string) : callable(int) : bool'],
                '/** @param callable(string) : callable(int) : bool $function',
            ],
            [
                ['TheCollection<callable(Foo, Bar,Baz): Foo[]>', 'string[]', 'null'],
                '* @param TheCollection<callable(Foo, Bar,Baz): Foo[]>|string[]|null $x',
            ],
            [
                ['Closure(string)'],
                '/** @param Closure(string) $function',
            ],
            [
                ['closure()'],
                '/** @param closure() $function',
            ],
            [
                ['array  <  int   , callable  (  string  )  :   bool  >'],
                '/** @param   array  <  int   , callable  (  string  )  :   bool  > $function',
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

    public static function provideTypesCases(): array
    {
        return [
            [['Foo', 'null'], ['Bar[]'], '     * @param Foo|null $foo', '     * @param Bar[] $foo'],
            [['false'], ['bool'], '*   @return            false', '*   @return            bool'],
            [['RUNTIMEEEEeXCEPTION'], [\Throwable::class], "* \t@throws\t  \t RUNTIMEEEEeXCEPTION\t\t\t\t\t\t\t\n\n\n", "* \t@throws\t  \t Throwable\t\t\t\t\t\t\t\n\n\n"],
            [['RUNTIMEEEEeXCEPTION'], [\Throwable::class], "*\t@throws\t  \t RUNTIMEEEEeXCEPTION\t\t\t\t\t\t\t\n\n\n", "*\t@throws\t  \t Throwable\t\t\t\t\t\t\t\n\n\n"],
            [['RUNTIMEEEEeXCEPTION'], [\Throwable::class], "*@throws\t  \t RUNTIMEEEEeXCEPTION\t\t\t\t\t\t\t\n\n\n", "*@throws\t  \t Throwable\t\t\t\t\t\t\t\n\n\n"],
            [['string'], ['string', 'null'], ' * @method string getString()', ' * @method string|null getString()'],
            [['Foo', 'Bar'], ['Bar', 'Foo'], ' * @param Foo&Bar $x', ' * @param Bar&Foo $x'],
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

    public static function provideNormalizedTypesCases(): array
    {
        return [
            [['null', 'string'], '* @param StRiNg|NuLl $foo'],
            [['void'], '* @return Void'],
            [['bar', 'baz', 'foo', 'null', 'qux'], '* @return Foo|Bar|Baz|Qux|null'],
            [['bool', 'int'], '* @param bool|int $foo'],
            [['bool', 'int'], '* @param bool|int ...$foo'],
            [['bool', 'int'], '* @param bool|int &$foo'],
            [['bool', 'int'], '* @param bool|int &...$foo'],
            [['bool', 'int'], '* @param bool|int&$foo'],
            [['bool', 'int'], '* @param bool|int&...$foo'],
            [['bar', 'baz', 'foo'], '* @param Foo|Bar&Baz&$param'],
        ];
    }

    public function testGetTypesOnBadTag(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('This tag does not support types');

        $tag = new Annotation([new Line(' * @deprecated since Symfony 1.2')]);

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

        foreach ($tags as $tag) {
            static::assertIsString($tag);
            static::assertNotEmpty($tag);
        }
    }

    /**
     * @param NamespaceUseAnalysis[] $namespaceUses
     *
     * @dataProvider provideTypeExpressionCases
     */
    public function testGetTypeExpression(string $line, ?NamespaceAnalysis $namespace, array $namespaceUses, ?string $expectedCommonType): void
    {
        $annotation = new Annotation([new Line($line)], $namespace, $namespaceUses);
        $result = $annotation->getTypeExpression();

        static::assertSame($expectedCommonType, $result->getCommonType());
    }

    public static function provideTypeExpressionCases(): iterable
    {
        $appNamespace = new NamespaceAnalysis('App', 'App', 0, 999, 0, 999);
        $useTraversable = new NamespaceUseAnalysis('Traversable', 'Traversable', false, 0, 999, NamespaceUseAnalysis::TYPE_CLASS);

        yield ['* @param array|Traversable $foo', null, [], 'iterable'];

        yield ['* @param array|Traversable $foo', $appNamespace, [], null];

        yield ['* @param array|Traversable $foo', $appNamespace, [$useTraversable], 'iterable'];
    }

    /**
     * @dataProvider provideGetVariableCases
     */
    public function testGetVariableName(string $line, ?string $expectedVariableName): void
    {
        $annotation = new Annotation([new Line($line)]);
        static::assertSame($expectedVariableName, $annotation->getVariableName());
    }

    public static function provideGetVariableCases(): iterable
    {
        yield ['* @param int $foo', '$foo'];

        yield ['* @param int $foo some description', '$foo'];

        yield ['/** @param int $foo*/', '$foo'];

        yield ['* @param int', null];

        yield ['* @var int $foo', '$foo'];

        yield ['* @var int $foo some description', '$foo'];

        yield ['/** @var int $foo*/', '$foo'];

        yield ['* @var int', null];

        yield ['* @param $foo', '$foo'];

        yield ['* @param &$foo', '$foo'];

        yield ['* @param & $foo', '$foo'];

        yield ['* @param int &$foo', '$foo'];

        yield ['* @param int & $foo', '$foo'];

        yield ['* @param int ...$foo', '$foo'];

        yield ['* @param int ... $foo', '$foo'];

        yield ['* @param int&...$foo', '$foo'];

        yield ['* @param int &...$foo', '$foo'];

        yield ['* @param int & ...$foo', '$foo'];

        yield ['* @param int & ... $foo', '$foo'];
    }
}
