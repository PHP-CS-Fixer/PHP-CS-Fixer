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

namespace PhpCsFixer\Tests\Indicator;

use PhpCsFixer\Indicator\PhpUnitTestCaseIndicator;
use PhpCsFixer\Tokenizer\Tokens;
use PHPUnit\Framework\TestCase;

/**
 * @author Gert de Pagter
 *
 * @internal
 *
 * @covers \PhpCsFixer\Indicator\PhpUnitTestCaseIndicator
 */
final class PhpUnitTestCaseIndicatorTest extends TestCase
{
    public function testItThrowsLogicExceptionWhenNoClassOnIndex()
    {
        $code = '<?php function hello () {}';

        $tokens = Tokens::fromCode($code);
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('No T_CLASS at given index 0, got T_OPEN_TAG.');
        $indicator = new PhpUnitTestCaseIndicator();
        $indicator->isPhpUnitClass($tokens, 0);
    }

    /**
     * @dataProvider provideTestClassCases
     *
     * @param string $code
     */
    public function testItIsAPhpUnitClass($code)
    {
        $tokens = Tokens::fromCode($code);
        $index = $tokens->getNextMeaningfulToken(0);
        $indicator = new PhpUnitTestCaseIndicator();
        $this->assertTrue($indicator->isPhpUnitClass($tokens, $index));
    }

    public function provideTestClassCases()
    {
        return [
            'Class name ends on Test' => [
              '<?php
class FooBarTest
{
}',
            ],
            'Class name ends on TestCase' => [
                '<?php
class FooBarTestCase
{
}',
            ],
            'Extended class name ends on Test' => [
                '<?php
class FooBar extends BarTest
{
}',
            ],
            'Extended class name ends on TestCase' => [
                '<?php
class FooBar extends BarTestCase
{
}',
            ],
            'Both class and extened class end on Test' => [
                '<?php
class FooBarTest extends BarTest
{
}',
            ],
        ];
    }

    /**
     * @dataProvider provideNonTestClassCases
     *
     * @param string$code
     */
    public function testItIsNotAPhpUnitClass($code)
    {
        $tokens = Tokens::fromCode($code);
        $index = $tokens->getNextMeaningfulToken(0);
        $indicator = new PhpUnitTestCaseIndicator();
        $this->assertFalse($indicator->isPhpUnitClass($tokens, $index));
    }

    public function provideNonTestClassCases()
    {
        return [
            'Class does not end on test or testcase' => ['<?php

class FooBar
{
}',
            ],
            'Both class and extended class do not end on test or testcase' => [
                '<?php

class FooBar extends BarFoo
{
}',
            ],
            'Class starts with Test but does not end with it' => [
                '<?php

class TestFooBar extends BarFoo
{
}',
            ],
            'Extended class starts with Test but does not end with it' => [
                '<?php

class FooBar extends TestBarFoo
{
}',
            ],
        ];
    }
}
