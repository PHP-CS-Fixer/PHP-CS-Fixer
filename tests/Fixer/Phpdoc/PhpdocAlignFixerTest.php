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

use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\Fixer\Phpdoc\PhpdocAlignFixer;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;
use PhpCsFixer\WhitespacesFixerConfig;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Phpdoc\PhpdocAlignFixer
 */
final class PhpdocAlignFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     *
     * @param array{align: string, tags: array<string>, spacing: array<string,int>} $configuration
     */
    public function testFix(
        array $configuration,
        string $expected,
        ?string $input = null,
        ?WhitespacesFixerConfig $whitespacesFixerConfig = null
    ): void {
        $this->fixer->configure($configuration);
        if (null !== $whitespacesFixerConfig) {
            $this->fixer->setWhitespacesConfig($whitespacesFixerConfig);
        }

        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): iterable
    {
        yield 'none, one and four spaces between type and variable' => [
            ['tags' => ['param']],
            '<?php
                    /**
                     * @param int $a
                     * @param int $b
                     * @param int $c
                     */',
            '<?php
                    /**
                     * @param int    $a
                     * @param int $b
                     * @param int$c
                     */',
        ];

        yield 'aligning params' => [
            ['tags' => ['param']],
            '<?php
    /**
     * @param EngineInterface $templating
     * @param string          $format
     * @param int             $code       An HTTP response status code
     * @param bool            $debug
     * @param mixed           &$reference A parameter passed by reference
     */

',
            '<?php
    /**
     * @param  EngineInterface $templating
     * @param string      $format
     * @param  int  $code       An HTTP response status code
     * @param    bool         $debug
     * @param  mixed    &$reference     A parameter passed by reference
     */

',
        ];

        yield 'left align' => [
            ['tags' => ['param'], 'align' => PhpdocAlignFixer::ALIGN_LEFT],
            '<?php
    /**
     * @param EngineInterface $templating
     * @param string $format
     * @param int $code An HTTP response status code
     * @param bool $debug
     * @param mixed &$reference A parameter passed by reference
     */

',
            '<?php
    /**
     * @param  EngineInterface $templating
     * @param string      $format
     * @param  int  $code       An HTTP response status code
     * @param    bool         $debug
     * @param  mixed    &$reference     A parameter passed by reference
     */

',
        ];

        yield 'partially untyped' => [
            ['tags' => ['param']],
            '<?php
    /**
     * @param         $id
     * @param         $parentId
     * @param int     $websiteId
     * @param         $position
     * @param int[][] $siblings
     */

',
            '<?php
    /**
     * @param      $id
     * @param    $parentId
     * @param int $websiteId
     * @param        $position
     * @param int[][]  $siblings
     */

',
        ];

        yield 'partially untyped left align' => [
            ['tags' => ['param'], 'align' => PhpdocAlignFixer::ALIGN_LEFT],
            '<?php
    /**
     * @param $id
     * @param $parentId
     * @param int $websiteId
     * @param $position
     * @param int[][] $siblings
     */

',
            '<?php
    /**
     * @param      $id
     * @param    $parentId
     * @param int $websiteId
     * @param        $position
     * @param int[][]  $siblings
     */

',
        ];

        yield 'multiline description' => [
            ['tags' => ['param', 'property', 'method']],
            '<?php
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

',
            '<?php
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

',
        ];

        yield 'multiline description left align' => [
            ['tags' => ['param', 'property', 'method'], 'align' => PhpdocAlignFixer::ALIGN_LEFT],
            '<?php
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

',
            '<?php
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

',
        ];

        yield 'multiline description with throws' => [
            ['tags' => ['param', 'return', 'throws']],
            '<?php
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

',
            '<?php
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

',
        ];

        yield 'multiline description with throws left align' => [
            ['tags' => ['param', 'return', 'throws'], 'align' => PhpdocAlignFixer::ALIGN_LEFT],
            '<?php
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

',
            '<?php
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

',
        ];

        yield 'return and throws' => [
            ['tags' => ['param', 'throws', 'return']],
            '<?php
    /**
     * @param  EngineInterface $templating
     * @param  mixed           &$reference A parameter passed by reference
     *                                     Multiline description
     * @throws Bar             description bar
     * @return Foo             description foo
     *                         multiline description
     */

',
            '<?php
    /**
     * @param EngineInterface       $templating
     * @param  mixed    &$reference     A parameter passed by reference
     *                                  Multiline description
     * @throws   Bar description bar
     * @return  Foo     description foo
     *                  multiline description
     */

',
        ];

        // https://github.com/FriendsOfPhp/PHP-CS-Fixer/issues/55
        yield 'three params with return' => [
            ['tags' => ['param', 'return']],
            '<?php
    /**
     * @param  string $param1
     * @param  bool   $param2 lorem ipsum
     * @param  string $param3 lorem ipsum
     * @return int    lorem ipsum
     */

',
            '<?php
    /**
     * @param   string $param1
     * @param bool   $param2 lorem ipsum
     * @param    string $param3 lorem ipsum
     * @return int lorem ipsum
     */

',
        ];

        yield 'only return' => [
            ['tags' => ['return']],
            '<?php
    /**
     * @return Foo description foo
     */

',
            '<?php
    /**
     * @return   Foo             description foo
     */

',
        ];

        yield 'return with $this' => [
            ['tags' => ['param', 'return']],
            '<?php
    /**
     * @param  Foo   $foo
     * @return $this
     */

',
            '<?php
    /**
     * @param Foo $foo
     * @return $this
     */

',
        ];

        yield 'custom annotations stay untouched' => [
            ['tags' => ['return']],
            '<?php
    /**
     * @return string
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */

',
            '<?php
    /**
     * @return string
     *  @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */

',
        ];

        yield 'custom annotations stay untouched 2' => [
            ['tags' => ['var']],
            '<?php

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

',
        ];

        yield 'left align 2' => [
            ['tags' => ['param'], 'align' => PhpdocAlignFixer::ALIGN_LEFT],
            '<?php
    /**
     * @param int $a
     * @param string $b
     *
     * @dataProvider     dataJobCreation
     */

',
            '<?php
    /**
     * @param     int       $a
     * @param     string    $b
     *
     * @dataProvider     dataJobCreation
     */

',
        ];

        yield 'params and data provider' => [
            ['tags' => ['param']],
            '<?php
    /**
     * @param int         $a
     * @param string|null $b
     *
     * @dataProvider   dataJobCreation
     */

',
            '<?php
    /**
     * @param     int       $a
     * @param     string|null    $b
     *
     * @dataProvider   dataJobCreation
     */

',
        ];

        yield 'var' => [
            ['tags' => ['var']],
            '<?php
    /**
     * @var Type
     */

',
            '<?php
    /**
     * @var   Type
     */

',
        ];

        yield 'type' => [
            ['tags' => ['type']],
            '<?php
    /**
     * @type Type
     */

',
            '<?php
    /**
     * @type   Type
     */

',
        ];

        yield 'var and description' => [
            ['tags' => ['var']],
            '<?php
    /**
     * This is a variable.
     *
     * @var Type
     */

',
            '<?php
    /**
     * This is a variable.
     *
     * @var   Type
     */

',
        ];

        yield 'var and inline description' => [
            ['tags' => ['var']],
            '<?php
    /**
     * @var Type This is a variable.
     */

',
            '<?php
    /**
     * @var   Type   This is a variable.
     */

',
        ];

        yield 'type and inline description' => [
            ['tags' => ['type']],
            '<?php
    /**
     * @type Type This is a variable.
     */

',
            '<?php
    /**
     * @type   Type   This is a variable.
     */

',
        ];

        yield 'when we are not modifying a docblock, then line endings should not change' => [
            ['tags' => ['param']],
            "<?php\r    /**\r     * @param Example Hello there!\r     */\r",
        ];

        yield 'malformed doc block' => [
            ['tags' => ['return']],
            '<?php
    /**
     * @return string
     * */

',
        ];

        yield 'different indentation' => [
            ['tags' => ['param', 'return']],
            '<?php
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
',
            '<?php
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
',
        ];

        yield 'different indentation left align' => [
            ['tags' => ['param', 'return'], 'align' => PhpdocAlignFixer::ALIGN_LEFT],
            '<?php
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
',
            '<?php
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
',
        ];

        yield 'messy whitespaces 1' => [
            ['tags' => ['type']],
            "<?php\r\n\t/**\r\n\t * @type Type This is a variable.\r\n\t */",
            "<?php\r\n\t/**\r\n\t * @type   Type   This is a variable.\r\n\t */",
            new WhitespacesFixerConfig("\t", "\r\n"),
        ];

        yield 'messy whitespaces 2' => [
            ['tags' => ['param', 'return']],
            "<?php\r\n/**\r\n * @param int    \$limit\r\n * @param string \$more\r\n *\r\n * @return array\r\n */",
            "<?php\r\n/**\r\n * @param   int       \$limit\r\n * @param   string       \$more\r\n *\r\n * @return array\r\n */",
            new WhitespacesFixerConfig("\t", "\r\n"),
        ];

        yield 'messy whitespaces 3' => [
            [],
            "<?php\r\n/**\r\n * @param int    \$limit\r\n * @param string \$more\r\n *\r\n * @return array\r\n */",
            "<?php\r\n/**\r\n * @param   int       \$limit\r\n * @param   string       \$more\r\n *\r\n * @return array\r\n */",
            new WhitespacesFixerConfig("\t", "\r\n"),
        ];

        yield 'messy whitespaces 4' => [
            [],
            "<?php\n/**\n * @param int \$a\n * @param int \$b\n *               ABC\n */",
            "<?php\n/**\n * @param    int \$a\n * @param    int   \$b\n * ABC\n */",
            new WhitespacesFixerConfig('    ', "\n"),
        ];

        yield 'messy whitespaces 5' => [
            [],
            "<?php\r\n/**\r\n * @param int \$z\r\n * @param int \$b\r\n *               XYZ\r\n */",
            "<?php\r\n/**\r\n * @param    int \$z\r\n * @param    int   \$b\r\n * XYZ\r\n */",
            new WhitespacesFixerConfig('    ', "\r\n"),
        ];

        yield 'badly formatted' => [
            ['tags' => ['var']],
            "<?php\n    /**\n     * @var Foo */\n",
        ];

        yield 'unicode' => [
            ['tags' => ['param', 'return']],
            '<?php
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
',
            '<?php
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
',
        ];

        yield 'does align property by default' => [
            [],
            '<?php
    /**
     * @param    int       $foobar Description
     * @return   int
     * @throws   Exception
     * @var      FooBar
     * @type     BarFoo
     * @property string    $foo    Hello World
     */
',
            '<?php
    /**
     * @param    int   $foobar   Description
     * @return  int
     * @throws Exception
     * @var       FooBar
     * @type      BarFoo
     * @property     string    $foo   Hello World
     */
',
        ];

        yield 'aligns property' => [
            ['tags' => ['param', 'property', 'return', 'throws', 'type', 'var']],
            '<?php
    /**
     * @param    int       $foobar Description
     * @return   int
     * @throws   Exception
     * @var      FooBar
     * @type     BarFoo
     * @property string    $foo    Hello World
     */
',
            '<?php
    /**
     * @param    int   $foobar   Description
     * @return  int
     * @throws Exception
     * @var       FooBar
     * @type      BarFoo
     * @property     string    $foo   Hello World
     */
',
        ];

        yield 'does align method by default' => [
            [],
            '<?php
    /**
     * @param  int       $foobar          Description
     * @return int
     * @throws Exception
     * @var    FooBar
     * @type   BarFoo
     * @method string    foo(string $bar) Hello World
     */
',
            '<?php
    /**
     * @param    int   $foobar   Description
     * @return  int
     * @throws Exception
     * @var       FooBar
     * @type      BarFoo
     * @method     string    foo(string $bar)   Hello World
     */
',
        ];

        yield 'aligns method' => [
            ['tags' => ['param', 'method', 'return', 'throws', 'type', 'var']],
            '<?php
    /**
     * @param  int       $foobar                                        Description
     * @return int
     * @throws Exception
     * @var    FooBar
     * @type   BarFoo
     * @method int       foo(string $bar, string ...$things, int &$baz) Description
     */
',
            '<?php
    /**
     * @param    int   $foobar     Description
     * @return  int
     * @throws Exception
     * @var       FooBar
     * @type      BarFoo
     * @method        int    foo(string $bar, string ...$things, int &$baz)   Description
     */
',
        ];

        yield 'aligns method without parameters' => [
            ['tags' => ['method', 'property']],
            '<?php
    /**
     * @property string $foo  Desc
     * @method   int    foo() Description
     */
',
            '<?php
    /**
     * @property    string   $foo     Desc
     * @method int      foo()          Description
     */
',
        ];

        yield 'aligns method without parameters left align' => [
            ['tags' => ['method', 'property'], 'align' => PhpdocAlignFixer::ALIGN_LEFT],
            '<?php
    /**
     * @property string $foo Desc
     * @method int foo() Description
     */
',
            '<?php
    /**
     * @property    string   $foo     Desc
     * @method int      foo()          Description
     */
',
        ];

        yield 'does not format method' => [
            ['tags' => ['method']],
            '<?php
    /**
     * @method int foo( string  $bar ) Description
     */
',
        ];

        yield 'aligns method without return type' => [
            ['tags' => ['method', 'property']],
            '<?php
    /**
     * @property string $foo  Desc
     * @method   int    foo() Description
     * @method          bar() Descrip
     */
',
            '<?php
    /**
     * @property    string   $foo     Desc
     * @method int      foo()          Description
     * @method    bar()   Descrip
     */
',
        ];

        yield 'aligns methods without return type' => [
            ['tags' => ['method']],
            '<?php
    /**
     * @method fooBaz()         Description
     * @method bar(string $foo) Descrip
     */
',
            '<?php
    /**
     * @method         fooBaz()  Description
     * @method    bar(string $foo)   Descrip
     */
',
        ];

        yield 'aligns static and non-static methods' => [
            ['tags' => ['method', 'property']],
            '<?php
    /**
     * @property        string      $foo             Desc1
     * @property        int         $bar             Desc2
     * @method                      foo(string $foo) DescriptionFoo
     * @method          static      bar(string $foo) DescriptionBar
     * @method          string|null baz(bool $baz)   DescriptionBaz
     * @method   static int|false   qux(float $qux)  DescriptionQux
     * @method   static static      quux(int $quux)  DescriptionQuux
     * @method   static $this       quuz(bool $quuz) DescriptionQuuz
     */
',
            '<?php
    /**
     * @property    string   $foo     Desc1
     * @property  int   $bar   Desc2
     * @method     foo(string $foo)    DescriptionFoo
     * @method  static     bar(string $foo) DescriptionBar
     * @method    string|null    baz(bool $baz)  DescriptionBaz
     * @method static     int|false qux(float $qux) DescriptionQux
     * @method static   static    quux(int $quux) DescriptionQuux
     * @method static  $this     quuz(bool $quuz) DescriptionQuuz
     */
',
        ];

        yield 'aligns static and non-static methods left align' => [
            ['tags' => ['method', 'property'], 'align' => PhpdocAlignFixer::ALIGN_LEFT],
            '<?php
    /**
     * @property string $foo Desc1
     * @property int $bar Desc2
     * @method foo(string $foo) DescriptionFoo
     * @method static bar(string $foo) DescriptionBar
     * @method string|null baz(bool $baz) DescriptionBaz
     * @method static int|false qux(float $qux) DescriptionQux
     * @method static static quux(int $quux) DescriptionQuux
     * @method static $this quuz(bool $quuz) DescriptionQuuz
     */
',
            '<?php
    /**
     * @property    string   $foo     Desc1
     * @property  int   $bar   Desc2
     * @method     foo(string $foo)    DescriptionFoo
     * @method  static     bar(string $foo) DescriptionBar
     * @method    string|null    baz(bool $baz)  DescriptionBaz
     * @method static     int|false qux(float $qux) DescriptionQux
     * @method static   static    quux(int $quux) DescriptionQuux
     * @method static  $this     quuz(bool $quuz) DescriptionQuuz
     */
',
        ];

        yield 'aligns return static' => [
            ['tags' => ['param', 'return', 'throws']],
            '<?php
    /**
     * @param  string    $foobar Desc1
     * @param  int       &$baz   Desc2
     * @param  ?Qux      $qux    Desc3
     * @param  int|float $quux   Desc4
     * @return static    DescriptionReturn
     * @throws Exception DescriptionException
     */
',
            '<?php
    /**
     * @param    string   $foobar     Desc1
     * @param  int   &$baz   Desc2
     * @param ?Qux       $qux   Desc3
     * @param    int|float $quux   Desc4
     * @return  static     DescriptionReturn
     * @throws   Exception       DescriptionException
     */
',
        ];

        yield 'aligns return static left align' => [
            ['tags' => ['param', 'return', 'throws'], 'align' => PhpdocAlignFixer::ALIGN_LEFT],
            '<?php
    /**
     * @param string $foobar Desc1
     * @param int &$baz Desc2
     * @param ?Qux $qux Desc3
     * @param int|float $quux Desc4
     * @return static DescriptionReturn
     * @throws Exception DescriptionException
     */
',
            '<?php
    /**
     * @param    string   $foobar     Desc1
     * @param  int   &$baz   Desc2
     * @param ?Qux       $qux   Desc3
     * @param    int|float $quux   Desc4
     * @return  static     DescriptionReturn
     * @throws   Exception       DescriptionException
     */
',
        ];

        yield 'does not align with empty config' => [
            ['tags' => []],
            '<?php
    /**
     * @param    int   $foobar   Description
     * @return  int
     * @throws Exception
     * @var       FooBar
     * @type      BarFoo
     * @property     string    $foo   Hello World
     * @method    int    bar() Description
     */
',
        ];

        yield 'variadic params 1' => [
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
        ];

        yield 'variadic params 2' => [
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
        ];

        yield 'variadic params 3' => [
            ['tags' => ['param'], 'align' => PhpdocAlignFixer::ALIGN_LEFT],
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
        ];

        yield 'variadic params 4' => [
            ['tags' => ['property', 'property-read', 'property-write']],
            '<?php
/**
 * @property       string $myMagicProperty      magic property
 * @property-read  string $myMagicReadProperty  magic read-only property
 * @property-write string $myMagicWriteProperty magic write-only property
 */
class Foo {}
',
            '<?php
/**
 * @property string $myMagicProperty magic property
 * @property-read string $myMagicReadProperty magic read-only property
 * @property-write string $myMagicWriteProperty magic write-only property
 */
class Foo {}
',
        ];

        yield 'invalid PHPDoc 1' => [
            ['tags' => ['param', 'return', 'throws', 'type', 'var']],
            '<?php
/**
 * @ Security("is_granted(\'CANCEL\', giftCard)")
 */
 ',
        ];

        yield 'invalid PHPDoc 2' => [
            ['tags' => ['param', 'return', 'throws', 'type', 'var', 'method']],
            '<?php
/**
 * @ Security("is_granted(\'CANCEL\', giftCard)")
 */
 ',
        ];

        yield 'invalid PHPDoc 3' => [
            ['tags' => ['param', 'return', 'throws', 'type', 'var']],
            '<?php
/**
 * @ Security("is_granted(\'CANCEL\', giftCard)")
 * @     foo   bar
 *   @ foo
 */
 ',
        ];

        yield 'types containing callables' => [
            [],
            '<?php
            /**
             * @param callable(Foo): Bar       $x  Description
             * @param callable(FooFoo): BarBar $yy Description
             */
            ',
            '<?php
            /**
             * @param callable(Foo): Bar $x Description
             * @param callable(FooFoo): BarBar $yy Description
             */
            ',
        ];

        yield 'types containing whitespace' => [
            [],
            '<?php
            /**
             * @var int                   $key
             * @var iterable<int, string> $value
             */
            /**
             * @param array<int, $this>    $arrayOfIntegers
             * @param array<string, $this> $arrayOfStrings
             */
        ', ];

        yield 'closure types containing backslash' => [
            [],
            '<?php
            /**
             * @var string                              $input
             * @var \Closure                            $fn
             * @var \Closure(bool):int                  $fn2
             * @var Closure                             $fn3
             * @var Closure(string):string              $fn4
             * @var array<string, array<string, mixed>> $data
             */
            /**
             * @param string                              $input
             * @param \Closure                            $fn
             * @param \Closure(bool):int                  $fn2
             * @param Closure                             $fn3
             * @param Closure(string):string              $fn4
             * @param array<string, array<string, mixed>> $data
             */
            /**
             * @var string                   $value
             * @var \Closure(string): string $callback
             * @var Closure(int): bool       $callback2
             */
            /**
             * @param string                   $value
             * @param \Closure(string): string $callback
             * @param Closure(int): bool       $callback2
             */
            /**
             * @var Closure(array<int, bool>): bool $callback1
             * @var \Closure(string): string        $callback2
             */
            /**
             * @param Closure(array<int, bool>): bool $callback1
             * @param \Closure(string): string        $callback2
             */
        ', ];

        yield 'types parenthesized' => [
            [],
            '<?php
            /**
             * @param list<string>                                   $allowedTypes
             * @param null|list<\Closure(mixed): (bool|null|scalar)> $allowedValues
             */
            ',
            '<?php
            /**
             * @param list<string> $allowedTypes
             * @param null|list<\Closure(mixed): (bool|null|scalar)> $allowedValues
             */
            ',
        ];

        yield 'callable types with ugly code 1' => [
            [],
            '<?php
        /**
         * @var callable                      $fn
         * @var callable(bool): int           $fn2
         * @var Closure                       $fn3
         * @var Closure(string|object):string $fn4
         * @var \Closure                      $fn5
         * @var \Closure(int, bool): bool     $fn6
         */
        ',
            '<?php
        /**
         * @var callable $fn
         * @var callable(bool): int $fn2
         * @var Closure $fn3
         * @var Closure(string|object):string $fn4
         * @var \Closure $fn5
         * @var \Closure(int, bool): bool $fn6
         */
        ',
        ];

        yield 'callable types with ugly code 2' => [
            [],
            '<?php
        /**
         * @var callable                      $fn
         * @var callable(bool): int           $fn2
         * @var Closure                       $fn3
         * @var Closure(string|object):string $fn4
         * @var \Closure                      $fn5
         * @var \Closure(int, bool): bool     $fn6
         */
        ',
            '<?php
        /**
         * @var          callable           $fn
         * @var   callable(bool): int     $fn2
         * @var   Closure          $fn3
         * @var Closure(string|object):string                  $fn4
         * @var      \Closure             $fn5
         * @var            \Closure(int, bool): bool       $fn6
         */
        ',
        ];

        yield 'CUSTOM tags' => [
            ['tags' => ['param', 'xxx-xxxxxxxxx']],
            '<?php
    /**
     * @param         EngineInterface $templating
     * @param         string          $format
     * @xxx-xxxxxxxxx int             $code       An HTTP response status code
     * @param         bool            $debug
     * @param         mixed           &$reference A parameter passed by reference
     */

',
            '<?php
    /**
     * @param  EngineInterface $templating
     * @param string      $format
     * @xxx-xxxxxxxxx  int  $code       An HTTP response status code
     * @param    bool         $debug
     * @param  mixed    &$reference     A parameter passed by reference
     */

',
        ];

        yield 'no/2+ spaces after comment star' => [
            [],
            '<?php
/**
 * @property string $age  @Atk4\Field()
 * @property string $city @Atk4\Field()
 */
',
            '<?php
/**
 *  @property string $age           @Atk4\Field()
*@property    string $city          @Atk4\Field()
 */
',
        ];

        yield 'untyped param with multiline desc' => [
            [],
            '<?php
/**
 * @param $typeless Foo.
 *                  Bar.
 */
function foo($typeless): void {}',
            '<?php
/**
 * @param $typeless    Foo.
 *                     Bar.
 */
function foo($typeless): void {}',
        ];

        yield 'left align and @param with 2 spaces' => [
            [
                'align' => PhpdocAlignFixer::ALIGN_LEFT,
                'spacing' => ['param' => 2],
            ],
            '<?php
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
     *
     */
',
            '<?php
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
     *             description foo
     *
     */
',
        ];

        yield 'vertical align with various spacing' => [
            [
                'align' => PhpdocAlignFixer::ALIGN_VERTICAL,
                'spacing' => ['param' => 2, 'return' => 4],
            ],
            '<?php
    /**
     * @param  EngineInterface  $templating
     * @param  string           $format
     * @param  int              $code        An HTTP response status code
     *                                       See constants
     * @param  bool             $debug
     * @param  bool             $debug       See constants
     *                                       See constants
     * @param  mixed            &$reference  A parameter passed by reference
     *
     * @return    Foo    description foo bar hello world!
     *                   return description continuation
     *
     * @throws Foo description foo
     *             description foo
     *
     */
',
            '<?php
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
     * @return Foo description foo bar hello world!
    *        return description continuation
     *
     * @throws Foo             description foo
     *             description foo
     *
     */
',
        ];

        yield 'left align with various spacing' => [
            [
                'align' => PhpdocAlignFixer::ALIGN_LEFT,
                'spacing' => ['param' => 2, 'return' => 4],
            ],
            '<?php
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
     * @return    Foo    description foo
     *
     * @throws Foo description foo
     *             description foo
     *
     */
',
            '<?php
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
     *             description foo
     *
     */
',
        ];

        yield 'left align with changed default spacing' => [
            [
                'align' => PhpdocAlignFixer::ALIGN_LEFT,
                'spacing' => ['_default' => 2, 'return' => 4],
            ],
            '<?php
    /**
     * @property  string  $bar  Foo-Bar lorem ipsum
     * @param  EngineInterface  $templating
     * @param  string  $format
     * @param  int  $code  An HTTP response status code
     *                     See constants
     * @param  bool  $debug
     * @param  bool  $debug  See constants
     *                       See constants
     * @param  mixed  &$reference  A parameter passed by reference
     *
     * @return    Foo    description foo
     *
     * @throws  Foo  description foo
     *               description foo
     *
     */
',
            '<?php
    /**
     * @property string $bar                    Foo-Bar lorem ipsum
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
     *             description foo
     *
     */
',
        ];
    }

    /**
     * @dataProvider provideInvalidConfigurationCases
     *
     * @param array<string,mixed> $config
     */
    public function testInvalidConfiguration(array $config, string $expectedMessage): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);
        $this->expectExceptionMessage($expectedMessage);

        $this->fixer->configure($config);
    }

    /**
     * @return iterable<array{array<string,mixed>, string}>
     */
    public static function provideInvalidConfigurationCases(): iterable
    {
        yield 'zero' => [
            ['spacing' => 0],
            'The option "spacing" is invalid. All spacings must be greater than zero.',
        ];

        yield 'negative' => [
            ['spacing' => -2],
            'The option "spacing" is invalid. All spacings must be greater than zero.',
        ];

        yield 'zeroInArray' => [
            ['spacing' => ['param' => 1, 'return' => 0]],
            'The option "spacing" is invalid. All spacings must be greater than zero.',
        ];

        yield 'negativeInArray' => [
            [
                'align' => PhpdocAlignFixer::ALIGN_LEFT,
                'spacing' => ['return' => 2, 'param' => -1],
            ],
            'The option "spacing" is invalid. All spacings must be greater than zero.',
        ];
    }
}
