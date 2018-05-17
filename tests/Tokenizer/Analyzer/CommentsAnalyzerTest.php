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

namespace PhpCsFixer\Tests\Tokenizer\Analyzer;

use PhpCsFixer\Tests\TestCase;
use PhpCsFixer\Tokenizer\Analyzer\CommentsAnalyzer;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Kuba Werłos <werlos@gmail.com>
 * @author SpacePossum
 *
 * @internal
 *
 * @covers \PhpCsFixer\Tokenizer\Analyzer\CommentsAnalyzer
 */
final class CommentsAnalyzerTest extends TestCase
{
    public function testWhenNotPointingToComment()
    {
        $analyzer = new CommentsAnalyzer();
        $tokens = Tokens::fromCode('<?php $no; $comment; $here;');

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Given index must point to a comment.');

        $analyzer->getCommentBlockIndices($tokens, 4);
    }

    /**
     * @param string     $code
     * @param int        $index
     * @param null|array $borders
     *
     * @dataProvider provideCommentsCases
     */
    public function testComments($code, $index, $borders)
    {
        $tokens = Tokens::fromCode($code);
        $analyzer = new CommentsAnalyzer();

        $this->assertSame($borders, $analyzer->getCommentBlockIndices($tokens, $index));
    }

    public function provideCommentsCases()
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

    public function testHeaderCommentAcceptsOnlyComments()
    {
        $tokens = Tokens::fromCode('<?php 1; 2; 3;');
        $analyzer = new CommentsAnalyzer();

        $this->expectException(\InvalidArgumentException::class);

        $analyzer->isHeaderComment($tokens, 2);
    }

    public function testHeaderComment()
    {
        $tokens = Tokens::fromCode('<?php /* This is header */ namespace Foo;');
        $analyzer = new CommentsAnalyzer();

        $this->assertTrue($analyzer->isHeaderComment($tokens, 1));
    }

    public function testNotHeaderComment()
    {
        $tokens = Tokens::fromCode('<?php /* This is not header */');
        $analyzer = new CommentsAnalyzer();

        $this->assertFalse($analyzer->isHeaderComment($tokens, 1));
    }

    public function testPhpdocCandidateAcceptsOnlyComments()
    {
        $tokens = Tokens::fromCode('<?php 1; 2; 3;');
        $analyzer = new CommentsAnalyzer();

        $this->expectException(\InvalidArgumentException::class);

        $analyzer->isBeforeStructuralElement($tokens, 2);
    }

    /**
     * @param bool   $isPhpdocCandidate
     * @param string $code
     *
     * @dataProvider providePhpdocCandidateCases
     */
    public function testPhpdocCandidate($code)
    {
        $tokens = Tokens::fromCode($code);
        $index = $tokens->getNextTokenOfKind(0, [[T_COMMENT], [T_DOC_COMMENT]]);
        $analyzer = new CommentsAnalyzer();

        $this->assertTrue($analyzer->isBeforeStructuralElement($tokens, $index));
    }

    public function providePhpdocCandidateCases()
    {
        return [
            ['<?php /* @var Foo */ $bar = "baz";'],
            ['<?php /* Before namespace */ namespace Foo;'],
            ['<?php /* Before class */ class Foo {}'],
            ['<?php /* Before class */ abstract class Foo {}'],
            ['<?php /* Before class */ final class Foo {}'],
            ['<?php /* Before trait */ trait Foo {}'],
            ['<?php /* Before interface */ interface Foo {}'],
            ['<?php class Foo { /* Before property */ private $bar; }'],
            ['<?php class Foo { /* Before property */ protected $bar; }'],
            ['<?php class Foo { /* Before property */ public $bar; }'],
            ['<?php class Foo { /* Before property */ var $bar; }'],
            ['<?php class Foo { /* Before function */ function bar() {} }'],
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
        ];
    }

    /**
     * @param bool   $isPhpdocCandidate
     * @param string $code
     *
     * @dataProvider provideNotPhpdocCandidateCases
     */
    public function testNotPhpdocCandidate($code)
    {
        $tokens = Tokens::fromCode($code);
        $index = $tokens->getNextTokenOfKind(0, [[T_COMMENT], [T_DOC_COMMENT]]);
        $analyzer = new CommentsAnalyzer();

        $this->assertFalse($analyzer->isBeforeStructuralElement($tokens, $index));
    }

    public function provideNotPhpdocCandidateCases()
    {
        return [
            ['<?php class Foo {} /* At the end of file */'],
            ['<?php class Foo { public $baz; public function baz(); /* At the end of class */ }'],
            ['<?php /* Before increment */ $i++;'],
            ['<?php /* Comment, but not doc block */ if ($foo === -1) {};'],
        ];
    }

    /**
     * @requires PHP 7.1
     */
    public function testPhpdocCandidate71()
    {
        $tokens = Tokens::fromCode('<?php /* @var int $x */ [$x] = [2];');
        $analyzer = new CommentsAnalyzer();

        $this->assertTrue($analyzer->isHeaderComment($tokens, 1));
    }

    /**
     * @requires PHP 7.1
     */
    public function testNotPhpdocCandidate71()
    {
        $tokens = Tokens::fromCode('<?php /* @var int $a */ [$b] = [2];');
        $analyzer = new CommentsAnalyzer();

        $this->assertFalse($analyzer->isBeforeStructuralElement($tokens, 1));
    }
}
