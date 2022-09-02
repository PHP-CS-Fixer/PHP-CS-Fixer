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

namespace PhpCsFixer\Tests\Fixer\Phpdoc;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Ceeram <ceeram@cakephp.org>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Phpdoc\PhpdocToCommentFixer
 */
final class PhpdocToCommentFixerTest extends AbstractFixerTestCase
{
    /**
     * @param array<string, mixed> $config
     *
     * @dataProvider provideDocblocksCases
     * @dataProvider provideTraitsCases
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null, array $config = []): void
    {
        $this->fixer->configure($config);

        $this->doTest($expected, $input);
    }

    public function provideDocblocksCases(): array
    {
        $cases = [];

        $cases[] = [
            '<?php
/**
 * Do not convert this
 */
 namespace Docs;

/**
 * Do not convert this
 */
class DocBlocks
{
    /**
     * Do not convert this
     */
    use TestTrait;

    /**
     * Do not convert this
     */
    const STRUCTURAL = true;

    /**
     * Do not convert this
     */
    protected $indent = false;

    /**
     * Do not convert this
     */
    var $oldPublicStyle;

    /**
     * Do not convert this
     */
    public function test() {}

    /**
     * Do not convert this
     */
    private function testPrivate() {}

    /**
     * Do not convert this
     */
    function testNoVisibility() {}
}',
        ];

        $cases[] = [
            '<?php namespace Docs;

/**
 * Do not convert this
 */

/**
 * Do not convert this
 */
class DocBlocks{}
',
        ];

        $cases[] = [
            '<?php

/**
 * Do not convert this
 */

namespace Foo;
',
        ];

        $cases[] = [
            '<?php
$first = true;// needed because by default first docblock is never fixed.

/**
 * Do not convert this
 */
abstract class DocBlocks
{

    /**
     * Do not convert this
     */
    abstract public function test();
}',
        ];

        $cases[] = [
            '<?php
$first = true;// needed because by default first docblock is never fixed.

/**
 * Do not convert this
 */
interface DocBlocks
{
    public function test();
}',
        ];

        $cases[] = [
            '<?php
namespace NS;

/**
 * Do not
 */
final class Foo
{
}',
        ];

        $cases[] = [
            '<?php
$first = true;// needed because by default first docblock is never fixed.

/**
 * Do not convert this
 */
require "require.php";

/**
 * Do not convert this
 */
require_once "require_once.php";

/**
 * Do not convert this
 */
include "include.php";

/**
 * Do not convert this
 */
include_once "include_once.php";
',
        ];

        $cases[] = [
            '<?php
$first = true;// needed because by default first docblock is never fixed.

/**
 * Do not convert this
 *
 * @var int
 */
$a = require "require.php";

/**
 * Do not convert this
 *
 * @var int
 */
$b = require_once "require_once.php";

/**
 * Do not convert this
 *
 * @var int
 */
$c = include "include.php";

/**
 * Do not convert this
 *
 * @var int
 */
$d = include_once "include_once.php";

/**
 * @var Composer\Autoload\ClassLoader $loader
 */
$loader = require_once __DIR__."/vendor/autoload.php";
',
        ];

        $cases[] = [
            '<?php
$first = true;// needed because by default first docblock is never fixed.

/**
 * @var ClassLoader $loader
 */
$loader = require_once __DIR__."/../app/autoload.php";
',
        ];

        $cases[] = [
            '<?php
$first = true;// needed because by default first docblock is never fixed.

/**
 * Do not convert this
 *
 * @var Foo
 */
$foo = createFoo();
',
        ];

        $cases[] = [
            '<?php
$first = true;// needed because by default first docblock is never fixed.

/**
 * Do not convert this
 *
 * @var bool $local
 */
$local = true;
',
        ];

        $cases[] = [
            '<?php
$first = true;// needed because by default first docblock is never fixed.

/**
 * Comment
 */
$local = true;
',
        ];

        $cases[] = [
            '<?php
$first = true;// needed because by default first docblock is never fixed.

/** @var \Sqlite3 $sqlite */
foreach($connections as $sqlite) {
    $sqlite->open($path);
}',
        ];

        $cases[] = [
            '<?php
$first = true;// needed because by default first docblock is never fixed.

/** @var \Sqlite3 $sqlite */
foreach($connections as $key => $sqlite) {
    $sqlite->open($path);
}',
        ];

        $cases[] = [
            '<?php
$first = true;// needed because by default first docblock is never fixed.

/** @var int $key */
foreach($connections as $key => $sqlite) {
    $sqlite->open($path);
}',
        ];

        $cases[] = [
            '<?php
$first = true;// needed because by default first docblock is never fixed.

/* This should not be a docblock */
foreach($connections as $key => $sqlite) {
    $sqlite->open($path);
}',
            '<?php
$first = true;// needed because by default first docblock is never fixed.

/** This should not be a docblock */
foreach($connections as $key => $sqlite) {
    $sqlite->open($path);
}',
        ];

        $cases[] = [
            '<?php
$first = true;// needed because by default first docblock is never fixed.

/* there should be no docblock here */
$sqlite1->open($path);
',
            '<?php
$first = true;// needed because by default first docblock is never fixed.

/** there should be no docblock here */
$sqlite1->open($path);
',
        ];

        $cases[] = [
            '<?php
$first = true;// needed because by default first docblock is never fixed.

/* there should be no docblock here */
$i++;
',
            '<?php
$first = true;// needed because by default first docblock is never fixed.

/** there should be no docblock here */
$i++;
',
        ];

        $cases[] = [
            '<?php
$first = true;// needed because by default first docblock is never fixed.

/** @var int $index */
$index = $a[\'number\'];
',
        ];

        $cases[] = [
            '<?php
$first = true;// needed because by default first docblock is never fixed.

/** @var string $two */
list($one, $two) = explode("," , $csvLines);
',
        ];

        $cases[] = [
            '<?php
$first = true;// needed because by default first docblock is never fixed.

/* This should be a comment */
list($one, $two) = explode("," , $csvLines);
',
            '<?php
$first = true;// needed because by default first docblock is never fixed.

/** This should be a comment */
list($one, $two) = explode("," , $csvLines);
',
        ];

        $cases[] = [
            '<?php
$first = true;// needed because by default first docblock is never fixed.

/** @var int $index */
foreach ($foo->getPairs($c->bar(), $bar) as $index => list($a, $b)) {
    // Do something with $index, $a and $b
}

/** @var \Closure $value */
if (!$value = $this->getValue()) {
    return false;
}

/** @var string $name */
switch ($name = $this->getName()) {
    case "John":
        return false;
    case "Jane":
        return true;
}

/** @var string $content */
while ($content = $this->getContent()) {
    $name .= $content;
}

/** @var int $size */
for($i = 0, $size = count($people); $i < $size; ++$i) {
    $people[$i][\'salt\'] = mt_rand(000000, 999999);
}',
        ];

        $cases[] = [
            '<?php
$first = true;// needed because by default first docblock is never fixed.

/* @var int $wrong */
foreach ($foo->getPairs($c->bar(), $bar) as $index => list($a, $b)) {
    // Do something with $index, $a and $b
}

/* @var \Closure $notValue */
if (!$value = $this->getValue()) {
    return false;
}

/* @var string $notName */
switch ($name = $this->getName()) {
    case "John":
        return false;
    case "Jane":
        return true;
}

/* @var string $notContent */
while ($content = $this->getContent()) {
    $name .= $content;
}

/* @var int $notSize */
for($i = 0, $size = count($people); $i < $size; ++$i) {
    $people[$i][\'salt\'] = mt_rand(000000, 999999);
}',
            '<?php
$first = true;// needed because by default first docblock is never fixed.

/** @var int $wrong */
foreach ($foo->getPairs($c->bar(), $bar) as $index => list($a, $b)) {
    // Do something with $index, $a and $b
}

/** @var \Closure $notValue */
if (!$value = $this->getValue()) {
    return false;
}

/** @var string $notName */
switch ($name = $this->getName()) {
    case "John":
        return false;
    case "Jane":
        return true;
}

/** @var string $notContent */
while ($content = $this->getContent()) {
    $name .= $content;
}

/** @var int $notSize */
for($i = 0, $size = count($people); $i < $size; ++$i) {
    $people[$i][\'salt\'] = mt_rand(000000, 999999);
}',
        ];

        $cases[] = [
            '<?php
/* This should be a comment */
',
            '<?php
/** This should be a comment */
',
        ];

        $cases[] = [
            '<?php
/**
 * This is a page level docblock should stay untouched
 */

echo "Some string";
',
        ];

        $cases[] = [
            '<?php
$first = true;// needed because by default first docblock is never fixed.

/** @var \NumberFormatter $formatter */
static $formatter;
',
        ];

        $cases[] = [
            '<?php
$first = true;// needed because by default first docblock is never fixed.

function getNumberFormatter()
{
    /** @var \NumberFormatter $formatter */
    static $formatter;
}
',
        ];

        $cases[] = [
            '<?php

class A
{
    public function b()
    {
        /** @var int $c */
        print($c = 0);
    }
}
',
        ];

        $cases[] = ['<?php
/** header */
echo 123;

/** @var int $bar1 */
(print($bar1 = 0));
            ',
        ];

        $cases[] = [
            '<?php
/** header */
echo 123;

/** @var ClassLoader $loader */
$loader = require __DIR__.\'/../vendor/autoload.php\';
',
        ];

        $cases[] = [
            '<?php
$first = true;// needed because by default first docblock is never fixed.

/** @todo Do not convert this */
foreach($connections as $key => $sqlite) {
    $sqlite->open($path);
}',
            null,
            ['ignored_tags' => ['todo']],
        ];

        $cases[] = [
            '<?php
$first = true;// needed because by default first docblock is never fixed.

/* Convert this */
foreach($connections as $key => $sqlite) {
    $sqlite->open($path);
}

/** @todo Do not convert this */
foreach($connections as $key => $sqlite) {
    $sqlite->open($path);
}',
            '<?php
$first = true;// needed because by default first docblock is never fixed.

/** Convert this */
foreach($connections as $key => $sqlite) {
    $sqlite->open($path);
}

/** @todo Do not convert this */
foreach($connections as $key => $sqlite) {
    $sqlite->open($path);
}',
            ['ignored_tags' => ['todo']],
        ];

        $cases[] = [
            '<?php
$first = true;// needed because by default first docblock is never fixed.

/* Convert this */
foreach($connections as $key => $sqlite) {
    $sqlite->open($path);
}

/** @fix-me Do not convert this */
foreach($connections as $key => $sqlite) {
    $sqlite->open($path);
}',
            '<?php
$first = true;// needed because by default first docblock is never fixed.

/** Convert this */
foreach($connections as $key => $sqlite) {
    $sqlite->open($path);
}

/** @fix-me Do not convert this */
foreach($connections as $key => $sqlite) {
    $sqlite->open($path);
}',
            ['ignored_tags' => ['fix-me']],
        ];

        $cases[] = [
            '<?php
$first = true;// needed because by default first docblock is never fixed.

/* @todoNot Convert this */
foreach($connections as $key => $sqlite) {
    $sqlite->open($path);
}

/** @TODO Do not convert this */
foreach($connections as $key => $sqlite) {
    $sqlite->open($path);
}',
            '<?php
$first = true;// needed because by default first docblock is never fixed.

/** @todoNot Convert this */
foreach($connections as $key => $sqlite) {
    $sqlite->open($path);
}

/** @TODO Do not convert this */
foreach($connections as $key => $sqlite) {
    $sqlite->open($path);
}',
            ['ignored_tags' => ['todo']],
        ];

        $cases[] = [
            '<?php
$first = true;// needed because by default first docblock is never fixed.

/* Convert this */
foreach($connections as $key => $sqlite) {
    $sqlite->open($path);
}

/**
 * @deprecated This tag is not in the list but the next one is
 * @todo This should be a PHPDoc as the tag is on "ignored_tags" list
 */
foreach($connections as $key => $sqlite) {
    $sqlite->open($path);
}',
            '<?php
$first = true;// needed because by default first docblock is never fixed.

/** Convert this */
foreach($connections as $key => $sqlite) {
    $sqlite->open($path);
}

/**
 * @deprecated This tag is not in the list but the next one is
 * @todo This should be a PHPDoc as the tag is on "ignored_tags" list
 */
foreach($connections as $key => $sqlite) {
    $sqlite->open($path);
}',
            ['ignored_tags' => ['todo']],
        ];

        return $cases;
    }

    public function provideTraitsCases(): array
    {
        return [
            [
                '<?php
$first = true;// needed because by default first docblock is never fixed.

/**
 * Do not convert this
 */
trait DocBlocks
{
    public function test() {}
}',
            ],
        ];
    }

    public function provideFixCases(): array
    {
        return [
            [
                '<?php
/** header */
echo 123;

/** @var User $bar3 */
($bar3 = tmp())->doSomething();

/** @var Session $session */ # test
$session = new Session();
                ',
            ],
            [
                '<?php
$first = true;// needed because by default first docblock is never fixed.

/** @var int $a */
[$a] = $b;

/* @var int $c */
[$a] = $c;
                ',
                '<?php
$first = true;// needed because by default first docblock is never fixed.

/** @var int $a */
[$a] = $b;

/** @var int $c */
[$a] = $c;
                ',
            ],
            [
                '<?php
$first = true;// needed because by default first docblock is never fixed.

/**
 * @var int $a
 */
[$a] = $b;
                ',
            ],
            [
                '<?php
                class Foo {
                    /**
                     * Do not convert this
                     */
                    private int $foo;
                }',
            ],
            [
                '<?php
                class Foo {
                    /**
                     * Do not convert this
                     */
                    protected ?string $foo;
                }',
            ],
            [
                '<?php
                class Foo {
                    /**
                     * Do not convert this
                     */
                    public ? float $foo;
                }',
            ],
            [
                '<?php
                class Foo {
                    /**
                     * Do not convert this
                     */
                    var ? Foo\Bar $foo;
                }',
            ],
            [
                '<?php
                class Foo {
                    /**
                     * Do not convert this
                     */
                    var ? array $foo;
                }',
            ],
        ];
    }

    /**
     * @dataProvider provideFix80Cases
     *
     * @requires PHP 8.0
     */
    public function testFix80(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function provideFix80Cases(): iterable
    {
        yield [
            '<?php
/**
 * @Annotation
 */
#[CustomAnnotationA]
Class MyAnnotation3
{
    /**
     * @Annotation
     */
    #[CustomAnnotationB]
    #[CustomAnnotationC]
    public function foo() {}

    /**
     * @Annotation
     */
    #[CustomAnnotationD]
    public $var;

    /*
     * end of class
     */
}',
            '<?php
/**
 * @Annotation
 */
#[CustomAnnotationA]
Class MyAnnotation3
{
    /**
     * @Annotation
     */
    #[CustomAnnotationB]
    #[CustomAnnotationC]
    public function foo() {}

    /**
     * @Annotation
     */
    #[CustomAnnotationD]
    public $var;

    /**
     * end of class
     */
}',
        ];

        yield [
            '<?php
class Foo
{
	public function __construct(
	    /** @var string Do not convert this */
		public string $bar
	) {
	}
}
',
        ];
    }

    /**
     * @dataProvider provideFix81Cases
     *
     * @requires PHP 8.1
     */
    public function testFix81(string $expected): void
    {
        $this->doTest($expected);
    }

    public function provideFix81Cases(): iterable
    {
        yield 'enum' => [
            '<?php
declare(strict_types=1);

namespace PhpCsFixer\Tests\Tokenizer\Analyzer;

/** Before enum */
enum Foo {
    //
}',
        ];
    }
}
