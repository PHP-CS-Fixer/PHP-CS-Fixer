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
     */
    private const SAMPLE = '/**
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
     * @var list<string>
     */
    private const CONTENT = [
        "     * @param string \$hello\n",
        "     * @param bool \$test Description\n     *        extends over many lines\n",
        "     * @param adkjbadjasbdand \$asdnjkasd\n",
        "     * @throws \\Exception asdnjkasd\n     *\n     * asdasdasdasdasdasdasdasd\n     * kasdkasdkbasdasdasdjhbasdhbasjdbjasbdjhb\n",
        "     * @return void\n",
    ];

    /**
     * This represents the start indexes of each annotation.
     *
     * @var list<int>
     */
    private const START = [3, 4, 7, 9, 14];

    /**
     * This represents the start indexes of each annotation.
     *
     * @var list<int>
     */
    private const END = [3, 5, 7, 12, 14];

    /**
     * This represents the tag type of each annotation.
     *
     * @var list<string>
     */
    private static array $tags = ['param', 'param', 'param', 'throws', 'return'];

    /**
     * @dataProvider provideGetContentCases
     */
    public function testGetContent(int $index, string $content): void
    {
        $doc = new DocBlock(self::SAMPLE);
        $annotation = $doc->getAnnotation($index);

        self::assertSame($content, $annotation->getContent());
        self::assertSame($content, (string) $annotation);
    }

    /**
     * @return iterable<int, _PhpTokenArray>
     */
    public static function provideGetContentCases(): iterable
    {
        foreach (self::CONTENT as $index => $content) {
            yield [$index, $content];
        }
    }

    /**
     * @dataProvider provideStartCases
     */
    public function testStart(int $index, int $start): void
    {
        $doc = new DocBlock(self::SAMPLE);
        $annotation = $doc->getAnnotation($index);

        self::assertSame($start, $annotation->getStart());
    }

    /**
     * @return iterable<int, array{int, int}>
     */
    public static function provideStartCases(): iterable
    {
        foreach (self::START as $index => $start) {
            yield [$index, $start];
        }
    }

    /**
     * @dataProvider provideEndCases
     */
    public function testEnd(int $index, int $end): void
    {
        $doc = new DocBlock(self::SAMPLE);
        $annotation = $doc->getAnnotation($index);

        self::assertSame($end, $annotation->getEnd());
    }

    /**
     * @return iterable<int, array{int, int}>
     */
    public static function provideEndCases(): iterable
    {
        foreach (self::END as $index => $end) {
            yield [$index, $end];
        }
    }

    /**
     * @dataProvider provideGetTagCases
     */
    public function testGetTag(int $index, string $tag): void
    {
        $doc = new DocBlock(self::SAMPLE);
        $annotation = $doc->getAnnotation($index);

        self::assertSame($tag, $annotation->getTag()->getName());
    }

    /**
     * @return iterable<int, _PhpTokenArray>
     */
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
        $doc = new DocBlock(self::SAMPLE);
        $annotation = $doc->getAnnotation($index);

        $annotation->remove();
        self::assertSame('', $annotation->getContent());
        self::assertSame('', $doc->getLine($start)->getContent());
        self::assertSame('', $doc->getLine($end)->getContent());
    }

    /**
     * @return iterable<int, array{int, int, int}>
     */
    public static function provideRemoveCases(): iterable
    {
        foreach (self::START as $index => $start) {
            yield [$index, $start, self::END[$index]];
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
        self::assertSame($expected, $doc->getContent());
    }

    /**
     * @return iterable<int, array{string, string}>
     */
    public static function provideRemoveEdgeCasesCases(): iterable
    {
        // Single line
        yield ['', '/** @return null*/'];

        yield ['', '/** @return null */'];

        yield ['', '/** @return null  */'];

        // Multi line, annotation on start line
        yield [
            '/**
                 */',
            '/** @return null
                 */',
        ];

        yield [
            '/**
                 */',
            '/** @return null '.'
                 */',
        ];

        // Multi line, annotation on end line
        yield [
            '/**
                 */',
            '/**
                 * @return null*/',
        ];

        yield [
            '/**
                 */',
            '/**
                 * @return null */',
        ];
    }

    /**
     * @param list<string> $expected
     *
     * @dataProvider provideTypeParsingCases
     */
    public function testTypeParsing(array $expected, string $input): void
    {
        $tag = new Annotation([new Line($input)]);

        self::assertSame($expected, $tag->getTypes());
    }

    /**
     * @return iterable<int, array{list<string>, string}>
     */
    public static function provideTypeParsingCases(): iterable
    {
        yield [
            ['int'],
            ' * @method int method()',
        ];

        yield [
            ['int[]'],
            " * @return int[]\r",
        ];

        yield [
            ['int[]'],
            " * @return int[]\r\n",
        ];

        yield [
            ['Foo[][]'],
            ' * @method Foo[][] method()',
        ];

        yield [
            ['int[]'],
            ' * @method int[] method()',
        ];

        yield [
            ['int[]', 'null'],
            ' * @method int[]|null method()',
        ];

        yield [
            ['int[]', 'null', '?int', 'array'],
            ' * @method int[]|null|?int|array method()',
        ];

        yield [
            ['null', 'Foo\Bar', '\Baz\Bax', 'int[]'],
            ' * @method null|Foo\Bar|\Baz\Bax|int[] method()',
        ];

        yield [
            ['gen<int>'],
            ' * @method gen<int> method()',
        ];

        yield [
            ['int', 'gen<int>'],
            ' * @method int|gen<int> method()',
        ];

        yield [
            ['\int', '\gen<\int, \bool>'],
            ' * @method \int|\gen<\int, \bool> method()',
        ];

        yield [
            ['gen<int,  int>'],
            ' * @method gen<int,  int> method()',
        ];

        yield [
            ['gen<int,  bool|string>'],
            ' * @method gen<int,  bool|string> method()',
        ];

        yield [
            ['gen<int,  string[]>'],
            ' * @method gen<int,  string[]> method() <> a',
        ];

        yield [
            ['gen<int,  gener<string, bool>>'],
            ' * @method gen<int,  gener<string, bool>> method() foo <a >',
        ];

        yield [
            ['gen<int,  gener<string, null|bool>>'],
            ' * @method gen<int,  gener<string, null|bool>> method()',
        ];

        yield [
            ['null', 'gen<int,  gener<string, bool>>', 'int', 'string[]'],
            ' * @method null|gen<int,  gener<string, bool>>|int|string[] method() foo <a >',
        ];

        yield [
            ['null', 'gen<int,  gener<string, bool>>', 'int', 'array<int, string>', 'string[]'],
            ' * @method null|gen<int,  gener<string, bool>>|int|array<int, string>|string[] method() foo <a >',
        ];

        yield [
            ['this'],
            '/** @return    this */',
        ];

        yield [
            ['@this'],
            '/** @return    @this */',
        ];

        yield [
            ['$SELF', 'int'],
            '/** @return $SELF|int */',
        ];

        yield [
            ['array<string|int, string>'],
            '/** @var array<string|int, string>',
        ];

        yield [
            ['int'],
            " * @return int\n",
        ];

        yield [
            ['int'],
            " * @return int\r\n",
        ];

        yield [
            ['Collection<Foo<Bar>, Foo<Baz>>'],
            '/** @var Collection<Foo<Bar>, Foo<Baz>>',
        ];

        yield [
            ['int', 'string'],
            '/** @var int | string',
        ];

        yield [
            ['Foo::*'],
            '/** @var Foo::*',
        ];

        yield [
            ['Foo::A'],
            '/** @var Foo::A',
        ];

        yield [
            ['Foo::A', 'Foo::B'],
            '/** @var Foo::A|Foo::B',
        ];

        yield [
            ['Foo::A*'],
            '/** @var Foo::A*',
        ];

        yield [
            ['array<Foo::A*>', 'null'],
            '/** @var array<Foo::A*>|null',
        ];

        yield [
            ['null', 'true', 'false', '1', '-1', '1.5', '-1.5', '.5', '1.', "'a'", '"b"'],
            '/** @var null|true|false|1|-1|1.5|-1.5|.5|1.|\'a\'|"b"',
        ];

        yield [
            ['int', '"a"', 'A<B<C, D>, E<F::*|G[]>>'],
            '/** @param int | "a" | A<B<C, D>, E<F::*|G[]>> $foo */',
        ];

        yield [
            ['class-string<Foo>'],
            '/** @var class-string<Foo> */',
        ];

        yield [
            ['A', 'B'],
            '/** @var A&B */',
        ];

        yield [
            ['A', 'B'],
            '/** @var A & B */',
        ];

        yield [
            ['array{1: bool, 2: bool}'],
            '/** @var array{1: bool, 2: bool} */',
        ];

        yield [
            ['array{a: int|string, b?: bool}'],
            '/** @var array{a: int|string, b?: bool} */',
        ];

        yield [
            ['array{\'a\': "a", "b"?: \'b\'}'],
            '/** @var array{\'a\': "a", "b"?: \'b\'} */',
        ];

        yield [
            ['array { a : int | string , b ? : A<B, C> }'],
            '/** @var array { a : int | string , b ? : A<B, C> } */',
        ];

        yield [
            ["array{\n    a: int,\n    b: string\n}"],
            "/** @var array{\n    a: int,\n    b: string\n} */",
        ];

        yield [
            ['callable(string)'],
            '/** @param callable(string) $function',
        ];

        yield [
            ['callable(string): bool'],
            '/** @param callable(string): bool $function',
        ];

        yield [
            ['callable(array<int, string>, array<int, Foo>): bool'],
            '/** @param callable(array<int, string>, array<int, Foo>): bool $function',
        ];

        yield [
            ['array<int, callable(string): bool>'],
            '/** @param array<int, callable(string): bool> $function',
        ];

        yield [
            ['callable(string): callable(int)'],
            '/** @param callable(string): callable(int) $function',
        ];

        yield [
            ['callable(string) : callable(int) : bool'],
            '/** @param callable(string) : callable(int) : bool $function',
        ];

        yield [
            ['TheCollection<callable(Foo, Bar,Baz): Foo[]>', 'string[]', 'null'],
            '* @param TheCollection<callable(Foo, Bar,Baz): Foo[]>|string[]|null $x',
        ];

        yield [
            ['Closure(string)'],
            '/** @param Closure(string) $function',
        ];

        yield [
            ['closure()'],
            '/** @param closure() $function',
        ];

        yield [
            ['array  <  int   , callable  (  string  )  :   bool  >'],
            '/** @param   array  <  int   , callable  (  string  )  :   bool  > $function',
        ];
    }

    /**
     * @param list<string> $expected
     * @param list<string> $new
     *
     * @dataProvider provideTypesCases
     */
    public function testTypes(array $expected, array $new, string $input, string $output): void
    {
        $line = new Line($input);
        $tag = new Annotation([$line]);

        self::assertSame($expected, $tag->getTypes());

        $tag->setTypes($new);

        self::assertSame($new, $tag->getTypes());

        self::assertSame($output, $line->getContent());
    }

    /**
     * @return iterable<int, array{list<string>, list<string>, string, string}>
     */
    public static function provideTypesCases(): iterable
    {
        yield [['Foo', 'null'], ['Bar[]'], '     * @param Foo|null $foo', '     * @param Bar[] $foo'];

        yield [['false'], ['bool'], '*   @return            false', '*   @return            bool'];

        yield [['RUNTIMEEEEeXCEPTION'], [\Throwable::class], "* \t@throws\t  \t RUNTIMEEEEeXCEPTION\t\t\t\t\t\t\t\n\n\n", "* \t@throws\t  \t Throwable\t\t\t\t\t\t\t\n\n\n"];

        yield [['RUNTIMEEEEeXCEPTION'], [\Throwable::class], "*\t@throws\t  \t RUNTIMEEEEeXCEPTION\t\t\t\t\t\t\t\n\n\n", "*\t@throws\t  \t Throwable\t\t\t\t\t\t\t\n\n\n"];

        yield [['RUNTIMEEEEeXCEPTION'], [\Throwable::class], "*@throws\t  \t RUNTIMEEEEeXCEPTION\t\t\t\t\t\t\t\n\n\n", "*@throws\t  \t Throwable\t\t\t\t\t\t\t\n\n\n"];

        yield [['string'], ['string', 'null'], ' * @method string getString()', ' * @method string|null getString()'];

        yield [['Foo', 'Bar'], ['Bar', 'Foo'], ' * @param Foo&Bar $x', ' * @param Bar&Foo $x'];
    }

    /**
     * @param list<string> $expected
     *
     * @dataProvider provideNormalizedTypesCases
     */
    public function testNormalizedTypes(array $expected, string $input): void
    {
        $line = new Line($input);
        $tag = new Annotation([$line]);

        self::assertSame($expected, $tag->getNormalizedTypes());
    }

    /**
     * @return iterable<int, array{list<string>, string}>
     */
    public static function provideNormalizedTypesCases(): iterable
    {
        yield [['null', 'string'], '* @param StRiNg|NuLl $foo'];

        yield [['void'], '* @return Void'];

        yield [['bar', 'baz', 'foo', 'null', 'qux'], '* @return Foo|Bar|Baz|Qux|null'];

        yield [['bool', 'int'], '* @param bool|int $foo'];

        yield [['bool', 'int'], '* @param bool&int $foo'];

        yield [['bool', 'int'], '* @param bool|int ...$foo'];

        yield [['bool', 'int'], '* @param bool|int &$foo'];

        yield [['bool', 'int'], '* @param bool|int &...$foo'];

        yield [['bool', 'int'], '* @param bool|int$foo'];

        yield [['bool', 'int'], '* @param bool|int&$foo'];

        yield [['bool', 'int'], '* @param bool|int&...$foo'];

        yield [['bar&baz', 'foo'], '* @param Foo|Bar&Baz&$param'];

        yield [['bar&baz', 'foo'], '* @param Baz&Bar|Foo&$param'];
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
            self::assertIsString($tag);
            self::assertNotEmpty($tag);
        }
    }

    /**
     * @param list<NamespaceUseAnalysis> $namespaceUses
     *
     * @dataProvider provideGetTypeExpressionCases
     */
    public function testGetTypeExpression(string $line, ?NamespaceAnalysis $namespace, array $namespaceUses, ?string $expectedCommonType): void
    {
        $annotation = new Annotation([new Line($line)], $namespace, $namespaceUses);
        $result = $annotation->getTypeExpression();

        self::assertSame($expectedCommonType, $result->getCommonType());
    }

    /**
     * @return iterable<int, array{string, null|NamespaceAnalysis, list<NamespaceUseAnalysis>, null|string}>
     */
    public static function provideGetTypeExpressionCases(): iterable
    {
        $appNamespace = new NamespaceAnalysis('App', 'App', 0, 999, 0, 999);
        $useTraversable = new NamespaceUseAnalysis(NamespaceUseAnalysis::TYPE_CLASS, \Traversable::class, \Traversable::class, false, false, 0, 999);

        yield ['* @param array|Traversable $foo', null, [], 'iterable'];

        yield ['* @param array|Traversable $foo', $appNamespace, [], null];

        yield ['* @param array|Traversable $foo', $appNamespace, [$useTraversable], 'iterable'];
    }

    /**
     * @dataProvider provideGetVariableNameCases
     */
    public function testGetVariableName(string $line, ?string $expectedVariableName): void
    {
        $annotation = new Annotation([new Line($line)]);
        self::assertSame($expectedVariableName, $annotation->getVariableName());
    }

    /**
     * @return iterable<int, array{string, null|string}>
     */
    public static function provideGetVariableNameCases(): iterable
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

        yield ['* @param int&$foo', '$foo'];

        yield ['* @param int& $foo', '$foo'];

        yield ['* @param int &$foo', '$foo'];

        yield ['* @param int & $foo', '$foo'];

        yield ['* @param int ...$foo', '$foo'];

        yield ['* @param int ... $foo', '$foo'];

        yield ['* @param int&...$foo', '$foo'];

        yield ['* @param int &...$foo', '$foo'];

        yield ['* @param int & ...$foo', '$foo'];

        yield ['* @param int & ... $foo', '$foo'];

        yield ['* @param ?int $foo=null invalid description', '$foo'];

        yield ['* @param int $počet Special chars in variable name', '$počet'];

        yield [" * @param array{\n * a: Foo,\n * b: Bar\n * } \$x", '$x'];
    }

    public function testGetVariableNameForMultiline(): void
    {
        $docBlock = new DocBlock(
            <<<'PHP'
                <?php
                /**
                 * @param array{
                 *        a: Foo,
                 *        b: Bar
                 * } $x
                 */
                PHP
        );
        $annotation = $docBlock->getAnnotation(0);

        self::assertSame('$x', $annotation->getVariableName());
    }
}
