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

namespace PhpCsFixer\Tests\Fixer\PhpUnit;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Gert de Pagter
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\PhpUnit\PhpUnitSetUpTearDownVisibilityFixer
 */
final class PhpUnitSetUpTearDownVisibilityFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     *
     * @param mixed       $expected
     * @param null|string $input
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return array
     */
    public function provideFixCases()
    {
        return [
            'setUp and tearDown are made protected if they are public' => [
                '<?php
class FixerTest extends \PhpUnit\FrameWork\TestCase
{
    protected function setUp() {}

    protected function tearDown() {}
}
',
                '<?php
class FixerTest extends \PhpUnit\FrameWork\TestCase
{
    public function setUp() {}

    public function tearDown() {}
}
',
            ],
            'Other functions are ignored' => [
                '<?php
class FixerTest extends \PhpUnit\FrameWork\TestCase
{
    public function hello() {}

    protected function setUp() {}

    protected function tearDown() {}

    public function testWork() {}

    protected function testProtectedFunction() {}

    private function privateHelperFunction() {}
}
',
                '<?php
class FixerTest extends \PhpUnit\FrameWork\TestCase
{
    public function hello() {}

    public function setUp() {}

    public function tearDown() {}

    public function testWork() {}

    protected function testProtectedFunction() {}

    private function privateHelperFunction() {}
}
',
            ],
            'It works when setUp and tearDown are final' => [
                '<?php
class FixerTest extends \PhpUnit\FrameWork\TestCase
{
    protected final function setUp() {}

    final protected function tearDown() {}
}
',
                '<?php
class FixerTest extends \PhpUnit\FrameWork\TestCase
{
    public final function setUp() {}

    final public function tearDown() {}
}
',
            ],
            'It works when setUp and tearDown do not have visibility defined' => [
                '<?php
class FixerTest extends \PhpUnit\FrameWork\TestCase
{
    protected function setUp() {}

    protected function tearDown() {}
}
',
                '<?php
class FixerTest extends \PhpUnit\FrameWork\TestCase
{
    function setUp() {}

    function tearDown() {}
}
',
            ],
            'It works when setUp and tearDown do not have visibility defined and are final' => [
                '<?php
class FixerTest extends \PhpUnit\FrameWork\TestCase
{
    final protected function setUp() {}

    final protected function tearDown() {}
}
',
                '<?php
class FixerTest extends \PhpUnit\FrameWork\TestCase
{
    final function setUp() {}

    final function tearDown() {}
}
',
            ],
            'Functions outside a test class do not get changed' => [
                '<?php
class Fixer extends OtherClass
{
    public function hello() {}

    public function setUp() {}

    public function tearDown() {}

    public function testWork() {}

    protected function testProtectedFunction() {}

    private function privateHelperFunction() {}
}
',
            ],
            'It works even when setup and teardown have improper casing' => [
                '<?php
class FixerTest extends \PhpUnit\FrameWork\TestCase
{
    protected function sEtUp() {}

    protected function TeArDoWn() {}
}
',
                '<?php
class FixerTest extends \PhpUnit\FrameWork\TestCase
{
    public function sEtUp() {}

    public function TeArDoWn() {}
}
',
            ],
            'It works even with messy comments' => [
                '<?php
class FixerTest extends \PhpUnit\FrameWork\TestCase
{
    protected /** foo */ function setUp() {}

    /** foo */protected function tearDown() {}
}
',
                '<?php
class FixerTest extends \PhpUnit\FrameWork\TestCase
{
    public /** foo */ function setUp() {}

    /** foo */public function tearDown() {}
}
',
            ],
            'It works even with messy comments and no defined visibility' => [
                '<?php
class FixerTest extends \PhpUnit\FrameWork\TestCase
{
    /** foo */protected function setUp() {}

    /** bar */protected function tearDown() {}
}
',
                '<?php
class FixerTest extends \PhpUnit\FrameWork\TestCase
{
    /** foo */function setUp() {}

    /** bar */function tearDown() {}
}
',
            ],
            'Nothing changes if setUp or tearDown are private' => [
                '<?php
class FixerTest extends \PhpUnit\FrameWork\TestCase
{
    private function setUp() {}

    private function tearDown() {}
}
',
            ],
            'It works when there are multiple classes in one file' => [
                '<?php
class FixerTest extends \PhpUnit\FrameWork\TestCase
{
    protected function setUp() {}

    protected function tearDown() {}
}

class OtherTest extends \PhpUnit\FrameWork\TestCase
{
    protected function setUp() {}

    protected function tearDown() {}
}
',
                '<?php
class FixerTest extends \PhpUnit\FrameWork\TestCase
{
    public function setUp() {}

    public function tearDown() {}
}

class OtherTest extends \PhpUnit\FrameWork\TestCase
{
    public function setUp() {}

    public function tearDown() {}
}
',
            ],
        ];
    }
}
