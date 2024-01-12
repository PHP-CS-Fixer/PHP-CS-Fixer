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
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): iterable
    {
        yield 'setUp and tearDown are made protected if they are public' => [
            <<<'EOD'
                <?php
                class FixerTest extends \PhpUnit\FrameWork\TestCase
                {
                    protected function setUp() {}

                    protected function tearDown() {}
                }

                EOD,
            <<<'EOD'
                <?php
                class FixerTest extends \PhpUnit\FrameWork\TestCase
                {
                    public function setUp() {}

                    public function tearDown() {}
                }

                EOD,
        ];

        yield 'Other functions are ignored' => [
            <<<'EOD'
                <?php
                class FixerTest extends \PhpUnit\FrameWork\TestCase
                {
                    public function hello() {}

                    protected function setUp() {}

                    protected function tearDown() {}

                    public function testWork() {}

                    protected function testProtectedFunction() {}

                    private function privateHelperFunction() {}
                }

                EOD,
            <<<'EOD'
                <?php
                class FixerTest extends \PhpUnit\FrameWork\TestCase
                {
                    public function hello() {}

                    public function setUp() {}

                    public function tearDown() {}

                    public function testWork() {}

                    protected function testProtectedFunction() {}

                    private function privateHelperFunction() {}
                }

                EOD,
        ];

        yield 'It works when setUp and tearDown are final' => [
            <<<'EOD'
                <?php
                class FixerTest extends \PhpUnit\FrameWork\TestCase
                {
                    protected final function setUp() {}

                    final protected function tearDown() {}
                }

                EOD,
            <<<'EOD'
                <?php
                class FixerTest extends \PhpUnit\FrameWork\TestCase
                {
                    public final function setUp() {}

                    final public function tearDown() {}
                }

                EOD,
        ];

        yield 'It works when setUp and tearDown do not have visibility defined' => [
            <<<'EOD'
                <?php
                class FixerTest extends \PhpUnit\FrameWork\TestCase
                {
                    protected function setUp() {}

                    protected function tearDown() {}
                }

                EOD,
            <<<'EOD'
                <?php
                class FixerTest extends \PhpUnit\FrameWork\TestCase
                {
                    function setUp() {}

                    function tearDown() {}
                }

                EOD,
        ];

        yield 'It works when setUp and tearDown do not have visibility defined and are final' => [
            <<<'EOD'
                <?php
                class FixerTest extends \PhpUnit\FrameWork\TestCase
                {
                    final protected function setUp() {}

                    final protected function tearDown() {}
                }

                EOD,
            <<<'EOD'
                <?php
                class FixerTest extends \PhpUnit\FrameWork\TestCase
                {
                    final function setUp() {}

                    final function tearDown() {}
                }

                EOD,
        ];

        yield 'Functions outside a test class do not get changed' => [
            <<<'EOD'
                <?php
                class Fixer extends OtherClass
                {
                    public function hello() {}

                    public function setUp() {}

                    public function tearDown() {}

                    public function testWork() {}

                    protected function testProtectedFunction() {}

                    private function privateHelperFunction() {}
                }

                EOD,
        ];

        yield 'It works even when setup and teardown have improper casing' => [
            <<<'EOD'
                <?php
                class FixerTest extends \PhpUnit\FrameWork\TestCase
                {
                    protected function sEtUp() {}

                    protected function TeArDoWn() {}
                }

                EOD,
            <<<'EOD'
                <?php
                class FixerTest extends \PhpUnit\FrameWork\TestCase
                {
                    public function sEtUp() {}

                    public function TeArDoWn() {}
                }

                EOD,
        ];

        yield 'It works even with messy comments' => [
            <<<'EOD'
                <?php
                class FixerTest extends \PhpUnit\FrameWork\TestCase
                {
                    protected /** foo */ function setUp() {}

                    /** foo */protected function tearDown() {}
                }

                EOD,
            <<<'EOD'
                <?php
                class FixerTest extends \PhpUnit\FrameWork\TestCase
                {
                    public /** foo */ function setUp() {}

                    /** foo */public function tearDown() {}
                }

                EOD,
        ];

        yield 'It works even with messy comments and no defined visibility' => [
            <<<'EOD'
                <?php
                class FixerTest extends \PhpUnit\FrameWork\TestCase
                {
                    /** foo */protected function setUp() {}

                    /** bar */protected function tearDown() {}
                }

                EOD,
            <<<'EOD'
                <?php
                class FixerTest extends \PhpUnit\FrameWork\TestCase
                {
                    /** foo */function setUp() {}

                    /** bar */function tearDown() {}
                }

                EOD,
        ];

        yield 'Nothing changes if setUp or tearDown are private' => [
            <<<'EOD'
                <?php
                class FixerTest extends \PhpUnit\FrameWork\TestCase
                {
                    private function setUp() {}

                    private function tearDown() {}
                }

                EOD,
        ];

        yield 'It works when there are multiple classes in one file' => [
            <<<'EOD'
                <?php
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

                EOD,
            <<<'EOD'
                <?php
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

                EOD,
        ];
    }
}
