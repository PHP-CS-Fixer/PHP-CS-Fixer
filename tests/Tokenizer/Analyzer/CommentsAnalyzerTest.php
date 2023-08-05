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

namespace PhpCsFixer\Tests\Tokenizer\Analyzer;

use PhpCsFixer\Tests\TestCase;
use PhpCsFixer\Tokenizer\Analyzer\CommentsAnalyzer;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Kuba Werłos <werlos@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Tokenizer\Analyzer\CommentsAnalyzer
 */
final class CommentsAnalyzerTest extends TestCase
{
    public function testWhenNotPointingToComment(): void
    {
        $analyzer = new CommentsAnalyzer();
        $tokens = Tokens::fromCode('<?php $no; $comment; $here;');

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Given index must point to a comment.');

        $analyzer->getCommentBlockIndices($tokens, 4);
    }

    /**
     * @param list<int> $borders
     *
     * @dataProvider provideCommentsCases
     */
    public function testComments(string $code, int $index, array $borders): void
    {
        $tokens = Tokens::fromCode($code);
        $analyzer = new CommentsAnalyzer();

        self::assertSame($borders, $analyzer->getCommentBlockIndices($tokens, $index));
        self::assertFalse($analyzer->isHeaderComment($tokens, $index));
    }

    public static function provideCommentsCases(): iterable
    {
        yield 'discover all 4 comments for the 1st comment with slash' => [
            '<?php
$foo;
// one
// two
// three
// four
$bar;',
            4,
            [4, 6, 8, 10],
        ];

        yield 'discover all 4 comments for the 1st comment with hash' => [
            '<?php
$foo;
# one
# two
# three
# four
$bar;',
            4,
            [4, 6, 8, 10],
        ];

        yield 'discover 3 comments out of 4 for the 2nd comment' => [
            '<?php
$foo;
// one
// two
// three
// four
$bar;',
            6,
            [6, 8, 10],
        ];

        yield 'discover 3 comments when empty line separates 4th' => [
            '<?php
$foo;
// one
// two
// three

// four
$bar;',
            4,
            [4, 6, 8],
        ];

        yield 'discover 3 comments when empty line of CR separates 4th' => [
            str_replace("\n", "\r", '<?php
$foo;
// one
// two
// three

// four
$bar;'),
            4,
            [4, 6, 8],
        ];

        yield 'discover correctly when mix of slash and hash' => [
            '<?php
$foo;
// one
// two
# three
// four
$bar;',
            4,
            [4, 6],
        ];

        yield 'do not group asterisk comments' => [
            '<?php
$foo;
/* one */
/* two */
/* three */
$bar;',
            4,
            [4],
        ];

        yield 'handle fancy indent' => [
            '<?php
$foo;
        // one
       //  two
      //   three
     //    four
$bar;',
            4,
            [4, 6, 8, 10],
        ];
    }

    public function testHeaderCommentAcceptsOnlyComments(): void
    {
        $tokens = Tokens::fromCode('<?php 1; 2; 3;');
        $analyzer = new CommentsAnalyzer();

        $this->expectException(\InvalidArgumentException::class);

        $analyzer->isHeaderComment($tokens, 2);
    }

    /**
     * @dataProvider provideHeaderCommentCases
     */
    public function testHeaderComment(string $code, int $index): void
    {
        $tokens = Tokens::fromCode($code);
        $analyzer = new CommentsAnalyzer();

        self::assertTrue($analyzer->isHeaderComment($tokens, $index));
    }

    public static function provideHeaderCommentCases(): iterable
    {
        yield ['<?php /* Comment */ namespace Foo;', 1];

        yield ['<?php /** Comment */ namespace Foo;', 1];

        yield ['<?php declare(strict_types=1); /* Comment */ namespace Foo;', 9];

        yield ['<?php /* We test this one */ /* Foo */ namespace Bar;', 1];

        yield ['<?php /** Comment */ namespace Foo; declare(strict_types=1); /* Comment */ namespace Foo;', 1];
    }

    /**
     * @dataProvider provideNotHeaderCommentCases
     */
    public function testNotHeaderComment(string $code, int $index): void
    {
        $tokens = Tokens::fromCode($code);
        $analyzer = new CommentsAnalyzer();

        self::assertFalse($analyzer->isHeaderComment($tokens, $index));
    }

    public static function provideNotHeaderCommentCases(): iterable
    {
        yield ['<?php $foo; /* Comment */ $bar;', 4];

        yield ['<?php foo(); /* Comment */ $bar;', 6];

        yield ['<?php namespace Foo; /* Comment */ class Bar {};', 6];

        yield ['<?php /* It is not header when no content after */', 1];

        yield ['<?php /* Foo */ /* We test this one */ namespace Bar;', 3];

        yield ['<?php /* Foo */ declare(strict_types=1); /* We test this one */ namespace Bar;', 11];
    }

    public function testPhpdocCandidateAcceptsOnlyComments(): void
    {
        $tokens = Tokens::fromCode('<?php 1; 2; 3;');
        $analyzer = new CommentsAnalyzer();

        $this->expectException(\InvalidArgumentException::class);

        $analyzer->isBeforeStructuralElement($tokens, 2);
    }

    /**
     * @dataProvider providePhpdocCandidateCases
     */
    public function testPhpdocCandidate(string $code): void
    {
        $tokens = Tokens::fromCode($code);
        $index = $tokens->getNextTokenOfKind(0, [[T_COMMENT], [T_DOC_COMMENT]]);
        $analyzer = new CommentsAnalyzer();

        self::assertTrue($analyzer->isBeforeStructuralElement($tokens, $index));
    }

    public static function providePhpdocCandidateCases(): iterable
    {
        yield ['<?php /* @var Foo */ $bar = "baz";'];

        yield ['<?php /* Before namespace */ namespace Foo;'];

        yield ['<?php /* Before class */ class Foo {}'];

        yield ['<?php /* Before class */ abstract class Foo {}'];

        yield ['<?php /* Before class */ final class Foo {}'];

        yield ['<?php /* Before trait */ trait Foo {}'];

        yield ['<?php /* Before interface */ interface Foo {}'];

        yield ['<?php /* Before anonymous function */ function () {};'];

        yield ['<?php class Foo { /* Before property */ private $bar; }'];

        yield ['<?php class Foo { /* Before property */ protected $bar; }'];

        yield ['<?php class Foo { /* Before property */ public $bar; }'];

        yield ['<?php class Foo { /* Before property */ var $bar; }'];

        yield ['<?php class Foo { /* Before function */ function bar() {} }'];

        yield ['<?php class Foo { /* Before use */ use Bar; }'];

        yield ['<?php class Foo { /* Before function */ final function bar() {} }'];

        yield ['<?php class Foo { /* Before function */ private function bar() {} }'];

        yield ['<?php class Foo { /* Before function */ protected function bar() {} }'];

        yield ['<?php class Foo { /* Before function */ public function bar() {} }'];

        yield ['<?php class Foo { /* Before function */ static function bar() {} }'];

        yield ['<?php class Foo { /* Before function */ abstract function bar(); }'];

        yield ['<?php class Foo { /* Before constant */ const FOO = 42; }'];

        yield ['<?php /* Before require */ require "foo/php";'];

        yield ['<?php /* Before require_once */ require_once "foo/php";'];

        yield ['<?php /* Before include */ include "foo/php";'];

        yield ['<?php /* Before include_once */ include_once "foo/php";'];

        yield ['<?php /* @var array $foo */ foreach ($foo as $bar) {};'];

        yield ['<?php /* @var int $foo */ if ($foo === -1) {};'];

        yield ['<?php /* @var SomeClass $foo */ switch ($foo) { default: exit; };'];

        yield ['<?php /* @var bool $foo */ while ($foo) { $foo--; };'];

        yield ['<?php /* @var int $i */ for ($i = 0; $i < 16; $i++) {};'];

        yield ['<?php /* @var int $i @var int $j */ list($i, $j) = getValues();'];

        yield ['<?php /* @var string $s */ print($s);'];

        yield ['<?php /* @var string $s */ echo($s);'];

        yield ['<?php /* @var User $bar */ ($baz = tmp())->doSomething();'];

        yield ['<?php /* @var User $bar */ list($bar) = a();'];

        yield ['<?php /* Before anonymous function */ $fn = fn($x) => $x + 1;'];

        yield ['<?php /* Before anonymous function */ fn($x) => $x + 1;'];

        yield ['<?php /* @var int $x */ [$x] = [2];'];
    }

    /**
     * @dataProvider provideNotPhpdocCandidateCases
     */
    public function testNotPhpdocCandidate(string $code): void
    {
        $tokens = Tokens::fromCode($code);
        $index = $tokens->getNextTokenOfKind(0, [[T_COMMENT], [T_DOC_COMMENT]]);
        $analyzer = new CommentsAnalyzer();

        self::assertFalse($analyzer->isBeforeStructuralElement($tokens, $index));
    }

    public static function provideNotPhpdocCandidateCases(): iterable
    {
        yield ['<?php class Foo {} /* At the end of file */'];

        yield ['<?php class Foo { public $baz; public function baz(); /* At the end of class */ }'];

        yield ['<?php /* Before increment */ $i++;'];

        yield ['<?php /* Comment, but not doc block */ if ($foo === -1) {};'];

        yield ['<?php
                $a = $b[1]; // @phpstan-ignore-line

                static::bar();',
        ];

        yield ['<?php /* @var int $a */ [$b] = [2];'];
    }

    /**
     * @dataProvider providePhpdocCandidatePhp80Cases
     *
     * @requires PHP 8.0
     */
    public function testPhpdocCandidatePhp80(string $code): void
    {
        $tokens = Tokens::fromCode($code);
        $index = $tokens->getNextTokenOfKind(0, [[T_COMMENT], [T_DOC_COMMENT]]);
        $analyzer = new CommentsAnalyzer();

        self::assertTrue($analyzer->isBeforeStructuralElement($tokens, $index));
    }

    public static function providePhpdocCandidatePhp80Cases(): iterable
    {
        yield 'attribute between class and phpDoc' => [
            '<?php
/**
 * @Annotation
 */
#[CustomAnnotationA]
Class MyAnnotation3 {}',
        ];
    }

    /**
     * @dataProvider providePhpdocCandidatePhp81Cases
     *
     * @requires PHP 8.1
     */
    public function testPhpdocCandidatePhp81(string $code): void
    {
        $tokens = Tokens::fromCode($code);
        $index = $tokens->getNextTokenOfKind(0, [[T_COMMENT], [T_DOC_COMMENT]]);
        $analyzer = new CommentsAnalyzer();

        self::assertTrue($analyzer->isBeforeStructuralElement($tokens, $index));
    }

    public static function providePhpdocCandidatePhp81Cases(): iterable
    {
        yield 'public readonly' => [
            '<?php class Foo { /* */ public readonly int $a1; }',
        ];

        yield 'readonly public' => [
            '<?php class Foo { /* */ readonly public int $a1; }',
        ];

        yield 'readonly union' => [
            '<?php class Foo { /* */ readonly A|B $a1; }',
        ];

        yield 'public final const' => [
            '<?php final class Foo2 extends B implements A
            {
                /* */
                public final const Y = "i";
            }',
        ];

        yield 'final public const' => [
            '<?php final class Foo2 extends B implements A
            {
                /* */
                final public const Y = "i";
            }',
        ];

        yield 'enum' => [
            '<?php /* Before enum */ enum Foo {}',
        ];

        yield 'enum with deprecated case' => [
            '<?php
enum Foo: int {
    /**
     * @deprecated Lorem ipsum
     */
    case BAR = 1;
}',
        ];
    }

    /**
     * @dataProvider provideNotPhpdocCandidatePhp81Cases
     *
     * @requires PHP 8.1
     */
    public function testNotPhpdocCandidatePhp81(string $code): void
    {
        $tokens = Tokens::fromCode($code);
        $index = $tokens->getNextTokenOfKind(0, [[T_COMMENT], [T_DOC_COMMENT]]);
        $analyzer = new CommentsAnalyzer();

        self::assertFalse($analyzer->isBeforeStructuralElement($tokens, $index));
    }

    public static function provideNotPhpdocCandidatePhp81Cases(): iterable
    {
        yield 'enum and switch' => [
            '<?php
            enum E {}
            switch ($x) {
                /* */
                case 1: return 2;
            }
            ',
        ];

        yield 'switch and enum' => [
            '<?php
            switch ($x) {
                /* */
                case 1: return 2;
            }
            enum E {}
            ',
        ];
    }
}
