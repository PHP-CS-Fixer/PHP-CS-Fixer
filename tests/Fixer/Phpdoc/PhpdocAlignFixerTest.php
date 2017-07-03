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
    public function testFix()
    {
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

    public function testFixMultiLineDesc()
    {
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
     */

EOF;

        $this->doTest($expected, $input);
    }

    public function testFixMultiLineDescWithThrows()
    {
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

    public function testFixWithReturnAndThrows()
    {
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

    public function testFixWithVar()
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

    public function testFixWithType()
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

    public function testFixWithVarAndDescription()
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

    public function testFixWithVarAndInlineDescription()
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

    public function testFixWithTypeAndInlineDescription()
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

    public function testRetainsNewLineCharacters()
    {
        // when we're not modifying a docblock, then line endings shouldn't change
        $this->doTest("<?php\r    /**\r     * @param Example Hello there!\r     */\r");
    }

    public function testMalformedDocBlock()
    {
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
        return array(
            array(
                "<?php\r\n\t/**\r\n\t * @type Type This is a variable.\r\n\t */",
                "<?php\r\n\t/**\r\n\t * @type   Type   This is a variable.\r\n\t */",
            ),
            array(
                "<?php\r\n/**\r\n * @param int    \$limit\r\n * @param string \$more\r\n *\r\n * @return array\r\n */",
                "<?php\r\n/**\r\n * @param   int       \$limit\r\n * @param   string       \$more\r\n *\r\n * @return array\r\n */",
            ),
        );
    }

    public function testCanFixBadFormatted()
    {
        $expected = "<?php\n    /**\n     * @var Foo */\n";

        $this->doTest($expected);
    }

    public function testFixUnicode()
    {
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

    /**
     * @param string $expected
     * @param string $input
     *
     * @requires PHP 5.6
     * @dataProvider provideVariadicCases
     */
    public function testVariadicParams($expected, $input)
    {
        $this->doTest($expected, $input);
    }

    public function provideVariadicCases()
    {
        return array(
            array(
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
            ),
                        array(
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
            ),
        );
    }
}
