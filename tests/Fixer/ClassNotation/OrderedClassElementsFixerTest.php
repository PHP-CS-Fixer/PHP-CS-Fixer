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

namespace PhpCsFixer\Tests\Fixer\ClassNotation;

use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\Fixer\ClassNotation\OrderedClassElementsFixer;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Gregor Harlan <gharlan@web.de>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ClassNotation\OrderedClassElementsFixer
 */
final class OrderedClassElementsFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases(): iterable
    {
        yield [
            <<<'EOT'
<?php

class Foo {}

class Bar
{
}
EOT
        ];

        yield [
            <<<'EOT'
<?php

class Foo { const C1 = 1; protected $abc = 'abc'; public function baz($y, $z) {} private function bar1($x) { return 1; } }
EOT
            , <<<'EOT'
<?php

class Foo { private function bar1($x) { return 1; } protected $abc = 'abc'; const C1 = 1; public function baz($y, $z) {} }
EOT
        ];

        yield [
            <<<'EOT'
<?php

interface FooInterface
{

    /** const 1 */
    const CONST1 = 'const1';

    // foo

    // const 2
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

    /** const 1 */
    const CONST1 = 'const1';

    /**
     * @param array $a
     *
     * @return string
     */
    function abc(array &$a = null);

    // foo

    // const 2
    const CONST2 = 'const2';

    public function def();
}
EOT
        ];

        yield [
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

    public static function tearDownAfterclass() {
    } /* multiline
    comment */

    protected function setUp() {}

    protected function assertPreConditions() {}

    protected function assertPostConditions() {}

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

    protected function setUp() {}

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

    public static function tearDownAfterclass() {
    } /* multiline
    comment */

    protected function assertPostConditions() {}

    use Baz {
        abc as private;
    }

    protected function assertPreConditions() {}

    private function foo5()
    {
    } // end foo5

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
        ];

        yield [
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
        ];

        yield [
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
        ];

        yield [
            <<<'EOT'
<?php

/**
 * @see #4086
 */
class ComplexStringVariableAndUseTrait
{
    use FooTrait;
    public function sayHello($name)
    {
        echo "Hello, {$name}!";
    }
}

EOT
            ,
            <<<'EOT'
<?php

/**
 * @see #4086
 */
class ComplexStringVariableAndUseTrait
{
    public function sayHello($name)
    {
        echo "Hello, {$name}!";
    }
    use FooTrait;
}

EOT
        ];
    }

    /**
     * @param array<string, mixed> $configuration
     *
     * @dataProvider provideConfigurationCases
     */
    public function testFixWithConfiguration(array $configuration, string $expected, string $input): void
    {
        $this->fixer->configure(['order' => $configuration]);
        $this->doTest($expected, $input);
    }

    public function provideConfigurationCases(): array
    {
        return [
            [
                ['use_trait', 'constant', 'property', 'construct', 'method', 'destruct'],
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
                ,
                <<<'EOT'
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
EOT
                ,
            ],
            [
                ['public', 'protected', 'private'],
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
                ,
                <<<'EOT'
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
EOT
                ,
            ],
            [
                [
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
                ],
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
                ,
                <<<'EOT'
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
EOT
                ,
            ],
            [
                ['use_trait', 'constant', 'property', 'construct', 'method', 'destruct'],
                <<<'EOT'
<?php

abstract class Foo
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
    abstract public function absPubFunc();
    private static function privStatFunc() {}
    public function pubFunc1() {}
    abstract protected function absProtFunc();
    public function __toString() {}
    protected function protFunc() {}
    function pubFunc2() {}
    abstract protected static function absProtStatFunc();
    public static function pubStatFunc1() {}
    public function pubFunc3() {}
    static function pubStatFunc2() {}
    private function privFunc() {}
    abstract public static function absPubStatFunc();
    public static function pubStatFunc3() {}
    protected static function protStatFunc() {}
    public function __destruct() {}
}
EOT
                ,
                <<<'EOT'
<?php

abstract class Foo
{
    abstract public function absPubFunc();
    private static function privStatFunc() {}
    protected static $protStatProp;
    public static $pubStatProp1;
    public function pubFunc1() {}
    abstract protected function absProtFunc();
    use BarTrait;
    public $pubProp1;
    public function __toString() {}
    protected function protFunc() {}
    protected $protProp;
    function pubFunc2() {}
    abstract protected static function absProtStatFunc();
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
    abstract public static function absPubStatFunc();
    protected function __construct() {}
    const C2 = 2;
    public static function pubStatFunc3() {}
    public $pubProp3;
    protected static function protStatFunc() {}
}
EOT
                ,
            ],
            [
                ['public', 'protected', 'private'],
                <<<'EOT'
<?php

abstract class Foo
{
    abstract public function absPubFunc();
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
    abstract public static function absPubStatFunc();
    const C2 = 2;
    public static function pubStatFunc3() {}
    public $pubProp3;
    protected static $protStatProp;
    abstract protected function absProtFunc();
    protected function protFunc() {}
    protected $protProp;
    abstract protected static function absProtStatFunc();
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
                ,
                <<<'EOT'
<?php

abstract class Foo
{
    abstract public function absPubFunc();
    private static function privStatFunc() {}
    protected static $protStatProp;
    public static $pubStatProp1;
    public function pubFunc1() {}
    abstract protected function absProtFunc();
    use BarTrait;
    public $pubProp1;
    public function __toString() {}
    protected function protFunc() {}
    protected $protProp;
    function pubFunc2() {}
    abstract protected static function absProtStatFunc();
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
    abstract public static function absPubStatFunc();
    protected function __construct() {}
    const C2 = 2;
    public static function pubStatFunc3() {}
    public $pubProp3;
    protected static function protStatFunc() {}
}
EOT
                ,
            ],
            [
                [
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
                    'method_public_abstract_static',
                    'method_protected_static',
                    'method_protected_abstract_static',
                    'method_private_static',
                    'method_public',
                    'method_public_abstract',
                    'method_protected',
                    'method_protected_abstract',
                    'method_private',
                ],
                <<<'EOT'
<?php

abstract class Foo
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
    abstract public static function absPubStatFunc();
    protected static function protStatFunc() {}
    abstract protected static function absProtStatFunc();
    private static function privStatFunc() {}
    public function pubFunc1() {}
    function pubFunc2() {}
    public function pubFunc3() {}
    abstract public function absPubFunc();
    protected function protFunc() {}
    abstract protected function absProtFunc();
    private function privFunc() {}
}
EOT
                ,
                <<<'EOT'
<?php

abstract class Foo
{
    abstract public function absPubFunc();
    private static function privStatFunc() {}
    protected static $protStatProp;
    public static $pubStatProp1;
    public function pubFunc1() {}
    abstract protected function absProtFunc();
    use BarTrait;
    public $pubProp1;
    public function __toString() {}
    protected function protFunc() {}
    protected $protProp;
    function pubFunc2() {}
    abstract protected static function absProtStatFunc();
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
    abstract public static function absPubStatFunc();
    protected function __construct() {}
    const C2 = 2;
    public static function pubStatFunc3() {}
    public $pubProp3;
    protected static function protStatFunc() {}
}
EOT
                ,
            ],
            [
                [
                    'method_public',
                    'method_abstract',
                ],
                <<<'EOT'
<?php

abstract class Foo
{
    public function test2(){}
    public abstract function test1();
}
EOT
                ,
                <<<'EOT'
<?php

abstract class Foo
{
    public abstract function test1();
    public function test2(){}
}
EOT
                ,
            ],
        ];
    }

    /**
     * @param array<string, mixed> $configuration
     *
     * @dataProvider provideSortingConfigurationCases
     */
    public function testFixWithSortingAlgorithm(array $configuration, string $expected, string $input): void
    {
        $this->fixer->configure($configuration);
        $this->doTest($expected, $input);
    }

    public function provideSortingConfigurationCases(): array
    {
        return [
            [
                [
                    'order' => [
                        'property_public_static',
                        'method_public',
                        'method_private',
                    ],
                    'sort_algorithm' => OrderedClassElementsFixer::SORT_ALPHA,
                ],
                <<<'EOT'
<?php
class Example
{
    public static $pubStatProp1;
    public static $pubStatProp2;
    public function A(){}
    public function B1(){}
    public function B2(){}
    public function C(){}
    public function C1(){}
    public function D(){}
    private function E(){}
}
EOT
                ,
                <<<'EOT'
<?php
class Example
{
    public function D(){}
    public static $pubStatProp2;
    public function B1(){}
    public function B2(){}
    private function E(){}
    public static $pubStatProp1;
    public function A(){}
    public function C(){}
    public function C1(){}
}
EOT
                ,
            ],
            [
                [
                    'order' => [
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
                    ],
                    'sort_algorithm' => OrderedClassElementsFixer::SORT_ALPHA,
                ],
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
    public function __magicA() {}
    public function __magicB() {}
    public function __toString() {}
    public static function pubStatFunc1() {}
    static function pubStatFunc2() {}
    public static function pubStatFunc3() {
        return $this->privFunc();
    }
    protected static function protStatFunc() {}
    private static function privStatFunc() {}
    public function pubFunc1() {}
    function pubFunc2() {}
    public function pubFunc3(int $b, int $c) {
        $a = $b*$c;

        return $a % 4;
    }
    protected function protFunc() {}
    private function privFunc() {}
}
EOT
                ,
                <<<'EOT'
<?php
class Foo
{
    private static function privStatFunc() {}
    protected static $protStatProp;
    use BazTrait;
    public static $pubStatProp2;
    public $pubProp3;
    use BarTrait;
    public function __toString() {}
    protected function protFunc() {}
    protected $protProp;
    function pubFunc2() {}
    public $pubProp1;
    public function __destruct() {}
    var $pubProp2;
    public function __magicB() {}
    const C2 = 2;
    public static $pubStatProp1;
    public function __magicA() {}
    private static $privStatProp;
    static function pubStatFunc2() {}
    public function pubFunc3(int $b, int $c) {
        $a = $b*$c;

        return $a % 4;
    }
    private $privProp;
    const C1 = 1;
    public static function pubStatFunc3() {
        return $this->privFunc();
    }
    public function pubFunc1() {}
    public static function pubStatFunc1() {}
    private function privFunc() {}
    protected function __construct() {}
    protected static function protStatFunc() {}
}
EOT
                ,
            ],
            [
                [
                    'order' => [
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
                        'method_public_abstract_static',
                        'method_protected_static',
                        'method_protected_abstract_static',
                        'method_private_static',
                        'method_public',
                        'method_public_abstract',
                        'method_protected',
                        'method_protected_abstract',
                        'method_private',
                    ],
                    'sort_algorithm' => OrderedClassElementsFixer::SORT_ALPHA,
                ],
                <<<'EOT'
<?php
abstract class Foo
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
    public function __magicA() {}
    public function __magicB() {}
    public function __toString() {}
    public static function pubStatFunc1() {}
    static function pubStatFunc2() {}
    public static function pubStatFunc3() {
        return $this->privFunc();
    }
    abstract public static function absPubStatFunc1();
    protected static function protStatFunc() {}
    abstract protected static function absProtStatFunc();
    private static function privStatFunc() {}
    public function pubFunc1() {}
    function pubFunc2() {}
    public function pubFunc3(int $b, int $c) {
        $a = $b*$c;

        return $a % 4;
    }
    abstract public function absPubFunc();
    protected function protFunc() {}
    abstract protected function absProtFunc();
    private function privFunc() {}
}
EOT
                ,
                <<<'EOT'
<?php
abstract class Foo
{
    private static function privStatFunc() {}
    protected static $protStatProp;
    use BazTrait;
    abstract public function absPubFunc();
    public static $pubStatProp2;
    public $pubProp3;
    use BarTrait;
    public function __toString() {}
    protected function protFunc() {}
    protected $protProp;
    function pubFunc2() {}
    public $pubProp1;
    abstract protected static function absProtStatFunc();
    public function __destruct() {}
    var $pubProp2;
    public function __magicB() {}
    const C2 = 2;
    public static $pubStatProp1;
    public function __magicA() {}
    private static $privStatProp;
    static function pubStatFunc2() {}
    public function pubFunc3(int $b, int $c) {
        $a = $b*$c;

        return $a % 4;
    }
    private $privProp;
    const C1 = 1;
    abstract protected function absProtFunc();
    public static function pubStatFunc3() {
        return $this->privFunc();
    }
    public function pubFunc1() {}
    public static function pubStatFunc1() {}
    private function privFunc() {}
    protected function __construct() {}
    protected static function protStatFunc() {}
    abstract public static function absPubStatFunc1();
}
EOT
                ,
            ],
            [
                [],
                <<<'EOT'
<?php

class Foo
{
    const C2 = 2;
    public const C1 = 1;
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
    const C2 = 2;
    public const C1 = 1;
    protected const C4 = 4;
    public const C3 = 3;
}
EOT
            ],
            [
                ['sort_algorithm' => OrderedClassElementsFixer::SORT_ALPHA],
                <<<'EOT'
<?php

class Foo
{
    public const C1 = 1;
    const C2 = 2;
    public const C3b = 3;
    protected const C4a = 4;
    private const C5 = 5;
}
EOT
                , <<<'EOT'
<?php

class Foo
{
    private const C5 = 5;
    const C2 = 2;
    public const C1 = 1;
    protected const C4a = 4;
    public const C3b = 3;
}
EOT
            ],
        ];
    }

    /**
     * @param array<string, mixed> $configuration
     *
     * @dataProvider provideFix74Cases
     */
    public function testFix74(string $expected, ?string $input = null, array $configuration = []): void
    {
        $this->fixer->configure($configuration);

        $this->doTest($expected, $input);
    }

    public function provideFix74Cases(): iterable
    {
        yield [
            '<?php
            class Foo {
                public iterable $baz;
                var ? Foo\Bar $qux;
                protected string $bar;
                private ?int $foo;
            }',
            '<?php
            class Foo {
                private ?int $foo;
                protected string $bar;
                public iterable $baz;
                var ? Foo\Bar $qux;
            }',
        ];

        yield [
            '<?php
            class Foo {
                public string $bar;
                public array $baz;
                public ?int $foo;
                public ? Foo\Bar $qux;
            }',
            '<?php
            class Foo {
                public array $baz;
                public ? Foo\Bar $qux;
                public string $bar;
                public ?int $foo;
            }',
            [
                'sort_algorithm' => OrderedClassElementsFixer::SORT_ALPHA,
            ],
        ];
    }

    public function testWrongConfig(): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageMatches('/^\[ordered_class_elements\] Invalid configuration: The option "order" .*\.$/');

        $this->fixer->configure(['order' => ['foo']]);
    }

    /**
     * @dataProvider provideWithConfigWithNoCandidateCases
     */
    public function testWithConfigWithNoCandidate(string $methodName1, string $methodName2): void
    {
        $template = '<?php
class TestClass
{
    public function %s(){}
    public function %s(){}
}';
        $this->fixer->configure(['order' => ['use_trait'], 'sort_algorithm' => OrderedClassElementsFixer::SORT_ALPHA]);
        $this->doTest(
            sprintf($template, $methodName2, $methodName1),
            sprintf($template, $methodName1, $methodName2)
        );
    }

    public function provideWithConfigWithNoCandidateCases(): iterable
    {
        yield ['z', '__construct'];

        yield ['z', '__destruct'];

        yield ['z', '__sleep'];

        yield ['z', 'abc'];
    }

    /**
     * @dataProvider provideFix80Cases
     *
     * @requires PHP 8.0
     */
    public function testFix80(string $expected, string $input): void
    {
        $this->doTest($expected, $input);
    }

    public function provideFix80Cases(): iterable
    {
        yield [
            '<?php

trait TestTrait
{
    abstract static public function abstractStaticPublic();
    abstract private function abstractPrivate();
    abstract static private function abstractStaticPrivate();
}
',
            '<?php

trait TestTrait
{
    abstract private function abstractPrivate();
    abstract static private function abstractStaticPrivate();
    abstract static public function abstractStaticPublic();
}
',
        ];

        yield [
            '<?php class Foo
            {

                #[PublicBarAttribute0]
                public int $bar = 1;

                #[PublicAttribute0]
                #[PublicAttribute1]
                #[PublicAttribute2]
                public function fooPublic()
                {
                }
                #[PrivateAttribute0]
                private function fooPrivate()
                {
                }
            }',
            '<?php class Foo
            {
                #[PrivateAttribute0]
                private function fooPrivate()
                {
                }

                #[PublicBarAttribute0]
                public int $bar = 1;

                #[PublicAttribute0]
                #[PublicAttribute1]
                #[PublicAttribute2]
                public function fooPublic()
                {
                }
            }',
        ];
    }

    /**
     * @param array<string, mixed> $configuration
     *
     * @dataProvider provideFix81Cases
     *
     * @requires PHP 8.1
     */
    public function testFix81(string $expected, ?string $input = null, array $configuration = []): void
    {
        $this->fixer->configure($configuration);

        $this->doTest($expected, $input);
    }

    public function provideFix81Cases(): iterable
    {
        yield [
            '<?php

class A
{
    readonly public string $publicProp0;
    public readonly string $publicProp1;
    public string $pubProp2;
    protected readonly string $protectedProp0;
    readonly protected string $protectedProp1;
    readonly private string $privateProp0;
    private readonly string $privateProp1;
}
',
            '<?php

class A
{
    public string $pubProp2;
    readonly public string $publicProp0;
    public readonly string $publicProp1;
    private readonly string $privateProp1;
    readonly private string $privateProp0;
    protected readonly string $protectedProp0;
    readonly protected string $protectedProp1;
}
',
            ['order' => ['property_public_readonly', 'property_public', 'property_protected_readonly', 'property_private_readonly'], 'sort_algorithm' => 'alpha'],
        ];

        yield [
            '<?php

 enum A: int
 {
     case Foo = 1;
     case Bar = 2;
     private const C1 = 1;
     function qux() {
         switch (true) {
             case 1: break;
         }
     }
 }
 ',
            '<?php

 enum A: int
 {
     private const C1 = 1;
     case Foo = 1;
     function qux() {
         switch (true) {
             case 1: break;
         }
     }
     case Bar = 2;
 }
 ',
        ];
    }

    /**
     * @dataProvider provideFix82Cases
     *
     * @requires PHP 8.2
     */
    public function testFix82(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function provideFix82Cases(): iterable
    {
        yield [
            '<?php trait Foo { const C1 = 1; protected $abc = "abc"; }',
            '<?php trait Foo { protected $abc = "abc"; const C1 = 1; }',
        ];
    }
}
