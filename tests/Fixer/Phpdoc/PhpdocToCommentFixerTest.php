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

namespace PhpCsFixer\Tests\Fixer\Phpdoc;

use PhpCsFixer\Test\AbstractFixerTestCase;

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
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideDocblocks
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     *
     * @dataProvider provideTraits
     */
    public function testFixTraits($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideDocblocks()
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

        return $cases;
    }

    public function provideTraits()
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

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideCases71
     * @requires PHP 7.1
     */
    public function testFix71($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideCases71()
    {
        return [
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
        ];
    }
}
