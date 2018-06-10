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

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;
use PhpCsFixer\WhitespacesFixerConfig;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Phpdoc\PhpdocAlignFixer
 */
final class PhpdocAlignFixerTest extends AbstractFixerTestCase
{
    private static $alignableTags = [
        'param',
        'property',
        'return',
        'throws',
        'type',
        'var',
        'method',
    ];

    public function testFix()
    {
        $this->fixer->configure(['tags' => ['param']]);

        $expected = <<<'EOF'
<?php
    /**
     * @param EngineInterface $templating
     * @param string          $format
     * @param int             $code       An HTTP response status code
     * @param bool            $debug
     * @param mixed           &$reference A parameter passed by reference
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

    public function testFixLeftAlign()
    {
        $this->fixer->configure(['tags' => ['param'], 'align' => 'left']);

        $expected = <<<'EOF'
<?php
    /**
     * @param EngineInterface $templating
     * @param string $format
     * @param int $code An HTTP response status code
     * @param bool $debug
     * @param mixed &$reference A parameter passed by reference
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

    public function testFixPartiallyUntyped()
    {
        $this->fixer->configure(['tags' => ['param']]);

        $expected = <<<'EOF'
<?php
    /**
     * @param         $id
     * @param         $parentId
     * @param int     $websiteId
     * @param         $position
     * @param int[][] $siblings
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

    public function testFixPartiallyUntypedLeftAlign()
    {
        $this->fixer->configure(['tags' => ['param'], 'align' => 'left']);

        $expected = <<<'EOF'
<?php
    /**
     * @param $id
     * @param $parentId
     * @param int $websiteId
     * @param $position
     * @param int[][] $siblings
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

    public function testFixMultiLineDesc()
    {
        $this->fixer->configure(['tags' => ['param', 'property', 'method']]);

        $expected = <<<'EOF'
<?php
    /**
     * @param    EngineInterface $templating
     * @param    string          $format
     * @param    int             $code       An HTTP response status code
     *                                       See constants
     * @param    bool            $debug
     * @param    bool            $debug      See constants
     *                                       See constants
     * @param    mixed           &$reference A parameter passed by reference
     * @property mixed           $foo        A foo
     *                                       See constants
     * @method   static          baz($bop)   A method that does a thing
     *                                       It does it well
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

    public function testFixMultiLineDescLeftAlign()
    {
        $this->fixer->configure(['tags' => ['param', 'property', 'method'], 'align' => 'left']);

        $expected = <<<'EOF'
<?php
    /**
     * @param EngineInterface $templating
     * @param string $format
     * @param int $code An HTTP response status code
     *                  See constants
     * @param bool $debug
     * @param bool $debug See constants
     *                    See constants
     * @param mixed &$reference A parameter passed by reference
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

    public function testFixMultiLineDescWithThrows()
    {
        $this->fixer->configure(['tags' => ['param', 'return', 'throws']]);

        $expected = <<<'EOF'
<?php
    /**
     * @param EngineInterface $templating
     * @param string          $format
     * @param int             $code       An HTTP response status code
     *                                    See constants
     * @param bool            $debug
     * @param bool            $debug      See constants
     *                                    See constants
     * @param mixed           &$reference A parameter passed by reference
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

    public function testFixMultiLineDescWithThrowsLeftAlign()
    {
        $this->fixer->configure(['tags' => ['param', 'return', 'throws'], 'align' => 'left']);

        $expected = <<<'EOF'
<?php
    /**
     * @param EngineInterface $templating
     * @param string $format
     * @param int $code An HTTP response status code
     *                  See constants
     * @param bool $debug
     * @param bool $debug See constants
     *                    See constants
     * @param mixed &$reference A parameter passed by reference
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

    public function testFixWithReturnAndThrows()
    {
        $this->fixer->configure(['tags' => ['param', 'throws', 'return']]);

        $expected = <<<'EOF'
<?php
    /**
     * @param  EngineInterface $templating
     * @param  mixed           &$reference A parameter passed by reference
     * @throws Bar             description bar
     * @return Foo             description foo
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
    public function testFixThreeParamsWithReturn()
    {
        $this->fixer->configure(['tags' => ['param', 'return']]);

        $expected = <<<'EOF'
<?php
    /**
     * @param  string $param1
     * @param  bool   $param2 lorem ipsum
     * @param  string $param3 lorem ipsum
     * @return int    lorem ipsum
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

    public function testFixOnlyReturn()
    {
        $this->fixer->configure(['tags' => ['return']]);

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

    public function testReturnWithDollarThis()
    {
        $this->fixer->configure(['tags' => ['param', 'return']]);

        $expected = <<<'EOF'
<?php
    /**
     * @param  Foo   $foo
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

    public function testCustomAnnotationsStayUntouched()
    {
        $this->fixer->configure(['tags' => ['return']]);

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

    public function testCustomAnnotationsStayUntouched2()
    {
        $this->fixer->configure(['tags' => ['var']]);

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

    public function testFixTestLeftAlign()
    {
        $this->fixer->configure(['tags' => ['param'], 'align' => 'left']);

        $expected = <<<'EOF'
<?php
    /**
     * @param int $a
     * @param string $b
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

    public function testFixTest()
    {
        $this->fixer->configure(['tags' => ['param']]);

        $expected = <<<'EOF'
<?php
    /**
     * @param int         $a
     * @param string|null $b
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

    public function testFixWithVar()
    {
        $this->fixer->configure(['tags' => ['var']]);

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

    public function testFixWithType()
    {
        $this->fixer->configure(['tags' => ['type']]);

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

    public function testFixWithVarAndDescription()
    {
        $this->fixer->configure(['tags' => ['var']]);

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

    public function testFixWithVarAndInlineDescription()
    {
        $this->fixer->configure(['tags' => ['var']]);

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

    public function testFixWithTypeAndInlineDescription()
    {
        $this->fixer->configure(['tags' => ['type']]);

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

    public function testRetainsNewLineCharacters()
    {
        $this->fixer->configure(['tags' => ['param']]);

        // when we're not modifying a docblock, then line endings shouldn't change
        $this->doTest("<?php\r    /**\r     * @param Example Hello there!\r     */\r");
    }

