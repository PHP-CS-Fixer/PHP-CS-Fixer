<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer\Symfony;

use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

/**
 * @author Ceeram <ceeram@cakephp.org>
 */
class PhpdocToCommentFixerTest extends AbstractFixerTestBase
{
    /**
     * @dataProvider provideDocblocks
     */
    public function testFix($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    /**
     * @requires PHP 5.4
     * @dataProvider provideTraits
     */
    public function testFixTraits($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    public function provideDocblocks()
    {
        $cases = array();

        $cases[] = array(
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
        );

        $cases[] = array(
            '<?php
/**
 * Do not convert this
 */
abstract class DocBlocks
{

    /**
     * Do not convert this
     */
    abstract public function test() {}
}',
        );

        $cases[] = array(
            '<?php
/**
 * Do not convert this
 */
interface DocBlocks
{
    public function test() {}
}',
        );

        $cases[] = array(
            '<?php
namespace NS;

/**
 * Do not
 */
final class Foo
{
}',
        );

        $cases[] = array(
            '<?php
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
        );

        $cases[] = array(
            '<?php
/**
 * Do not convert this
 *
 * @var bool $local
 */
$local = true;
',
        );

        $cases[] = array(
            '<?php
$first = true;

/*
 * This should be a normal comment
 */
$local = true;
',
            '<?php
$first = true;

/**
 * This should be a normal comment
 */
$local = true;
',
        );

        $cases[] = array(
            '<?php
/** @var \Sqlite3 $sqlite */
foreach($connections as $sqlite) {
    $sqlite->open($path);
}',
        );

        $cases[] = array(
            '<?php
/** @var \Sqlite3 $sqlite */
foreach($connections as $key => $sqlite) {
    $sqlite->open($path);
}',
        );

        $cases[] = array(
            '<?php
/** @var int $key */
foreach($connections as $key => $sqlite) {
    $sqlite->open($path);
}',
        );

        $cases[] = array(
            '<?php
$first = true;

/* This should not be a docblock */
foreach($connections as $key => $sqlite) {
    $sqlite->open($path);
}',
            '<?php
$first = true;

/** This should not be a docblock */
foreach($connections as $key => $sqlite) {
    $sqlite->open($path);
}',
        );

        $cases[] = array(
            '<?php
$first = true;

/* there should be no docblock here */
$sqlite1->open($path);
}',
            '<?php
$first = true;

/** there should be no docblock here */
$sqlite1->open($path);
}',
        );

        $cases[] = array(
            '<?php
$first = true;

/* there should be no docblock here */
$i++;
}',
            '<?php
$first = true;

/** there should be no docblock here */
$i++;
}',
        );

        $cases[] = array(
            '<?php
/** @var int $index */
$index = $a[\'number\'];
',
        );

        $cases[] = array(
            '<?php
/** @var string $two */
list($one, $two) = explode("," , $csvLines);
',
        );

        $cases[] = array(
            '<?php
$first = true;

/* This should be a comment */
list($one, $two) = explode("," , $csvLines);
',
            '<?php
$first = true;

/** This should be a comment */
list($one, $two) = explode("," , $csvLines);
',
        );

        $cases[] = array(
            '<?php
/** @var int $index */
foreach ($foo->getPairs($c->bar(), $bar) as $index => list($a, $b)) [
    // Do something with $index, $a and $b
}',
        );

        $cases[] = array(
            '<?php
/* This should be a comment */
',
            '<?php
/** This should be a comment */
',
        );

        $cases[] = array(
            '<?php
/**
 * This is a page level docblock should stay untouched
 */

echo "Some string";
',
        );

        return $cases;
    }

    public function provideTraits()
    {
        return array(
            array(
                '<?php
/**
 * Do not convert this
 */
trait DocBlocks
{
    public function test() {}
}',
            ),
        );
    }
}
