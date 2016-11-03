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

use PhpCsFixer\Test\AbstractFixerTestCase;
use PhpCsFixer\WhitespacesFixerConfig;

/**
 * @author Ceeram <ceeram@cakephp.org>
 *
 * @internal
 */
final class NoBlankLinesAfterClassOpeningFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    /**
     * @requires PHP 5.4
     * @dataProvider provideTraits
     */
    public function testFixTraits($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideCases()
    {
        $cases = array();

        $cases[] = array(
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
        );
        $cases[] = array(
            '<?php
class Good
{
    /**
     * Also no blankline before DocBlock
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
     * Also no blankline before DocBlock
     */
    public function firstMethod()
    {
        //code here
    }
}',
        );

        $cases[] = array(
            '<?php
interface Good
{
    /**
     * Also no blankline before DocBlock
     */
    public function firstMethod();
}',
            '<?php
interface Good
{

    /**
     * Also no blankline before DocBlock
     */
    public function firstMethod();
}',
        );

        // check if some fancy whitespaces aren't modified
        $cases[] = array(
            '<?php
class Good
{public



    function firstMethod()
    {
        //code here
    }
}',
        );

        return $cases;
    }

    public function provideTraits()
    {
        $cases = array();

        $cases[] = array(
            '<?php
trait Good
{
    /**
     * Also no blankline before DocBlock
     */
    public function firstMethod() {}
}',
            '<?php
trait Good
{

    /**
     * Also no blankline before DocBlock
     */
    public function firstMethod() {}
}',
        );

        return $cases;
    }

    /**
     * @dataProvider provideMessyWhitespacesCases
     */
    public function testMessyWhitespaces($expected, $input = null)
    {
        $fixer = clone $this->getFixer();
        $fixer->setWhitespacesConfig(new WhitespacesFixerConfig("\t", "\r\n"));

        $this->doTest($expected, $input, null, $fixer);
    }

    public function provideMessyWhitespacesCases()
    {
        return array(
            array(
                "<?php\nclass Foo\n{\r\n    public function bar() {}\n}",
                "<?php\nclass Foo\n{\n\n    public function bar() {}\n}",
            ),
            array(
                "<?php\nclass Foo\n{\r\n    public function bar() {}\n}",
                "<?php\nclass Foo\n{\r\n\r\n    public function bar() {}\n}",
            ),
        );
    }
}