    public function testMalformedDocBlock()
    {
        $this->fixer->configure(['tags' => ['return']]);

        $input = <<<'EOF'
<?php
    /**
     * @return string
     * */

EOF;

        $this->doTest($input);
    }

    public function testDifferentIndentation()
    {
        $this->fixer->configure(['tags' => ['param', 'return']]);

        $expected = <<<'EOF'
<?php
/**
 * @param int    $limit
 * @param string $more
 *
 * @return array
 */

        /**
         * @param int    $limit
         * @param string $more
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

    public function testDifferentIndentationLeftAlign()
    {
        $this->fixer->configure(['tags' => ['param', 'return'], 'align' => 'left']);

        $expected = <<<'EOF'
<?php
/**
 * @param int $limit
 * @param string $more
 *
 * @return array
 */

        /**
         * @param int $limit
         * @param string $more
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
     * @param array                  $config
     * @param string                 $expected
     * @param string                 $input
     * @param WhitespacesFixerConfig $whitespacesFixerConfig
     *
     * @dataProvider provideMessyWhitespacesCases
     */
    public function testMessyWhitespaces(array $config, $expected, $input, WhitespacesFixerConfig $whitespacesFixerConfig)
    {
        $this->fixer->configure($config);
        $this->fixer->setWhitespacesConfig($whitespacesFixerConfig);

        $this->doTest($expected, $input);
    }

    public function provideMessyWhitespacesCases()
    {
        return [
            [
                ['tags' => ['type']],
                "<?php\r\n\t/**\r\n\t * @type Type This is a variable.\r\n\t */",
                "<?php\r\n\t/**\r\n\t * @type   Type   This is a variable.\r\n\t */",
                new WhitespacesFixerConfig("\t", "\r\n"),
            ],
            [
                ['tags' => ['param', 'return']],
                "<?php\r\n/**\r\n * @param int    \$limit\r\n * @param string \$more\r\n *\r\n * @return array\r\n */",
                "<?php\r\n/**\r\n * @param   int       \$limit\r\n * @param   string       \$more\r\n *\r\n * @return array\r\n */",
                new WhitespacesFixerConfig("\t", "\r\n"),
            ],
            [
                [],
                "<?php\r\n/**\r\n * @param int    \$limit\r\n * @param string \$more\r\n *\r\n * @return array\r\n */",
                "<?php\r\n/**\r\n * @param   int       \$limit\r\n * @param   string       \$more\r\n *\r\n * @return array\r\n */",
                new WhitespacesFixerConfig("\t", "\r\n"),
            ],
            [
                [],
                "<?php\n/**\n * @param int \$a\n * @param int \$b\n *               ABC\n */",
                "<?php\n/**\n * @param    int \$a\n * @param    int   \$b\n * ABC\n */",
                new WhitespacesFixerConfig('    ', "\n"),
            ],
            [
                [],
                "<?php\r\n/**\r\n * @param int \$z\r\n * @param int \$b\r\n *               XYZ\r\n */",
                "<?php\r\n/**\r\n * @param    int \$z\r\n * @param    int   \$b\r\n * XYZ\r\n */",
                new WhitespacesFixerConfig('    ', "\r\n"),
            ],
        ];
    }

    public function testCanFixBadFormatted()
    {
        $this->fixer->configure(['tags' => ['var']]);

        $expected = "<?php\n    /**\n     * @var Foo */\n";

        $this->doTest($expected);
    }

    public function testFixUnicode()
    {
        $this->fixer->configure(['tags' => ['param', 'return']]);

        $expected = <<<'EOF'
<?php
    /**
     * Method test.
     *
     * @param int      $foobar Description
     * @param string   $foo    Description
     * @param mixed    $bar    Description word_with_ą
     * @param int|null $test   Description
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

    public function testDoesNotAlignPropertyByDefault()
    {
        $expected = <<<'EOF'
<?php
    /**
     * @param  int       $foobar Description
     * @return int
     * @throws Exception
     * @var    FooBar
     * @type   BarFoo
     * @property     string    $foo   Hello World
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

    public function testAlignsProperty()
    {
        $this->fixer->configure(['tags' => ['param', 'property', 'return', 'throws', 'type', 'var']]);

        $expected = <<<'EOF'
<?php
    /**
     * @param    int       $foobar Description
     * @return   int
     * @throws   Exception
     * @var      FooBar
     * @type     BarFoo
     * @property string    $foo    Hello World
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

    public function testDoesNotAlignMethodByDefault()
    {
        $expected = <<<'EOF'
<?php
    /**
     * @param  int       $foobar Description
     * @return int
     * @throws Exception
     * @var    FooBar
     * @type   BarFoo
     * @method     string    foo(string $bar)   Hello World
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

    public function testAlignsMethod()
    {
        $this->fixer->configure(['tags' => ['param', 'method', 'return', 'throws', 'type', 'var']]);

        $expected = <<<'EOF'
<?php
    /**
     * @param  int       $foobar                                        Description
     * @return int
     * @throws Exception
     * @var    FooBar
     * @type   BarFoo
     * @method int       foo(string $bar, string ...$things, int &$baz) Description
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

    public function testAlignsMethodWithoutParameters()
    {
        $this->fixer->configure(['tags' => ['method', 'property']]);

        $expected = <<<'EOF'
<?php
    /**
     * @property string $foo  Desc
     * @method   int    foo() Description
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

    public function testAlignsMethodWithoutParametersLeftAlign()
    {
        $this->fixer->configure(['tags' => ['method', 'property'], 'align' => 'left']);

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

    public function testDoesNotFormatMethod()
    {
        $this->fixer->configure(['tags' => ['method']]);

        $input = <<<'EOF'
<?php
    /**
     * @method int foo( string  $bar ) Description
     */
EOF;

        $this->doTest($input);
    }

    public function testAlignsMethodWithoutReturnType()
    {
        $this->fixer->configure(['tags' => ['method', 'property']]);

        $expected = <<<'EOF'
<?php
    /**
     * @property string $foo  Desc
     * @method   int    foo() Description
     * @method          bar() Descrip
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

    public function testAlignsMethodsWithoutReturnType()
    {
        $this->fixer->configure(['tags' => ['method']]);

        $expected = <<<'EOF'
<?php
    /**
     * @method fooBaz()         Description
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

    public function testDoesNotAlignWithEmptyConfig()
    {
        $this->fixer->configure(['tags' => []]);

        $input = <<<'EOF'
<?php
    /**
     * @param    int   $foobar   Description
     * @return  int
     * @throws Exception
     * @var       FooBar
     * @type      BarFoo
     * @property     string    $foo   Hello World
     * @method    int    bar() Description
     */
EOF;

        $this->doTest($input);
    }

    /**
     * @param array  $config
     * @param string $expected
     * @param string $input
     *
     * @dataProvider provideVariadicCases
     */
    public function testVariadicParams(array $config, $expected, $input)
    {
        $this->fixer->configure($config);

        $this->doTest($expected, $input);
    }

    public function provideVariadicCases()
    {
        return [
            [
                ['tags' => ['param']],
                '<?php
final class Sample
{
    /**
     * @param int[] $a    A
     * @param int   &$b   B
     * @param array ...$c C
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
                ['tags' => ['param']],
                '<?php
final class Sample
{
    /**
     * @param int     $a
     * @param int     $b
     * @param array[] ...$c
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
                ['tags' => ['param'], 'align' => 'left'],
                '<?php
final class Sample
{
    /**
     * @param int $a
     * @param int $b
     * @param array[] ...$c
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
        ];
    }

    /**
     * @param array  $config
     * @param string $input
     *
     * @dataProvider provideInvalidPhpdocCases
     */
    public function testInvalidPhpdocsAreUnchanged(array $config, $input)
    {
        $this->fixer->configure($config);

        $this->doTest($input);
    }

    public function provideInvalidPhpdocCases()
    {
        return [
            [
                ['tags' => ['param', 'return', 'throws', 'type', 'var']],
                '<?php
/**
 * @ Security("is_granted(\'CANCEL\', giftCard)")
 */
 ',
            ],
            [
                ['tags' => ['param', 'return', 'throws', 'type', 'var', 'method']],
                '<?php
/**
 * @ Security("is_granted(\'CANCEL\', giftCard)")
 */
 ',
            ],
            [
                ['tags' => ['param', 'return', 'throws', 'type', 'var']],
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

    public function testDescriptionAlignTagMethod()
    {
        $this->fixer->configure([
            'tags' => ['method'],
            'description_align' => 'tag',
        ]);

        $expected = <<<'EOF'
<?php
    /**
     * @method Type method() Desc
     * ription
     */
EOF;

        $input = <<<'EOF'
<?php
    /**
     * @method Type method() Desc
     *     ription
     */
EOF;

        $this->doTest($expected, $input);
    }

    public function testDescriptionAlignTagParam()
    {
        $this->fixer->configure(['description_align' => 'tag']);

        $expected = <<<'EOF'
<?php
    /**
     * @param int $a Desc
     * ription
     */
EOF;

        $input = <<<'EOF'
<?php
    /**
     * @param int $a Desc
     *    ription
     */
EOF;

        $this->doTest($expected, $input);
    }

    public function testDescriptionAlignTagReturn()
    {
        $this->fixer->configure(['description_align' => 'tag']);

        $expected = <<<'EOF'
<?php
    /**
     * @return string Desc
     * ription
     */
EOF;

        $input = <<<'EOF'
<?php
    /**
     * @return string Desc
     *      ription
     */
EOF;

        $this->doTest($expected, $input);
    }

    public function testDescriptionAlignTag()
    {
        $this->fixer->configure([
            'tags' => static::$alignableTags,
            'description_align' => 'tag',
        ]);

        $expected = <<<'EOF'
<?php
    /**
     * @param    int       $foobar          Descrip
     * tion
     * @property mixed     $barfoo          Desc
     * ription
     * @return   int       Returns
     * an int
     * @throws   Exception On
     * error
     * @var      FooBar    Foo
     * Foo
     * @type     BarFoo    Bar
     * Bar
     * @method   int       foo(string $bar) Method
     * with type
     * @method             bar(string $foo) Method
     * without type
     */
EOF;

        $input = <<<'EOF'
<?php
    /**
     * @param    int   $foobar     Descrip
     *   tion
     * @property mixed $barfoo Desc
     *  ription
     * @return  int  Returns
     *   an int
     * @throws Exception   On
     *  error
     * @var       FooBar Foo
     *     Foo
     * @type      BarFoo Bar
     *  Bar
     * @method        int    foo(string $bar)   Method
     *  with type
     * @method            bar(string $foo)   Method
     *  without type
     */
EOF;

        $this->doTest($expected, $input);
    }

    public function testDescriptionAlignHintMethod()
    {
        $this->fixer->configure([
            'tags' => ['method'],
            'description_align' => 'hint',
        ]);

        $expected = <<<'EOF'
<?php
    /**
     * @method Type method() Desc
     *         ription
     */
EOF;

        $input = <<<'EOF'
<?php
    /**
     * @method Type method() Desc
     *     ription
     */
EOF;

        $this->doTest($expected, $input);
    }

    /**
     * @param string $tag
     */
    public function testDescriptionAlignHintParam()
    {
        $this->fixer->configure(['description_align' => 'hint']);

        $expected = <<<'EOF'
<?php
    /**
     * @param int $a Desc
     *        ription
     */
EOF;

        $input = <<<'EOF'
<?php
    /**
     * @param int $a Desc
     *    ription
     */
EOF;

        $this->doTest($expected, $input);
    }

    public function testDescriptionAlignHintThrows()
    {
        $this->fixer->configure(['description_align' => 'hint']);

        $expected = <<<'EOF'
<?php
    /**
     * @throws Exception Desc
     *         ription
     */
EOF;

        $input = <<<'EOF'
<?php
    /**
     * @throws Exception Desc
     *      ription
     */
EOF;

        $this->doTest($expected, $input);
    }

    public function testDescriptionAlignHint()
    {
        $this->fixer->configure([
            'tags' => static::$alignableTags,
            'description_align' => 'hint',
        ]);

        $expected = <<<'EOF'
<?php
    /**
     * @param    int       $foobar          Descrip
     *           tion
     * @property mixed     $barfoo          Desc
     *           ription
     * @return   int       Returns
     *           an int
     * @throws   Exception On
     *           error
     * @var      FooBar    Foo
     *           Foo
     * @type     BarFoo    Bar
     *           Bar
     * @method   int       foo(string $bar) Method
     *           with type
     * @method             bar(string $foo) Method
     *           without type
     */
EOF;

        $input = <<<'EOF'
<?php
    /**
     * @param    int   $foobar     Descrip
     *   tion
     * @property mixed $barfoo Desc
     *  ription
     * @return  int  Returns
     *   an int
     * @throws Exception   On
     *  error
     * @var       FooBar Foo
     *     Foo
     * @type      BarFoo Bar
     *  Bar
     * @method        int    foo(string $bar)   Method
     *  with type
     * @method            bar(string $foo)   Method
     *  without type
     */
EOF;

        $this->doTest($expected, $input);
    }

    public function testDescriptionAlignNameMethod()
    {
        $this->fixer->configure([
            'tags' => ['method'],
            'description_align' => 'name',
        ]);

        $expected = <<<'EOF'
<?php
    /**
     * @method Type method() Desc
     *              ription
     */
EOF;

        $input = <<<'EOF'
<?php
    /**
     * @method Type method() Desc
     *     ription
     */
EOF;

        $this->doTest($expected, $input);
    }

    public function testDescriptionAlignNameParam()
    {
        $this->fixer->configure(['description_align' => 'name']);

        $expected = <<<'EOF'
<?php
    /**
     * @param int $a Desc
     *            ription
     */
EOF;

        $input = <<<'EOF'
<?php
    /**
     * @param int $a Desc
     *    ription
     */
EOF;

        $this->doTest($expected, $input);
    }

    public function testDescriptionAlignNameReturn()
    {
        $this->fixer->configure(['description_align' => 'name']);

        $expected = <<<'EOF'
<?php
    /**
     * @return string Desc
     *                ription
     */
EOF;

        $input = <<<'EOF'
<?php
    /**
     * @return string Desc
     *      ription
     */
EOF;

        $this->doTest($expected, $input);
    }

    public function testDescriptionAlignName()
    {
        $this->fixer->configure([
            'tags' => static::$alignableTags,
            'description_align' => 'name',
        ]);

        $expected = <<<'EOF'
<?php
    /**
     * @param    int       $foobar          Descrip
     *                     tion
     * @property mixed     $barfoo          Desc
     *                     ription
     * @return   int       Returns
     *                     an int
     * @throws   Exception On
     *                     error
     * @var      FooBar    Foo
     *                     Foo
     * @type     BarFoo    Bar
     *                     Bar
     * @method   int       foo(string $bar) Method
     *                     with type
     * @method             bar(string $foo) Method
     *                     without type
     */
EOF;

        $input = <<<'EOF'
<?php
    /**
     * @param    int   $foobar     Descrip
     *   tion
     * @property mixed $barfoo Desc
     *  ription
     * @return  int  Returns
     *   an int
     * @throws Exception   On
     *  error
     * @var       FooBar Foo
     *     Foo
     * @type      BarFoo Bar
     *  Bar
     * @method        int    foo(string $bar)   Method
     *  with type
     * @method            bar(string $foo)   Method
     *  without type
     */
EOF;

        $this->doTest($expected, $input);
    }

    public function testDescriptionAlignDescriptionMethod()
    {
        $this->fixer->configure([
            'tags' => ['method'],
            'description_align' => 'description',
        ]);

        $expected = <<<'EOF'
<?php
    /**
     * @method Type method() Desc
     *                       ription
     */
EOF;

        $input = <<<'EOF'
<?php
    /**
     * @method Type method() Desc
     *     ription
     */
EOF;

        $this->doTest($expected, $input);
    }

    public function testDescriptionAlignDescriptionParam()
    {
        $this->fixer->configure(['description_align' => 'description']);

        $expected = <<<'EOF'
<?php
    /**
     * @param int $a Desc
     *               ription
     */
EOF;

        $input = <<<'EOF'
<?php
    /**
     * @param int $a Desc
     *    ription
     */
EOF;

        $this->doTest($expected, $input);
    }

    public function testDescriptionAlignDescriptionThrows()
    {
        $this->fixer->configure(['description_align' => 'description']);

        $expected = <<<'EOF'
<?php
    /**
     * @throws Exception Desc
     *                   ription
     */
EOF;

        $input = <<<'EOF'
<?php
    /**
     * @throws Exception Desc
     *      ription
     */
EOF;

        $this->doTest($expected, $input);
    }

    public function testDescriptionAlignDescription()
    {
        $this->fixer->configure([
            'tags' => static::$alignableTags,
            'description_align' => 'description',
        ]);

        $expected = <<<'EOF'
<?php
    /**
     * @param    int       $foobar          Descrip
     *                                      tion
     * @property mixed     $barfoo          Desc
     *                                      ription
     * @return   int       Returns
     *                                      an int
     * @throws   Exception On
     *                                      error
     * @var      FooBar    Foo
     *                                      Foo
     * @type     BarFoo    Bar
     *                                      Bar
     * @method   int       foo(string $bar) Method
     *                                      with type
     * @method             bar(string $foo) Method
     *                                      without type
     */
EOF;

        $input = <<<'EOF'
<?php
    /**
     * @param    int   $foobar     Descrip
     *   tion
     * @property mixed $barfoo Desc
     *  ription
     * @return  int  Returns
     *   an int
     * @throws Exception   On
     *  error
     * @var       FooBar Foo
     *     Foo
     * @type      BarFoo Bar
     *  Bar
     * @method        int    foo(string $bar)   Method
     *  with type
     * @method            bar(string $foo)   Method
     *  without type
     */
EOF;

        $this->doTest($expected, $input);
    }

    public function testDescriptionAlignDescriptionX()
    {
        $this->fixer->configure([]);

        $expected = <<<'EOF'
<?php
    /**
     * @param  int $foobar Desc
     *                     ription
     * @return int Desc
     *                     ription
     */
EOF;

        $input = <<<'EOF'
<?php
    /**
     * @param int $foobar Desc
     * ription
     * @return int Desc
     * ription
     */
EOF;

        $this->doTest($expected, $input);
    }

    public function testDescriptionExtraIntendMethod()
    {
        $this->fixer->configure([
            'tags' => ['method'],
            'description_extra_indent' => 2,
        ]);

        $expected = <<<'EOF'
<?php
    /**
     * @method Type method() Desc
     *                         ription
     */
EOF;

        $input = <<<'EOF'
<?php
    /**
     * @method Type method() Desc
     *     ription
     */
EOF;

        $this->doTest($expected, $input);
    }

    public function testDescriptionExtraIndentParam()
    {
        $this->fixer->configure(['description_extra_indent' => 3]);

        $expected = <<<'EOF'
<?php
    /**
     * @param int $a Desc
     *                  ription
     */
EOF;

        $input = <<<'EOF'
<?php
    /**
     * @param int $a Desc
     *    ription
     */
EOF;

        $this->doTest($expected, $input);
    }

    public function testDescriptionExtraIndentReturn()
    {
        $this->fixer->configure(['description_extra_indent' => 4]);

        $expected = <<<'EOF'
<?php
    /**
     * @return string Desc
     *                    ription
     */
EOF;

        $input = <<<'EOF'
<?php
    /**
     * @return string Desc
     *      ription
     */
EOF;

        $this->doTest($expected, $input);
    }

    public function testDescriptionExtraIndent()
    {
        $this->fixer->configure([
            'tags' => static::$alignableTags,
            'description_extra_indent' => 1,
        ]);

        $expected = <<<'EOF'
<?php
    /**
     * @param    int       $foobar          Descrip
     *                                       tion
     * @property mixed     $barfoo          Desc
     *                                       ription
     * @return   int       Returns
     *                                       an int
     * @throws   Exception On
     *                                       error
     * @var      FooBar    Foo
     *                                       Foo
     * @type     BarFoo    Bar
     *                                       Bar
     * @method   int       foo(string $bar) Method
     *                                       with type
     * @method             bar(string $foo) Method
     *                                       without type
     */
EOF;

        $input = <<<'EOF'
<?php
    /**
     * @param    int   $foobar     Descrip
     *   tion
     * @property mixed $barfoo Desc
     *  ription
     * @return  int  Returns
     *   an int
     * @throws Exception   On
     *  error
     * @var       FooBar Foo
     *     Foo
     * @type      BarFoo Bar
     *  Bar
     * @method        int    foo(string $bar)   Method
     *  with type
     * @method            bar(string $foo)   Method
     *  without type
     */
EOF;

        $this->doTest($expected, $input);
    }

    public function testDescriptionExtraIndentNegative()
    {
        $this->fixer->configure([
            'tags' => static::$alignableTags,
            'description_extra_indent' => -1,
        ]);

        $expected = <<<'EOF'
<?php
    /**
     * @param    int       $foobar          Descrip
     *                                     tion
     * @property mixed     $barfoo          Desc
     *                                     ription
     * @return   int       Returns
     *                                     an int
     * @throws   Exception On
     *                                     error
     * @var      FooBar    Foo
     *                                     Foo
     * @type     BarFoo    Bar
     *                                     Bar
     * @method   int       foo(string $bar) Method
     *                                     with type
     * @method             bar(string $foo) Method
     *                                     without type
     */
EOF;

        $input = <<<'EOF'
<?php
    /**
     * @param    int   $foobar     Descrip
     *   tion
     * @property mixed $barfoo Desc
     *  ription
     * @return  int  Returns
     *   an int
     * @throws Exception   On
     *  error
     * @var       FooBar Foo
     *     Foo
     * @type      BarFoo Bar
     *  Bar
     * @method        int    foo(string $bar)   Method
     *  with type
     * @method            bar(string $foo)   Method
     *  without type
     */
EOF;

        $this->doTest($expected, $input);
    }

    public function testDescriptionExtraIndentAlignTagParam()
    {
        $this->fixer->configure([
            'description_align' => 'tag',
            'description_extra_indent' => 2,
        ]);

        $expected = <<<'EOF'
<?php
    /**
     * @param int $a Desc
     *   ription
     */
EOF;

        $input = <<<'EOF'
<?php
    /**
     * @param int $a Desc
     *    ription
     */
EOF;

        $this->doTest($expected, $input);
    }

    public function testDescriptionExtraIndentAlignHintParam()
    {
        $this->fixer->configure([
            'description_align' => 'hint',
            'description_extra_indent' => 2,
        ]);

        $expected = <<<'EOF'
<?php
    /**
     * @param int $a Desc
     *          ription
     */
EOF;

        $input = <<<'EOF'
<?php
    /**
     * @param int $a Desc
     *    ription
     */
EOF;

        $this->doTest($expected, $input);
    }

    public function testDescriptionExtraIndentAlignNameParam()
    {
        $this->fixer->configure([
            'description_align' => 'name',
            'description_extra_indent' => 2,
        ]);

        $expected = <<<'EOF'
<?php
    /**
     * @param int $a Desc
     *              ription
     */
EOF;

        $input = <<<'EOF'
<?php
    /**
     * @param int $a Desc
     *    ription
     */
EOF;

        $this->doTest($expected, $input);
    }

    public function testDescriptionAlignTagWithLeftAlign()
    {
        $this->fixer->configure([
            'tags' => ['param', 'property', 'method'],
            'align' => 'left',
            'description_align' => 'tag',
        ]);

        $expected = <<<'EOF'
<?php
    /**
     * @param EngineInterface $templating
     * @param string $format
     * @param int $code An HTTP response status code
     * See constants
     * @param bool $debug
     * @param bool $debug See constants
     * See constants
     * @param mixed &$reference A parameter passed by reference
     * @property mixed $foo A foo
     * See constants
     * @method static baz($bop) A method that does a thing
     * It does it well
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

    public function testDescriptionAlignHintWithLeftAlign()
    {
        $this->fixer->configure([
            'tags' => ['param', 'property', 'method'],
            'align' => 'left',
            'description_align' => 'hint',
        ]);

        $expected = <<<'EOF'
<?php
    /**
     * @param EngineInterface $templating
     * @param string $format
     * @param int $code An HTTP response status code
     *        See constants
     * @param bool $debug
     * @param bool $debug See constants
     *        See constants
     * @param mixed &$reference A parameter passed by reference
     * @property mixed $foo A foo
     *           See constants
     * @method static baz($bop) A method that does a thing
     *         It does it well
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

    public function testDescriptionAlignNameWithLeftAlign()
    {
        $this->fixer->configure([
            'tags' => ['param', 'property', 'method'],
            'align' => 'left',
            'description_align' => 'name',
        ]);

        $expected = <<<'EOF'
<?php
    /**
     * @param EngineInterface $templating
     * @param string $format
     * @param int $code An HTTP response status code
     *            See constants
     * @param bool $debug
     * @param bool $debug See constants
     *             See constants
     * @param mixed &$reference A parameter passed by reference
     * @property mixed $foo A foo
     *                 See constants
     * @method static baz($bop) A method that does a thing
     *                It does it well
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

    public function testDescriptionExtraIndentInvalid()
    {
        $this->expectException(\PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException::class);
        $this->expectExceptionMessage('[phpdoc_align] Invalid configuration: The option "description_extra_indent" with value "invalid" is expected to be of type "int", but is of type "string".');
        $this->fixer->configure(['description_extra_indent' => 'invalid']);
    }
}
