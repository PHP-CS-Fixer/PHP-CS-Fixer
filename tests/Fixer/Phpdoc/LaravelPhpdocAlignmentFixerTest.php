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
use PhpCsFixer\WhitespacesFixerConfig;

/**
 * @author Jakub Kwaśniewski <jakub@zero-85.pl>
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Phpdoc\LaravelPhpdocAlignmentFixer
 */
final class LaravelPhpdocAlignmentFixerTest extends AbstractFixerTestCase
{
    public function testFix(): void
    {
        $expected = <<<'EOF'
<?php
    /**
     * @param  EngineInterface  $templating
     * @param  string  $format
     * @param  int  $code  An HTTP response status code
     * @param  bool  $debug
     * @param  mixed  &$reference  A parameter passed by reference
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * @param  EngineInterface $templating
     * @param string      $format
     * @param  int  $code       An HTTP response status code
     * @param    bool         $debug
     * @param  mixed    &$reference     A parameter passed by reference
     */

EOF;

        $this->doTest($expected, $input);
    }

    public function testFixPartiallyUntyped(): void
    {
        $expected = <<<'EOF'
<?php
    /**
     * @param  $id
     * @param  $parentId
     * @param  int  $websiteId
     * @param  $position
     * @param  int[][]  $siblings
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * @param      $id
     * @param    $parentId
     * @param int $websiteId
     * @param        $position
     * @param int[][]  $siblings
     */

EOF;

        $this->doTest($expected, $input);
    }

    public function testFixMultiLineDesc(): void
    {
        $expected = <<<'EOF'
<?php
    /**
     * @param  EngineInterface  $templating
     * @param  string  $format
     * @param  int  $code  An HTTP response status code
     *                     See constants
     * @param  bool  $debug
     * @param  bool  $debug  See constants
     *                       See constants
     * @param  mixed  &$reference  A parameter passed by reference
     * @property mixed $foo A foo
     *                      See constants
     * @method static baz($bop) A method that does a thing
     *                          It does it well
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * @param  EngineInterface $templating
     * @param string      $format
     * @param  int  $code       An HTTP response status code
     *                              See constants
     * @param    bool         $debug
     * @param    bool         $debug See constants
     * See constants
     * @param  mixed    &$reference     A parameter passed by reference
     * @property   mixed   $foo     A foo
     *                               See constants
     * @method static   baz($bop)   A method that does a thing
     *                          It does it well
     */

EOF;

        $this->doTest($expected, $input);
    }

    public function testFixMultiLineDescWithThrows(): void
    {
        $expected = <<<'EOF'
<?php
    /**
     * @param  EngineInterface  $templating
     * @param  string  $format
     * @param  int  $code  An HTTP response status code
     *                     See constants
     * @param  bool  $debug
     * @param  bool  $debug  See constants
     *                       See constants
     * @param  mixed  &$reference  A parameter passed by reference
     *
     * @return Foo description foo
     *
     * @throws Foo description foo
     *             description foo
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * @param  EngineInterface $templating
     * @param string      $format
     * @param  int  $code       An HTTP response status code
     *                              See constants
     * @param    bool         $debug
     * @param    bool         $debug See constants
     * See constants
     * @param  mixed    &$reference     A parameter passed by reference
     *
     * @return Foo description foo
     *
     * @throws Foo             description foo
     * description foo
     */

EOF;

        $this->doTest($expected, $input);
    }

    public function testFixWithReturnAndThrows(): void
    {
        $expected = <<<'EOF'
<?php
    /**
     * @param  EngineInterface  $templating
     * @param  mixed  &$reference  A parameter passed by reference
     * @throws Bar description bar
     * @return Foo description foo
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * @param EngineInterface       $templating
     * @param  mixed    &$reference     A parameter passed by reference
     * @throws   Bar description bar
     * @return  Foo     description foo
     */

EOF;

        $this->doTest($expected, $input);
    }

