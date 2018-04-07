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
 * @author ErickSkrauch <erickskrauch@ely.by>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ClassNotation\BlankLineAroundClassBodyFixer
 */
final class BlankLineAroundClassBodyFixerTest extends AbstractFixerTestCase
{
    private static $configurationApplyForAnonymousClasses = ['apply_to_anonymous_classes' => true];
    private static $configurationTwoEmptyLines = ['blank_lines_count' => 2];

    /**
     * @param string      $expected
     * @param null|string $input
     * @param null|array  $configuration
     *
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input = null, array $configuration = null)
    {
        if (null !== $configuration) {
            $this->fixer->configure($configuration);
        }

        $this->doTest($expected, $input);
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     *
     * @dataProvider provideTraitsCases
     */
    public function testFixTraits($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases()
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
     * Also blank line before DocBlock
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
     * Also blank line before DocBlock
     */
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
     * Too many whitespaces
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
     * Too many whitespaces
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
     * Also blank line before DocBlock
     */
    public function firstMethod();

}',
            '<?php
interface Good
{
    /**
     * Also blank line before DocBlock
     */
    public function firstMethod();
}',
        ];
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
        $cases[] = [
            '<?php
$class = new class extends \DateTime {
    public $field;

    public function firstMethod() {}
};',
            '<?php
$class = new class extends \DateTime {

    public $field;

    public function firstMethod() {}

};',
        ];
        $cases[] = [
            '<?php
$class = new class extends \DateTime {

    public $field;

    public function firstMethod() {}

};',
            '<?php
$class = new class extends \DateTime {
    public $field;

    public function firstMethod() {}
};',
            self::$configurationApplyForAnonymousClasses,
        ];
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
            self::$configurationTwoEmptyLines,
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

    public function provideTraitsCases()
    {
        $cases = [];

        $cases[] = [
            '<?php
class Good
{
    use Foo\bar;

    public function firstMethod()
    {
        //code here
    }

}',
            '<?php
class Good
{
    use Foo\bar;

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
    use Foo\bar;
    use Foo\baz;

    public function firstMethod()
    {
        //code here
    }

}',
            '<?php
class Good
{
    use Foo\bar;
    use Foo\baz;

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
    use Foo, Bar {
        Bar::smallTalk insteadof A;
        Foo::bigTalk insteadof B;
    }

    public function firstMethod()
    {
        //code here
    }

}',
            '<?php
class Good
{
    use Foo, Bar {
        Bar::smallTalk insteadof A;
        Foo::bigTalk insteadof B;
    }

    public function firstMethod()
    {
        //code here
    }
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
        /** @var \PhpCsFixer\Fixer\WhitespacesAwareFixerInterface $fixer */
        $fixer = $this->fixer;
        $fixer->setWhitespacesConfig(new WhitespacesFixerConfig("\t", "\r\n"));

        $this->doTest($expected, $input);
    }

    public function provideMessyWhitespacesCases()
    {
        return [
            [
                "<?php\nclass Foo\n{\r\n\r\n    public function bar() {}\r\n\r\n}",
                "<?php\nclass Foo\n{\n    public function bar() {}\n}",
            ],
            [
                "<?php\nclass Foo\n{\r\n\r\n    public function bar() {}\r\n\r\n}",
                "<?php\nclass Foo\n{\r\n\r\n\n\n    public function bar() {}\n\n\n\n}",
            ],
        ];
    }
}
