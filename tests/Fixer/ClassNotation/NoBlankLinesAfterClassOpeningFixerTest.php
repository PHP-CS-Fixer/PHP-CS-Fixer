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

namespace PhpCsFixer\Tests\Fixer\ClassNotation;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;
use PhpCsFixer\WhitespacesFixerConfig;

/**
 * @author Ceeram <ceeram@cakephp.org>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ClassNotation\NoBlankLinesAfterClassOpeningFixer
 */
final class NoBlankLinesAfterClassOpeningFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideCases
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

    public function provideCases()
    {
        $cases = [];

        $cases[] = [
            '<?php
class Good
{
    public function firstMethod()
    {
        //code here
    }
}',
                '<?php
class Good
{

    public function firstMethod()
    {
        //code here
    }
}',
        ];
        $cases[] = [
            '<?php
class Good
{
    /**
     * Also no blank line before DocBlock
     */
    public function firstMethod()
    {
        //code here
    }
}',
                '<?php
class Good
{

    /**
     * Also no blank line before DocBlock
     */
    public function firstMethod()
    {
        //code here
    }
}',
        ];

        $cases[] = [
            '<?php
interface Good
{
    /**
     * Also no blank line before DocBlock
     */
    public function firstMethod();
}',
            '<?php
interface Good
{

    /**
     * Also no blank line before DocBlock
     */
    public function firstMethod();
}',
        ];

        // check if some fancy whitespaces aren't modified
        $cases[] = [
            '<?php
class Good
{public



    function firstMethod()
    {
        //code here
    }
}',
        ];

        return $cases;
    }

    public function provideTraits()
    {
        $cases = [];

        $cases[] = [
            '<?php
trait Good
{
    /**
     * Also no blank line before DocBlock
     */
    public function firstMethod() {}
}',
            '<?php
trait Good
{

    /**
     * Also no blank line before DocBlock
     */
    public function firstMethod() {}
}',
        ];

        return $cases;
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideMessyWhitespacesCases
     */
    public function testMessyWhitespaces($expected, $input = null)
    {
        $this->fixer->setWhitespacesConfig(new WhitespacesFixerConfig("\t", "\r\n"));

        $this->doTest($expected, $input);
    }

    public function provideMessyWhitespacesCases()
    {
        return [
            [
                "<?php\nclass Foo\n{\r\n    public function bar() {}\n}",
                "<?php\nclass Foo\n{\n\n    public function bar() {}\n}",
            ],
            [
                "<?php\nclass Foo\n{\r\n    public function bar() {}\n}",
                "<?php\nclass Foo\n{\r\n\r\n    public function bar() {}\n}",
            ],
        ];
    }
}
