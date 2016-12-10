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
 * @author Javier Spagnoletti <phansys@gmail.com>
 *
 * @internal
 */
final class SingleClassElementPerStatementFixerTest extends AbstractFixerTestCase
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

    public function provideCases()
    {
        return array(
            array(
                '<?php
class Foo
{
    private static $bar1 = array(1,2,3);
    private static $bar2 = [1,2,3];
    private static $baz1 = array(array(1,2), array(3, 4));
    private static $baz2 = array(1,2,3);
    private static $aaa1 = 1;
    private static $aaa2 = array(2, 2);
    private static $aaa3 = 3;
}',
                '<?php
class Foo
{
    private static $bar1 = array(1,2,3), $bar2 = [1,2,3];
    private static $baz1 = array(array(1,2), array(3, 4)), $baz2 = array(1,2,3);
    private static $aaa1 = 1, $aaa2 = array(2, 2), $aaa3 = 3;
}',
            ),
            array(
                '<?php
class Foo
{
    const A = 1;
    const B = 2;
}

echo Foo::A, Foo::B;
',
                '<?php
class Foo
{
    const A = 1, B = 2;
}

echo Foo::A, Foo::B;
',
            ),
            array(
                <<<'EOT'
<?php

class Foo { protected static $foo = 1; protected static $bar; protected static $baz=2 ; }
EOT
                , <<<'EOT'
<?php

class Foo { protected static $foo = 1,$bar,$baz=2 ; }
EOT
            ),
            array(
                <<<'EOT'
<?php

class Foo {}

class Bar
{
}
EOT
            ),
            array(
                <<<'EOT'
<?php

class Foo { protected static $foo = 1; protected static $bar; protected static $baz=2 ; }
EOT
                , <<<'EOT'
<?php

class Foo { protected static $foo = 1, $bar,  $baz=2 ; }
EOT
            ),
            array(
                <<<'EOT'
<?php

class Foo { const ONE = 1; const TWO = 2; protected static $foo = 1; protected static $bar; protected static $baz=2 ; const THREE = 3; }
EOT
                , <<<'EOT'
<?php

class Foo { const ONE = 1, TWO = 2; protected static $foo = 1, $bar,  $baz=2 ; const THREE = 3; }
EOT
            ),
            array(
                <<<'EOT'
<?php

class Foo {
    protected static $foo = 1;
    protected static $bar;
    protected static $baz=2;
}
EOT
                , <<<'EOT'
<?php

class Foo {
    protected static $foo = 1,
    $bar,
   $baz=2;
}
EOT
            ),
            array(
                <<<'EOT'
<?php

class Foo {
    /**
     * Some great docblock
     */
    protected static $foo = 1;
    protected static $bar;
    protected static $baz=2;
}
EOT
                , <<<'EOT'
<?php

class Foo {
    /**
     * Some great docblock
     */
    protected static $foo = 1,
    $bar,
   $baz=2;
}
EOT
            ),
            array(
                <<<'EOT'
<?php

class Foo {
    /**
     * @int
     */
    protected static $foo = 1;
    protected static $bar;
    protected static $baz=2;
    // this is an inline comment, not a docblock
    private $var = false;
}
EOT
                , <<<'EOT'
<?php

class Foo {
    /**
     * @int
     */
    protected static $foo = 1,
    $bar,
   $baz=2;
    // this is an inline comment, not a docblock
    private $var = false;
}
EOT
            ),
            array(
                <<<'EOT'
<?php

class Foo {
    /**
     * @int
     */
    protected static $foo = 1;
    protected static $bar;
    protected static $baz=2;

    function doSomething()
    {
    }
}
EOT
                , <<<'EOT'
<?php

class Foo {
    /**
     * @int
     */
    protected static $foo = 1,
    $bar,
    $baz=2;

    function doSomething()
    {
    }
}
EOT
            ),
            'line_breaks_1' => array(
                <<<'EOT'
<?php

class Foo
{

    public $bar = null;
    public $initialized = false;
    public $configured = false;
    public $called = false;
    public $arguments = array();


    public $baz = null;
    public $drop = false;

    function doSomething()
    {
    }
}
EOT
                , <<<'EOT'
<?php

class Foo
{

    public $bar = null, $initialized = false, $configured = false, $called = false, $arguments = array();


    public $baz = null, $drop = false;

    function doSomething()
    {
    }
}
EOT
            ),
            'line_breaks_2' => array(
                <<<'EOT'
<?php

class Foo
{
    const TWO = '2';


    public $bar = null;
    public $initialized = false;

    function doSomething()
    {
    }
}
EOT
                , <<<'EOT'
<?php

class Foo
{
    const TWO = '2';


    public $bar = null, $initialized = false;

    function doSomething()
    {
    }
}
EOT
            ),
            'line_breaks_3' => array(
                <<<'EOT'
<?php

class Foo
{
    const TWO = '2';

    public $bar = null;
    public $initialized = false;


    function doSomething()
    {
    }
}
EOT
                , <<<'EOT'
<?php

class Foo
{
    const TWO = '2';

    public $bar = null, $initialized = false;


    function doSomething()
    {
    }
}
EOT
            ),
            'line_breaks_4' => array(
                <<<'EOT'
<?php

class Foo
{
    public $one = 1;
    public $bar = null;
    public $initialized = false;
    public $configured = false;
    public $called = false;
    public $arguments = array();

    function doSomething()
    {
    }
}
EOT
                , <<<'EOT'
<?php

class Foo
{
    public $one = 1;
    public $bar = null, $initialized = false, $configured = false, $called = false, $arguments = array();

    function doSomething()
    {
    }
}
EOT
            ),
            'line_breaks_5' => array(
                <<<'EOT'
<?php

class Foo
{
    public $one = 1; public $bar = null; public $initialized = false; public $configured = false; public $called = false; public $arguments = array();

    function doSomething()
    {
    }
}
EOT
                , <<<'EOT'
<?php

class Foo
{
    public $one = 1; public $bar = null, $initialized = false, $configured = false, $called = false, $arguments = array();

    function doSomething()
    {
    }
}
EOT
            ),
            'line_breaks_6' => array(
                <<<'EOT'
<?php

class Foo
{
    public $one = 1;public $bar = null;public $initialized = false;public $configured = false;public $called = false;public $arguments = array();

    function doSomething()
    {
    }
}
EOT
                , <<<'EOT'
<?php

class Foo
{
    public $one = 1;public $bar = null, $initialized = false, $configured = false, $called = false, $arguments = array();

    function doSomething()
    {
    }
}
EOT
            ),
            'whitespace_1' => array(
                <<<'EOT'
<?php

class Foo {    public $one = 1; public $bar = null; public $initialized = false; public $configured = false; public $called = false; public $arguments = array();

    function doSomething()
    {
    }
}
EOT
                , <<<'EOT'
<?php

class Foo {    public $one = 1; public $bar = null,$initialized = false,$configured = false,$called = false,$arguments = array();

    function doSomething()
    {
    }
}
EOT
            ),
            'whitespace_2' => array(
                <<<'EOT'
<?php

class Foo {    public $one = 1;  public $bar = null;  public $initialized = false;  public $configured = false;  public $called=false;  public $arguments = array();

    function doSomething()
    {
    }
}
EOT
                , <<<'EOT'
<?php

class Foo {    public $one = 1;  public $bar = null,$initialized = false,$configured = false,$called=false,$arguments = array();

    function doSomething()
    {
    }
}
EOT
            ),
            array(
                <<<'EOT'
<?php

class Foo { protected static $foo = 1; protected static $bar; protected static $baz=1; }
EOT
                , <<<'EOT'
<?php

class Foo { protected static $foo = 1, $bar, $baz=1; }
EOT
            ),
            array(
                <<<'EOT'
<?php

class Foo {   protected static $foo = 1;   protected static $bar;   protected static $baz=1; }
EOT
                , <<<'EOT'
<?php

class Foo {   protected static $foo = 1, $bar, $baz=1; }
EOT
            ),
            array(
                <<<'EOT'
<?php

class Foo { protected $foo = 1; protected $bar; protected $baz=2; }
EOT
                , <<<'EOT'
<?php

class Foo { protected $foo = 1, $bar, $baz=2; }
EOT
            ),
            array(
                <<<'EOT'
<?php

class Foo { var $foo = 1; var $bar; var $baz=2; }
EOT
                , <<<'EOT'
<?php

class Foo { var $foo = 1, $bar, $baz=2; }
EOT
            ),
            array(
                <<<'EOT'
<?php

class Foo { var $foo = 1; var $bar; public function doSomething1() {} var $baz=2; }
EOT
                , <<<'EOT'
<?php

class Foo { var $foo = 1, $bar; public function doSomething1() {} var $baz=2; }
EOT
            ),
            array(
                <<<'EOT'
<?php

class Foo { var $foo = 1; var $bar; public function doSomething2() { global $one, $two, $three; } var $baz=2; }
EOT
                , <<<'EOT'
<?php

class Foo { var $foo = 1, $bar; public function doSomething2() { global $one, $two, $three; } var $baz=2; }
EOT
            ),
            array(
                <<<'EOT'
<?php

class Foo { public function doSomething3() {} protected $foo = 1; protected $bar; protected $baz=2; }
EOT
                , <<<'EOT'
<?php

class Foo { public function doSomething3() {} protected $foo = 1, $bar, $baz=2; }
EOT
            ),
            array(
                <<<'EOT'
<?php

class Foo { public function doSomethingElse() {} protected $foo = 1; protected $bar; protected $baz=2; private $acme =array(); }
EOT
                , <<<'EOT'
<?php

class Foo { public function doSomethingElse() {} protected $foo = 1, $bar, $baz=2; private $acme =array(); }
EOT
            ),
            array(
                <<<'EOT'
<?php

class Foo { public function doSomewhere() {} protected $foo = 1; protected $bar; protected $baz=2; private $acme1 =array(); }
EOT
                , <<<'EOT'
<?php

class Foo { public function doSomewhere() {} protected $foo = 1, $bar, $baz=2; private $acme1 =array(); }
EOT
            ),
            array(
                <<<'EOT'
<?php

class Foo { public function doThis() { global $one1, $two2, $three3; } protected $foo = 1; protected $bar; protected $baz=2; private $acme2 =array(); }
EOT
                , <<<'EOT'
<?php

class Foo { public function doThis() { global $one1, $two2, $three3; } protected $foo = 1, $bar, $baz=2; private $acme2 =array(); }
EOT
            ),
        );
    }

    /**
     * @param string $expected
     *
     * @dataProvider provideConfigurationCases
     */
    public function testFixWithConfiguration(array $configuration, $expected)
    {
        static $input = <<<'EOT'
<?php

class Foo
{
    const SOME_CONST = 'a', OTHER_CONST = 'b';
    protected static $foo = 1, $bar = 2;
}
EOT;

        $this->fixer->configure($configuration);

        $this->doTest($expected, $input);
    }

    public function provideConfigurationCases()
    {
        return array(
            array(
                array('const', 'property'),
                <<<'EOT'
<?php

class Foo
{
    const SOME_CONST = 'a';
    const OTHER_CONST = 'b';
    protected static $foo = 1;
    protected static $bar = 2;
}
EOT
            ),
            array(
                array('const'),
                <<<'EOT'
<?php

class Foo
{
    const SOME_CONST = 'a';
    const OTHER_CONST = 'b';
    protected static $foo = 1, $bar = 2;
}
EOT
            ),
            array(
                array('property'),
                <<<'EOT'
<?php

class Foo
{
    const SOME_CONST = 'a', OTHER_CONST = 'b';
    protected static $foo = 1;
    protected static $bar = 2;
}
EOT
            ),
        );
    }

    public function testWrongConfig()
    {
        $this->setExpectedExceptionRegExp(
            'PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException',
            '/^\[single_class_element_per_statement\] Unknown configuration option "foo"\. Expected any of "property", "const"\.$/'
        );

        $this->fixer->configure(array('foo'));
    }

    /**
     * @param string $expected
     * @param string $input
     *
     * @dataProvider providePHP71Cases
     * @requires PHP 7.1
     */
    public function testPHP71($expected, $input)
    {
        $this->doTest($expected, $input);
    }

    public function providePHP71Cases()
    {
        return array(
            array(
                '<?php
                    class Token {
                        const PUBLIC_CONST = 0;
                        private const PRIVATE_CONST = 0;
                        protected const PROTECTED_CONST = 0;
                        public const PUBLIC_CONST_TWO = 0;
                        public const TEST_71 = 0;
                    }
                ',
                '<?php
                    class Token {
                        const PUBLIC_CONST = 0;
                        private const PRIVATE_CONST = 0;
                        protected const PROTECTED_CONST = 0;
                        public const PUBLIC_CONST_TWO = 0, TEST_71 = 0;
                    }
                ',
            ),
        );
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
                "<?php\r\n\tclass Foo {\r\n\t\tconst AAA=0;\r\n\t\tconst BBB=1;\r\n\t}",
                "<?php\r\n\tclass Foo {\r\n\t\tconst AAA=0, BBB=1;\r\n\t}",
            ),
        );
    }
}
