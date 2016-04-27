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

/**
 * @author Javier Spagnoletti <phansys@gmail.com>
 */
final class SinglePropertyPerStatementFixerTest extends AbstractFixerTestCase
{
    /**
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

class Foo { const ONE = 1, const TWO = 2; protected static $foo = 1; protected static $bar; protected static $baz=2 ; const THREE = 3; }
EOT
                , <<<'EOT'
<?php

class Foo { const ONE = 1, const TWO = 2; protected static $foo = 1, $bar,  $baz=2 ; const THREE = 3; }
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

class Foo { var $foo = 1; var $bar; public function doSomething() {} var $baz=2; }
EOT
                , <<<'EOT'
<?php

class Foo { var $foo = 1, $bar; public function doSomething() {} var $baz=2; }
EOT
            ),
            array(
                <<<'EOT'
<?php

class Foo { var $foo = 1; var $bar; public function doSomething() { $one, $two, $three = 123; } var $baz=2; }
EOT
                , <<<'EOT'
<?php

class Foo { var $foo = 1, $bar; public function doSomething() { $one, $two, $three = 123; } var $baz=2; }
EOT
            ),
            array(
                <<<'EOT'
<?php

class Foo { public function doSomething() {} protected $foo = 1; protected $bar; protected $baz=2; }
EOT
                , <<<'EOT'
<?php

class Foo { public function doSomething() {} protected $foo = 1, $bar, $baz=2; }
EOT
            ),
            array(
                <<<'EOT'
<?php

class Foo { public function doSomething() {} protected $foo = 1; protected $bar; protected $baz=2; private $acme =array(); }
EOT
                , <<<'EOT'
<?php

class Foo { public function doSomething() {} protected $foo = 1, $bar, $baz=2; private $acme =array(); }
EOT
            ),
            array(
                <<<'EOT'
<?php

class Foo { public function doSomething() {}; protected $foo = 1; protected $bar; protected $baz=2; private $acme =array(); }
EOT
                , <<<'EOT'
<?php

class Foo { public function doSomething() {}; protected $foo = 1, $bar, $baz=2; private $acme =array(); }
EOT
            ),
            array(
                <<<'EOT'
<?php

class Foo { public function doSomething() { $one, $two, $three = 123; } protected $foo = 1; protected $bar; protected $baz=2; private $acme =array(); }
EOT
                , <<<'EOT'
<?php

class Foo { public function doSomething() { $one, $two, $three = 123; } protected $foo = 1, $bar, $baz=2; private $acme =array(); }
EOT
            ),
        );
    }

    /**
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

        $this->getFixer()->configure($configuration);

        $this->doTest($expected, $input);
    }

    public function provideConfigurationCases()
    {
        return array(
            array(
                array('constant', 'property'),
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
                array('constant'),
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

    /**
     * @expectedException \PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException
     * @expectedExceptionMessage [single_property_per_statement] Unknown configuration option "foo"
     */
    public function testWrongConfig()
    {
        $this->getFixer()->configure(array('foo'));
    }
}