    /**
     * References the issue #55 on github issue
     * https://github.com/FriendsOfPhp/PHP-CS-Fixer/issues/55.
     */
    public function testFixThreeParamsWithReturn(): void
    {
        $expected = <<<'EOF'
<?php
    /**
     * @param  string  $param1
     * @param  bool  $param2  lorem ipsum
     * @param  string  $param3  lorem ipsum
     * @return int lorem ipsum
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * @param   string $param1
     * @param bool   $param2 lorem ipsum
     * @param    string $param3 lorem ipsum
     * @return int lorem ipsum
     */

EOF;

        $this->doTest($expected, $input);
    }

    public function testFixOnlyReturn(): void
    {
        $expected = <<<'EOF'
<?php
    /**
     * @return Foo description foo
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * @return   Foo             description foo
     */

EOF;

        $this->doTest($expected, $input);
    }

    public function testReturnWithDollarThis(): void
    {
        $expected = <<<'EOF'
<?php
    /**
     * @param  Foo  $foo
     * @return $this
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * @param Foo $foo
     * @return $this
     */

EOF;

        $this->doTest($expected, $input);
    }

    public function testCustomAnnotationsStayUntouched(): void
    {
        $expected = <<<'EOF'
<?php
    /**
     * @return string
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * @return string
     *  @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */

EOF;

        $this->doTest($expected, $input);
    }

    public function testCustomAnnotationsStayUntouched2(): void
    {
        $expected = <<<'EOF'
<?php

class X
{
    /**
     * @var Collection<Value>|Value[]
     * @ORM\ManyToMany(
     *  targetEntity="\Dl\Component\DomainModel\Product\Value\AbstractValue",
     *  inversedBy="externalAliases"
     * )
     */
    private $values;
}

EOF;

        $this->doTest($expected);
    }

    public function testFixTestLeftAlign(): void
    {
        $expected = <<<'EOF'
<?php
    /**
     * @param  int  $a
     * @param  string  $b
     *
     * @dataProvider     dataJobCreation
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * @param     int       $a
     * @param     string    $b
     *
     * @dataProvider     dataJobCreation
     */

EOF;

        $this->doTest($expected, $input);
    }

    public function testFixTest(): void
    {
        $expected = <<<'EOF'
<?php
    /**
     * @param  int  $a
     * @param  string|null  $b
     *
     * @dataProvider   dataJobCreation
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * @param     int       $a
     * @param     string|null    $b
     *
     * @dataProvider   dataJobCreation
     */

EOF;

        $this->doTest($expected, $input);
    }

    public function testFixWithVar(): void
    {
        $expected = <<<'EOF'
<?php
    /**
     * @var Type
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * @var   Type
     */

EOF;

        $this->doTest($expected, $input);
    }

    public function testFixWithType(): void
    {
        $expected = <<<'EOF'
<?php
    /**
     * @type Type
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * @type   Type
     */

EOF;

        $this->doTest($expected, $input);
    }

    public function testFixWithVarAndDescription(): void
    {
        $expected = <<<'EOF'
<?php
    /**
     * This is a variable.
     *
     * @var Type
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * This is a variable.
     *
     * @var   Type
     */

EOF;

        $this->doTest($expected, $input);
    }

    public function testFixWithVarAndInlineDescription(): void
    {
        $expected = <<<'EOF'
<?php
    /**
     * @var Type This is a variable.
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * @var   Type   This is a variable.
     */

EOF;

        $this->doTest($expected, $input);
    }

    public function testFixWithTypeAndInlineDescription(): void
    {
        $expected = <<<'EOF'
<?php
    /**
     * @type Type This is a variable.
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * @type   Type   This is a variable.
     */

EOF;

        $this->doTest($expected, $input);
    }

    public function testRetainsNewLineCharacters(): void
    {
        // when we're not modifying a docblock, then line endings shouldn't change
        $this->doTest("<?php\r    /**\r     * @param Example Hello there!\r     */\r");
    }

    public function testMalformedDocBlock(): void
    {
        $input = <<<'EOF'
<?php
    /**
     * @return string
     * */

EOF;

        $this->doTest($input);
    }

