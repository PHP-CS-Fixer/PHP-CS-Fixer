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

        static::assertSame($borders, $analyzer->getCommentBlockIndices($tokens, $index));
        static::assertFalse($analyzer->isHeaderComment($tokens, $index));
    }

    public static function provideCommentsCases(): array
    {
        return [
            'discover all 4 comments for the 1st comment with slash' => [
                '<?php
$foo;
// one
// two
// three
// four
$bar;',
                4,
                [4, 6, 8, 10],
            ],
            'discover all 4 comments for the 1st comment with hash' => [
                '<?php
$foo;
# one
# two
# three
# four
$bar;',
                4,
                [4, 6, 8, 10],
            ],
            'discover 3 comments out of 4 for the 2nd comment' => [
                '<?php
$foo;
// one
// two
// three
// four
$bar;',
                6,
                [6, 8, 10],
            ],
            'discover 3 comments when empty line separates 4th' => [
                '<?php
$foo;
// one
// two
// three

// four
$bar;',
                4,
                [4, 6, 8],
            ],
            'discover 3 comments when empty line of CR separates 4th' => [
                str_replace("\n", "\r", '<?php
$foo;
// one
// two
// three

// four
$bar;'),
                4,
                [4, 6, 8],
            ],
            'discover correctly when mix of slash and hash' => [
                '<?php
$foo;
// one
// two
# three
// four
$bar;',
                4,
                [4, 6],
            ],
            'do not group asterisk comments' => [
                '<?php
$foo;
/* one */
/* two */
/* three */
$bar;',
                4,
                [4],
            ],
            'handle fancy indent' => [
                '<?php
$foo;
        // one
       //  two
      //   three
     //    four
$bar;',
                4,
                [4, 6, 8, 10],
            ],
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

        static::assertTrue($analyzer->isHeaderComment($tokens, $index));
    }

    public static function provideHeaderCommentCases(): array
    {
        return [
            ['<?php /* Comment */ namespace Foo;', 1],
            ['<?php /** Comment */ namespace Foo;', 1],
            ['<?php declare(strict_types=1); /* Comment */ namespace Foo;', 9],
            ['<?php /* We test this one */ /* Foo */ namespace Bar;', 1],
            ['<?php /** Comment */ namespace Foo; declare(strict_types=1); /* Comment */ namespace Foo;', 1],
        ];
    }

    /**
     * @dataProvider provideNotHeaderCommentCases
     */
    public function testNotHeaderComment(string $code, int $index): void
    {
        $tokens = Tokens::fromCode($code);
        $analyzer = new CommentsAnalyzer();

        static::assertFalse($analyzer->isHeaderComment($tokens, $index));
    }

    public static function provideNotHeaderCommentCases(): array
    {
        return [
            ['<?php $foo; /* Comment */ $bar;', 4],
            ['<?php foo(); /* Comment */ $bar;', 6],
            ['<?php namespace Foo; /* Comment */ class Bar {};', 6],
            ['<?php /* It is not header when no content after */', 1],
            ['<?php /* Foo */ /* We test this one */ namespace Bar;', 3],
            ['<?php /* Foo */ declare(strict_types=1); /* We test this one */ namespace Bar;', 11],
        ];
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

        static::assertTrue($analyzer->isBeforeStructuralElement($tokens, $index));
    }

    public static function providePhpdocCandidateCases(): array
    {
        return [
            ['<?php /* @var Foo */ $bar = "baz";'],
            ['<?php /* Before namespace */ namespace Foo;'],
            ['<?php /* Before class */ class Foo {}'],
            ['<?php /* Before class */ abstract class Foo {}'],
            ['<?php /* Before class */ final class Foo {}'],
            ['<?php /* Before trait */ trait Foo {}'],
            ['<?php /* Before interface */ interface Foo {}'],
            ['<?php /* Before anonymous function */ function () {};'],
            ['<?php class Foo { /* Before property */ private $bar; }'],
            ['<?php class Foo { /* Before property */ protected $bar; }'],
            ['<?php class Foo { /* Before property */ public $bar; }'],
            ['<?php class Foo { /* Before property */ var $bar; }'],
            ['<?php class Foo { /* Before function */ function bar() {} }'],
            ['<?php class Foo { /* Before use */ use Bar; }'],
            ['<?php class Foo { /* Before function */ final function bar() {} }'],
            ['<?php class Foo { /* Before function */ private function bar() {} }'],
            ['<?php class Foo { /* Before function */ protected function bar() {} }'],
            ['<?php class Foo { /* Before function */ public function bar() {} }'],
            ['<?php class Foo { /* Before function */ static function bar() {} }'],
            ['<?php class Foo { /* Before function */ abstract function bar(); }'],
            ['<?php class Foo { /* Before constant */ const FOO = 42; }'],
            ['<?php /* Before require */ require "foo/php";'],
            ['<?php /* Before require_once */ require_once "foo/php";'],
            ['<?php /* Before include */ include "foo/php";'],
            ['<?php /* Before include_once */ include_once "foo/php";'],
            ['<?php /* @var array $foo */ foreach ($foo as $bar) {};'],
            ['<?php /* @var int $foo */ if ($foo === -1) {};'],
            ['<?php /* @var SomeClass $foo */ switch ($foo) { default: exit; };'],
            ['<?php /* @var bool $foo */ while ($foo) { $foo--; };'],
            ['<?php /* @var int $i */ for ($i = 0; $i < 16; $i++) {};'],
            ['<?php /* @var int $i @var int $j */ list($i, $j) = getValues();'],
            ['<?php /* @var string $s */ print($s);'],
            ['<?php /* @var string $s */ echo($s);'],
            ['<?php /* @var User $bar */ ($baz = tmp())->doSomething();'],
            ['<?php /* @var User $bar */ list($bar) = a();'],
        ];
    }

    /**
     * @dataProvider provideNotPhpdocCandidateCases
     */
    public function testNotPhpdocCandidate(string $code): void
    {
        $tokens = Tokens::fromCode($code);
        $index = $tokens->getNextTokenOfKind(0, [[T_COMMENT], [T_DOC_COMMENT]]);
        $analyzer = new CommentsAnalyzer();

        static::assertFalse($analyzer->isBeforeStructuralElement($tokens, $index));
    }

    public static function provideNotPhpdocCandidateCases(): array
    {
        return [
            ['<?php class Foo {} /* At the end of file */'],
            ['<?php class Foo { public $baz; public function baz(); /* At the end of class */ }'],
            ['<?php /* Before increment */ $i++;'],
            ['<?php /* Comment, but not doc block */ if ($foo === -1) {};'],
            ['<?php
                $a = $b[1]; // @phpstan-ignore-line

                static::bar();',
            ],
        ];
    }

    public function testPhpdocCandidate71(): void
    {
        $tokens = Tokens::fromCode('<?php /* @var int $x */ [$x] = [2];');
        $analyzer = new CommentsAnalyzer();

        static::assertTrue($analyzer->isHeaderComment($tokens, 1));
    }

    public function testNotPhpdocCandidate71(): void
    {
        $tokens = Tokens::fromCode('<?php /* @var int $a */ [$b] = [2];');
        $analyzer = new CommentsAnalyzer();

        static::assertFalse($analyzer->isBeforeStructuralElement($tokens, 1));
    }

    /**
     * @dataProvider providePhpdocCandidatePhp74Cases
     */
    public function testPhpdocCandidatePhp74(string $code): void
    {
        $tokens = Tokens::fromCode($code);
        $index = $tokens->getNextTokenOfKind(0, [[T_COMMENT], [T_DOC_COMMENT]]);
        $analyzer = new CommentsAnalyzer();

        static::assertTrue($analyzer->isBeforeStructuralElement($tokens, $index));
    }

    public static function providePhpdocCandidatePhp74Cases(): array
    {
        return [
            ['<?php /* Before anonymous function */ $fn = fn($x) => $x + 1;'],
        ];
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

        static::assertTrue($analyzer->isBeforeStructuralElement($tokens, $index));
    }

    public static function providePhpdocCandidatePhp80Cases(): array
    {
        return [
            ['<?php
/**
 * @Annotation
 */
#[CustomAnnotationA]
Class MyAnnotation3 {}'],
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

        static::assertTrue($analyzer->isBeforeStructuralElement($tokens, $index));
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
    }
}
