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
 * @author Gregor Harlan <gharlan@web.de>
 *
 * @internal
 */
final class OrderedClassElementsFixerTest extends AbstractFixerTestCase
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

class Foo { const C1 = 1; protected $abc = 'abc'; public function baz($y, $z) {} private function bar1($x) { return 1; } }
EOT
                , <<<'EOT'
<?php

class Foo { private function bar1($x) { return 1; } protected $abc = 'abc'; const C1 = 1; public function baz($y, $z) {} }
EOT
            ),
            array(
                <<<'EOT'
<?php

interface FooInterface
{

    const CONST1 = 'const1';

    const CONST2 = 'const2';
    public function xyz($x, $y, $z); // comment

    /**
     * @param array $a
     *
     * @return string
     */
    function abc(array &$a = null);

    public function def();
}
EOT
                , <<<'EOT'
<?php

interface FooInterface
{
    public function xyz($x, $y, $z); // comment

    const CONST1 = 'const1';

    /**
     * @param array $a
     *
     * @return string
     */
    function abc(array &$a = null);

    const CONST2 = 'const2';

    public function def();
}
EOT
            ),
            array(
                <<<'EOT'
<?php

abstract class Foo extends FooParent implements FooInterface1, FooInterface2
{

    use Bar;

    use Baz {
        abc as private;
    }

    const C1 = 1;
    /* comment for C2 */

    const C2 = 2;

    public $fooPublic;

    // comment 3

    protected $fooProtected = array(1, 2);
    // comment 1

    private $fooPrivate;

    protected function __construct()
    {
    }

    public function __destruct() {}

    public function __clone() {}

    public static function setUpBeforeClass() {}

    public static function teardownafterclass() {
    } /* multiline
    comment */

    public function setUp() {}

    protected function tearDown() {}

    abstract public function foo1($a, $b = 1);

    // foo2
    function foo2()
    {
        return $this->foo1(1);
    }

    public static function foo3(self $foo)
    {
        return $foo->foo2();
    } /* comment 1 */ /* comment 2 */

    // comment

    /**
     * Docblock
     */
    protected function foo4(\ArrayObject $object, array $array, $a = null)
    {
        bar();

        if (!$a) {
            $a = function ($x) {
                var_dump($x);
            };
        }
    }

    private function foo5()
    {
    } // end foo5
}
EOT
                , <<<'EOT'
<?php

abstract class Foo extends FooParent implements FooInterface1, FooInterface2
{
    // comment 1

    private $fooPrivate;

    abstract public function foo1($a, $b = 1);

    protected function tearDown() {}

    public function __clone() {}

    const C1 = 1;

    // foo2
    function foo2()
    {
        return $this->foo1(1);
    }

    public static function setUpBeforeClass() {}

    public function __destruct() {}

    use Bar;

    public static function foo3(self $foo)
    {
        return $foo->foo2();
    } /* comment 1 */ /* comment 2 */

    // comment 3

    protected $fooProtected = array(1, 2);

    public $fooPublic;
    /* comment for C2 */

    const C2 = 2;

    public static function teardownafterclass() {
    } /* multiline
    comment */

    use Baz {
        abc as private;
    }

    private function foo5()
    {
    } // end foo5

    public function setUp() {}

    protected function __construct()
    {
    }

    // comment

    /**
     * Docblock
     */
    protected function foo4(\ArrayObject $object, array $array, $a = null)
    {
        bar();

        if (!$a) {
            $a = function ($x) {
                var_dump($x);
            };
        }
    }
}
EOT
            ),
            array(
                <<<'EOT'
<?php

class Foo
{
    const C = 'C';
    public function abc() {}
    protected function xyz() {}
}

class Bar
{
    const C = 1;
    public function foo($a) { return 1; }
    public function baz() {}
}
EOT
                , <<<'EOT'
<?php

class Foo
{
    protected function xyz() {}
    const C = 'C';
    public function abc() {}
}

class Bar
{
    public function foo($a) { return 1; }
    const C = 1;
    public function baz() {}
}
EOT
            ),
        );
    }

    /**
     * @dataProvider provideCases54
     * @requires PHP 5.4
     */
    public function testFix54($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideCases54()
    {
        return array(
            array(
                <<<'EOT'
<?php

trait FooTrait
{

    use BarTrait;

    use BazTrait;
    protected function abc() {
    }
}
EOT
                , <<<'EOT'
<?php

trait FooTrait
{
    protected function abc() {
    }

    use BarTrait;

    use BazTrait;
}
EOT
            ),
        );
    }

    /**
     * @dataProvider provideCases71
     * @requires PHP 7.1
     */
    public function testFix71($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideCases71()
    {
        return array(
            array(
                <<<'EOT'
<?php

class Foo
{
    public const C1 = 1;
    const C2 = 2;
    public const C3 = 3;
    protected const C4 = 4;
    private const C5 = 5;
}
EOT
                , <<<'EOT'
<?php

class Foo
{
    private const C5 = 5;
    public const C1 = 1;
    protected const C4 = 4;
    const C2 = 2;
    public const C3 = 3;
}
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
    private static function privStatFunc() {}
    protected static $protStatProp;
    public static $pubStatProp1;
    public function pubFunc1() {}
    use BarTrait;
    public $pubProp1;
    public function __toString() {}
    protected function protFunc() {}
    protected $protProp;
    function pubFunc2() {}
    public function __destruct() {}
    var $pubProp2;
    private static $privStatProp;
    use BazTrait;
    public static function pubStatFunc1() {}
    public function pubFunc3() {}
    private $privProp;
    const C1 = 1;
    static function pubStatFunc2() {}
    private function privFunc() {}
    public static $pubStatProp2;
    protected function __construct() {}
    const C2 = 2;
    public static function pubStatFunc3() {}
    public $pubProp3;
    protected static function protStatFunc() {}
}
EOT;

        $this->getFixer()->configure($configuration);

        $this->doTest($expected, $input);
    }

    public function provideConfigurationCases()
    {
        return array(
            array(
                array('use_trait', 'constant', 'property', 'construct', 'method', 'destruct'),
                <<<'EOT'
<?php

class Foo
{
    use BarTrait;
    use BazTrait;
    const C1 = 1;
    const C2 = 2;
    protected static $protStatProp;
    public static $pubStatProp1;
    public $pubProp1;
    protected $protProp;
    var $pubProp2;
    private static $privStatProp;
    private $privProp;
    public static $pubStatProp2;
    public $pubProp3;
    protected function __construct() {}
    private static function privStatFunc() {}
    public function pubFunc1() {}
    public function __toString() {}
    protected function protFunc() {}
    function pubFunc2() {}
    public static function pubStatFunc1() {}
    public function pubFunc3() {}
    static function pubStatFunc2() {}
    private function privFunc() {}
    public static function pubStatFunc3() {}
    protected static function protStatFunc() {}
    public function __destruct() {}
}
EOT
            ),
            array(
                array('public', 'protected', 'private'),
                <<<'EOT'
<?php

class Foo
{
    public static $pubStatProp1;
    public function pubFunc1() {}
    public $pubProp1;
    public function __toString() {}
    function pubFunc2() {}
    public function __destruct() {}
    var $pubProp2;
    public static function pubStatFunc1() {}
    public function pubFunc3() {}
    const C1 = 1;
    static function pubStatFunc2() {}
    public static $pubStatProp2;
    const C2 = 2;
    public static function pubStatFunc3() {}
    public $pubProp3;
    protected static $protStatProp;
    protected function protFunc() {}
    protected $protProp;
    protected function __construct() {}
    protected static function protStatFunc() {}
    private static function privStatFunc() {}
    private static $privStatProp;
    private $privProp;
    private function privFunc() {}
    use BarTrait;
    use BazTrait;
}
EOT
            ),
            array(
                array(
                    'use_trait',
                    'constant',
                    'property_public_static',
                    'property_protected_static',
                    'property_private_static',
                    'property_public',
                    'property_protected',
                    'property_private',
                    'construct',
                    'destruct',
                    'magic',
                    'method_public_static',
                    'method_protected_static',
                    'method_private_static',
                    'method_public',
                    'method_protected',
                    'method_private',
                ),
                <<<'EOT'
<?php

class Foo
{
    use BarTrait;
    use BazTrait;
    const C1 = 1;
    const C2 = 2;
    public static $pubStatProp1;
    public static $pubStatProp2;
    protected static $protStatProp;
    private static $privStatProp;
    public $pubProp1;
    var $pubProp2;
    public $pubProp3;
    protected $protProp;
    private $privProp;
    protected function __construct() {}
    public function __destruct() {}
    public function __toString() {}
    public static function pubStatFunc1() {}
    static function pubStatFunc2() {}
    public static function pubStatFunc3() {}
    protected static function protStatFunc() {}
    private static function privStatFunc() {}
    public function pubFunc1() {}
    function pubFunc2() {}
    public function pubFunc3() {}
    protected function protFunc() {}
    private function privFunc() {}
}
EOT
            ),
        );
    }

    /**
     * @expectedException \PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException
     * @expectedExceptionMessage [ordered_class_elements] Unknown class element type "foo".
     */
    public function testWrongConfig()
    {
        $this->getFixer()->configure(array('foo'));
    }
}