    public function testDifferentIndentation(): void
    {
        $expected = <<<'EOF'
<?php
/**
 * @param  int  $limit
 * @param  string  $more
 *
 * @return array
 */

        /**
         * @param  int  $limit
         * @param  string  $more
         *
         * @return array
         */
EOF;

        $input = <<<'EOF'
<?php
/**
 * @param   int       $limit
 * @param   string       $more
 *
 * @return array
 */

        /**
         * @param   int       $limit
         * @param   string       $more
         *
         * @return array
         */
EOF;

        $this->doTest($expected, $input);
    }

    public function testDifferentIndentationLeftAlign(): void
    {
        $expected = <<<'EOF'
<?php
/**
 * @param  int  $limit
 * @param  string  $more
 *
 * @return array
 */

        /**
         * @param  int  $limit
         * @param  string  $more
         *
         * @return array
         */
EOF;

        $input = <<<'EOF'
<?php
/**
 * @param   int       $limit
 * @param   string       $more
 *
 * @return array
 */

        /**
         * @param   int       $limit
         * @param   string       $more
         *
         * @return array
         */
EOF;

        $this->doTest($expected, $input);
    }

    /**
     * @dataProvider provideMessyWhitespacesCases
     */
    public function testMessyWhitespaces(string $expected, string $input, WhitespacesFixerConfig $whitespacesFixerConfig): void
    {
        $this->fixer->setWhitespacesConfig($whitespacesFixerConfig);

        $this->doTest($expected, $input);
    }

    public function provideMessyWhitespacesCases(): array
    {
        return [
            [
                "<?php\r\n    /**\r\n     * @type Type This is a variable.\r\n     */",
                "<?php\r\n    /**\r\n     * @type   Type   This is a variable.\r\n     */",
                new WhitespacesFixerConfig('    ', "\r\n"),
            ],
            [
                "<?php\r\n/**\r\n * @param  int  \$limit\r\n * @param  string  \$more\r\n *\r\n * @return array\r\n */",
                "<?php\r\n/**\r\n * @param   int       \$limit\r\n * @param   string       \$more\r\n *\r\n * @return array\r\n */",
                new WhitespacesFixerConfig("\t", "\r\n"),
            ],
            [
                "<?php\r\n/**\r\n * @param  int  \$limit\r\n * @param  string  \$more\r\n *\r\n * @return array\r\n */",
                "<?php\r\n/**\r\n * @param   int       \$limit\r\n * @param   string       \$more\r\n *\r\n * @return array\r\n */",
                new WhitespacesFixerConfig("\t", "\r\n"),
            ],
            [
                "<?php\n/**\n * @param  int  \$a\n * @param  int  \$b\n *                  ABC\n */",
                "<?php\n/**\n * @param    int \$a\n * @param    int   \$b\n * ABC\n */",
                new WhitespacesFixerConfig('    ', "\n"),
            ],
            [
                "<?php\r\n/**\r\n * @param  int  \$z\r\n * @param  int  \$b\r\n *                  XYZ\r\n */",
                "<?php\r\n/**\r\n * @param    int \$z\r\n * @param    int   \$b\r\n * XYZ\r\n */",
                new WhitespacesFixerConfig('    ', "\r\n"),
            ],
        ];
    }

    public function testCanFixBadFormatted(): void
    {
        $expected = "<?php\n    /**\n     * @var Foo */\n";

        $this->doTest($expected);
    }

    public function testFixUnicode(): void
    {
        $expected = <<<'EOF'
<?php
    /**
     * Method test.
     *
     * @param  int  $foobar  Description
     * @param  string  $foo  Description
     * @param  mixed  $bar  Description word_with_ą
     * @param  int|null  $test  Description
     */
    $a = 1;

    /**
     * @return string
     * @SuppressWarnings(PHPMD.UnusedLocalVariable) word_with_ą
     */
    $b = 1;
EOF;

        $input = <<<'EOF'
<?php
    /**
     * Method test.
     *
     * @param int    $foobar Description
     * @param string $foo    Description
     * @param mixed $bar Description word_with_ą
     * @param int|null $test Description
     */
    $a = 1;

    /**
     * @return string
     *   @SuppressWarnings(PHPMD.UnusedLocalVariable) word_with_ą
     */
    $b = 1;
EOF;

        $this->doTest($expected, $input);
    }

    public function testDoesAlignPropertyByDefault(): void
    {
        $expected = <<<'EOF'
<?php
    /**
     * @param  int  $foobar  Description
     * @return int
     * @throws Exception
     * @var FooBar
     * @type BarFoo
     * @property string $foo Hello World
     */
EOF;

        $input = <<<'EOF'
<?php
    /**
     * @param    int   $foobar   Description
     * @return  int
     * @throws Exception
     * @var       FooBar
     * @type      BarFoo
     * @property     string    $foo   Hello World
     */
EOF;

        $this->doTest($expected, $input);
    }

    public function testAlignsProperty(): void
    {
        $expected = <<<'EOF'
<?php
    /**
     * @param  int  $foobar  Description
     * @return int
     * @throws Exception
     * @var FooBar
     * @type BarFoo
     * @property string $foo Hello World
     */
EOF;

        $input = <<<'EOF'
<?php
    /**
     * @param    int   $foobar   Description
     * @return  int
     * @throws Exception
     * @var       FooBar
     * @type      BarFoo
     * @property     string    $foo   Hello World
     */
EOF;

        $this->doTest($expected, $input);
    }

    public function testDoesAlignMethodByDefault(): void
    {
        $expected = <<<'EOF'
<?php
    /**
     * @param  int  $foobar  Description
     * @return int
     * @throws Exception
     * @var FooBar
     * @type BarFoo
     * @method string foo(string $bar) Hello World
     */
EOF;

        $input = <<<'EOF'
<?php
    /**
     * @param    int   $foobar   Description
     * @return  int
     * @throws Exception
     * @var       FooBar
     * @type      BarFoo
     * @method     string    foo(string $bar)   Hello World
     */
EOF;

        $this->doTest($expected, $input);
    }

    public function testAlignsMethod(): void
    {
        $expected = <<<'EOF'
<?php
    /**
     * @param  int  $foobar  Description
     * @return int
     * @throws Exception
     * @var FooBar
     * @type BarFoo
     * @method int foo(string $bar, string ...$things, int &$baz) Description
     */
EOF;

        $input = <<<'EOF'
<?php
    /**
     * @param    int   $foobar     Description
     * @return  int
     * @throws Exception
     * @var       FooBar
     * @type      BarFoo
     * @method        int    foo(string $bar, string ...$things, int &$baz)   Description
     */
EOF;

        $this->doTest($expected, $input);
    }

    public function testAlignsMethodWithoutParameters(): void
    {
        $expected = <<<'EOF'
<?php
    /**
     * @property string $foo Desc
     * @method int foo() Description
     */
EOF;

        $input = <<<'EOF'
<?php
    /**
     * @property    string   $foo     Desc
     * @method int      foo()          Description
     */
EOF;

        $this->doTest($expected, $input);
    }

    public function testAlignsMethodWithoutParametersLeftAlign(): void
    {
        $expected = <<<'EOF'
<?php
    /**
     * @property string $foo Desc
     * @method int foo() Description
     */
EOF;

        $input = <<<'EOF'
<?php
    /**
     * @property    string   $foo     Desc
     * @method int      foo()          Description
     */
EOF;

        $this->doTest($expected, $input);
    }

    public function testDoesNotFormatMethod(): void
    {
        $input = <<<'EOF'
<?php
    /**
     * @method int foo( string  $bar ) Description
     */
EOF;

        $this->doTest($input);
    }

    public function testAlignsMethodWithoutReturnType(): void
    {
        $expected = <<<'EOF'
<?php
    /**
     * @property string $foo Desc
     * @method int foo() Description
     * @method bar() Descrip
     */
EOF;

        $input = <<<'EOF'
<?php
    /**
     * @property    string   $foo     Desc
     * @method int      foo()          Description
     * @method    bar()   Descrip
     */
EOF;

        $this->doTest($expected, $input);
    }

    public function testAlignsMethodsWithoutReturnType(): void
    {
        $expected = <<<'EOF'
<?php
    /**
     * @method fooBaz() Description
     * @method bar(string $foo) Descrip
     */
EOF;

        $input = <<<'EOF'
<?php
    /**
     * @method         fooBaz()  Description
     * @method    bar(string $foo)   Descrip
     */
EOF;

        $this->doTest($expected, $input);
    }

    public function testDoesNotAlignWithEmptyConfig(): void
    {
        $input = <<<'EOF'
<?php
    /**
     * @param  int  $foobar  Description
     * @return int
     * @throws Exception
     * @var FooBar
     * @type BarFoo
     * @property string $foo Hello World
     * @method int bar() Description
     */
EOF;

        $this->doTest($input);
    }

    /**
     * @dataProvider provideVariadicCases
     */
    public function testVariadicParams(string $expected, string $input): void
    {
        $this->doTest($expected, $input);
    }

    public function provideVariadicCases(): array
    {
        return [
            [
                '<?php
final class Sample
{
    /**
     * @param  int[]  $a  A
     * @param  int  &$b  B
     * @param  array  ...$c  C
     */
    public function sample2($a, &$b, ...$c)
    {
    }
}
',
                '<?php
final class Sample
{
    /**
     * @param int[]       $a  A
     * @param int          &$b B
     * @param array ...$c    C
     */
    public function sample2($a, &$b, ...$c)
    {
    }
}
',
            ],
            [
                '<?php
final class Sample
{
    /**
     * @param  int  $a
     * @param  int  $b
     * @param  array[]  ...$c
     */
    public function sample2($a, $b, ...$c)
    {
    }
}
',
                '<?php
final class Sample
{
    /**
     * @param int       $a
     * @param int    $b
     * @param array[]      ...$c
     */
    public function sample2($a, $b, ...$c)
    {
    }
}
',
            ],
            [
                '<?php
final class Sample
{
    /**
     * @param  int  $a
     * @param  int  $b
     * @param  array[]  ...$c
     */
    public function sample2($a, $b, ...$c)
    {
    }
}
',
                '<?php
final class Sample
{
    /**
     * @param int       $a
     * @param int    $b
     * @param array[]      ...$c
     */
    public function sample2($a, $b, ...$c)
    {
    }
}
',
            ],
            [
                '<?php
/**
 * @property string $myMagicProperty
 * @property-read string $myMagicReadyProperty
 * @property-write string $myMagicWriteProperty
 */
class Foo
{
}
',
                '<?php
/**
 * @property string         $myMagicProperty
 * @property-read  string   $myMagicReadyProperty
 * @property-write  string  $myMagicWriteProperty
 */
class Foo
{
}
',
            ],
        ];
    }

    /**
     * @dataProvider provideInvalidPhpdocCases
     */
    public function testInvalidPhpdocsAreUnchanged(string $input): void
    {
        $this->doTest($input);
    }

    public function provideInvalidPhpdocCases(): array
    {
        return [
            [
                '<?php
/**
 * @ Security("is_granted(\'CANCEL\', giftCard)")
 */
 ',
            ],
            [
                '<?php
/**
 * @ Security("is_granted(\'CANCEL\', giftCard)")
 */
 ',
            ],
            [
                '<?php
/**
 * @ Security("is_granted(\'CANCEL\', giftCard)")
 * @     foo   bar
 *   @ foo
 */
 ',
            ],
        ];
    }

    public function testTypesContainingCallables(): void
    {
        $this->doTest(
            '<?php
            /**
             * @param  callable(Foo): Bar  $x  Description
             * @param  callable(FooFoo): BarBar  $yy  Description
             */
            ',
            '<?php
            /**
             * @param callable(Foo): Bar $x Description
             * @param callable(FooFoo): BarBar $yy Description
             */
            '
        );
    }

    public function testTypesContainingWhitespace(): void
    {
        $this->doTest('<?php
            /**
             * @var int $key
             * @var iterable<int, string> $value
             */
            /**
             * @param  array<int, $this>  $arrayOfIntegers
             * @param  array<string, $this>  $arrayOfStrings
             */
        ');
    }
}
