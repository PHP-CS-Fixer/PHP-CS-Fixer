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

/**
 * @author Jonathan Gruber <gruberjonathan@gmail.com>
 *
 * @internal
 * @coversNothing
 */
final class PhpdocParamOrderFixerTest extends AbstractFixerTestCase
{
    public function testNoChanges()
    {
        $expected = <<<'EOT'
<?php
class C {
    /**
     * @param $a
     */
    public function m($a) {}
}
EOT;
        $this->doTest($expected);
    }

    public function testNoChangesMultiple()
    {
        $expected = <<<'EOT'
<?php
class C {
    /**
     * @param string $a
     * @param bool   $b
     */
    public function m($a, $b) {}
}
EOT;
        $this->doTest($expected);
    }

    public function testOnlyParamsUntyped()
    {
        $expected = <<<'EOT'
<?php
class C {
    /**
     * @param $a
     * @param $b
     * @param $c
     * @param $d
     * @param $e
     */
    public function m($a, $b, $c, $d, $e) {}
}
EOT;
        $input = <<<'EOT'
<?php
class C {
    /**
     * @param $b
     * @param $e
     * @param $a
     * @param $c
     * @param $d
     */
    public function m($a, $b, $c, $d, $e) {}
}
EOT;
        $this->doTest($expected, $input);
    }

    public function testOnlyParamsUntypedMixed()
    {
        $expected = <<<'EOT'
<?php
class C {
    /**
     * @param int $a
     * @param $b
     * @param $c
     * @param bool $d
     * @param $e
     */
    public function m($a, $b, $c, $d, $e) {}
}
EOT;
        $input = <<<'EOT'
<?php
class C {
    /**
     * @param $c
     * @param $e
     * @param int $a
     * @param $b
     * @param bool $d
     */
    public function m($a, $b, $c, $d, $e) {}
}
EOT;
        $this->doTest($expected, $input);
    }

    public function testOnlyParamsTyped()
    {
        $expected = <<<'EOT'
<?php
class C {
    /**
     * @param string $a
     * @param bool   $b
     * @param string $c
     * @param string $d
     * @param int $e
     */
    public function m($a, $b, $c, $d, $e) {}
}
EOT;
        $input = <<<'EOT'
<?php
class C {
    /**
     * @param bool   $b
     * @param string $a
     * @param string $c
     * @param int $e
     * @param string $d
     */
    public function m($a, $b, $c, $d, $e) {}
}
EOT;
        $this->doTest($expected, $input);
    }

    public function testParamsUntyped()
    {
        $expected = <<<'EOT'
<?php
class C {
    /**
     * Some function
     *
     * @param $a
     * @param $b
     * @param $c
     *
     * @throws \Exception
     *
     * @return bool
     */
    public function m($a, $b, $c, $d, $e) {}
}
EOT;
        $input = <<<'EOT'
<?php
class C {
    /**
     * Some function
     *
     * @param $b
     * @param $c
     * @param $a
     *
     * @throws \Exception
     *
     * @return bool
     */
    public function m($a, $b, $c, $d, $e) {}
}
EOT;
        $this->doTest($expected, $input);
    }

    public function testParamsTyped()
    {
        $expected = <<<'EOT'
<?php
class C {
    /**
     * Some function
     *
     * @param Foo $a
     * @param int $b
     * @param bool $c
     *
     * @throws \Exception
     *
     * @return bool
     */
    public function m($a, $b, $c, $d, $e) {}
}
EOT;
        $input = <<<'EOT'
<?php
class C {
    /**
     * Some function
     *
     * @param int $b
     * @param bool $c
     * @param Foo $a
     *
     * @throws \Exception
     *
     * @return bool
     */
    public function m($a, $b, $c, $d, $e) {}
}
EOT;
        $this->doTest($expected, $input);
    }

    public function testParamsDescription()
    {
        $expected = <<<'EOT'
<?php
class C {
    /**
     * Some function
     *
     * @param Foo $a A parameter
     * @param int $b B parameter
     * @param bool $c C parameter
     *
     * @throws \Exception
     *
     * @return bool
     */
    public function m($a, $b, $c, $d, $e) {}
}
EOT;
        $input = <<<'EOT'
<?php
class C {
    /**
     * Some function
     *
     * @param int $b B parameter
     * @param bool $c C parameter
     * @param Foo $a A parameter
     *
     * @throws \Exception
     *
     * @return bool
     */
    public function m($a, $b, $c, $d, $e) {}
}
EOT;
        $this->doTest($expected, $input);
    }

    public function testParamsMultilineDescription()
    {
        $expected = <<<'EOT'
<?php
class C {
    /**
     * Some function
     *
     * @param Foo $a A parameter
     * @param int $b B parameter
     * @param bool $c Another multiline, longer
     *                description of C parameter
     * @param bool $d Multiline description
     *                of D parameter
     *
     * @throws \Exception
     *
     * @return bool
     */
    public function m($a, $b, $c, $d) {}
}
EOT;
        $input = <<<'EOT'
<?php
class C {
    /**
     * Some function
     *
     * @param int $b B parameter
     * @param bool $d Multiline description
     *                of D parameter
     * @param bool $c Another multiline, longer
     *                description of C parameter
     * @param Foo $a A parameter
     *
     * @throws \Exception
     *
     * @return bool
     */
    public function m($a, $b, $c, $d) {}
}
EOT;
        $this->doTest($expected, $input);
    }

    public function testComplexTypes()
    {
        $expected = <<<'EOT'
<?php
class C {
    /**
     * @param Foo[]|\Bar\Baz $a
     * @param Foo|Bar $b
     * @param array<int, FooInterface>|string $c
     * @param array<[int, int]> $d
     * @param ?Foo $e
     */
    public function m($a, $b, $c, $d, $e) {}
}
EOT;
        $input = <<<'EOT'
<?php
class C {
    /**
     * @param array<int, FooInterface>|string $c
     * @param Foo|Bar $b
     * @param array<[int, int]> $d
     * @param ?Foo $e
     * @param Foo[]|\Bar\Baz $a
     */
    public function m($a, $b, $c, $d, $e) {}
}
EOT;
        $this->doTest($expected, $input);
    }

    public function testOnlyParamsUndocumented()
    {
        $expected = <<<'EOT'
<?php
class C {
    /**
     * @param $a
     * @param $b
     * @param $c
     * @param $d
     */
    public function m($a, $b, $c, $d, $e, $f) {}
}
EOT;
        $input = <<<'EOT'
<?php
class C {
    /**
     * @param $a
     * @param $c
     * @param $d
     * @param $b
     */
    public function m($a, $b, $c, $d, $e, $f) {}
}
EOT;
        $this->doTest($expected, $input);
    }

    public function testOnlyParamsSuperfluousAnnotation()
    {
        $expected = <<<'EOT'
<?php
class C {
    /**
     * @param $a
     * @param $b
     * @param $c
     * @param $superfluous
     */
    public function m($a, $b, $c) {}
}
EOT;
        $input = <<<'EOT'
<?php
class C {
    /**
     * @param $a
     * @param $superfluous
     * @param $b
     * @param $c
     */
    public function m($a, $b, $c) {}
}
EOT;
        $this->doTest($expected, $input);
    }

    public function testOnlyParamsSuperfluousAnnotations()
    {
        $expected = <<<'EOT'
<?php
class C {
    /**
     * @param $a
     * @param $b
     * @param $c
     * @param $superfluous2
     * @param $superfluous1
     * @param $superfluous3
     */
    public function m($a, $b, $c) {}
}
EOT;
        $input = <<<'EOT'
<?php
class C {
    /**
     * @param $a
     * @param $superfluous2
     * @param $b
     * @param $superfluous1
     * @param $c
     * @param $superfluous3
     */
    public function m($a, $b, $c) {}
}
EOT;
        $this->doTest($expected, $input);
    }

    public function testParamsBlankLines()
    {
        $expected = <<<'EOT'
<?php
class C {
    /**
     * Some function
     *
     * @param $a
     * @param $b
     *
     * @param $c
     *
     *
     * @param $d
     *
     * @param $e
     *
     * @throws \Exception
     *
     * @return bool
     */
    public function m($a, $b, $c, $d, $e) {}
}
EOT;
        $input = <<<'EOT'
<?php
class C {
    /**
     * Some function
     *
     * @param $b
     * @param $e
     *
     * @param $c
     *
     *
     * @param $a
     *
     * @param $d
     *
     * @throws \Exception
     *
     * @return bool
     */
    public function m($a, $b, $c, $d, $e) {}
}
EOT;
        $this->doTest($expected, $input);
    }

    public function testNestedPhpdoc()
    {
        $expected = <<<'EOT'
<?php

/**
 * @param string[] $array
 * @param callable $callback {
 *     @param string $value
 *     @param int    $key
 *     @param mixed  $userdata
 * }
 * @param mixed    $userdata
 *
 * @return bool
 */
function string_array_walk(array &$array, callable $callback, $userdata = null) {}
EOT;

        $input = <<<'EOT'
<?php

/**
 * @param callable $callback {
 *     @param string $value
 *     @param int    $key
 *     @param mixed  $userdata
 * }
 * @param mixed    $userdata
 * @param string[] $array
 *
 * @return bool
 */
function string_array_walk(array &$array, callable $callback, $userdata = null) {}
EOT;

        $this->doTest($expected, $input);
    }

    public function testMultiNestedPhpdoc()
    {
        $expected = <<<'EOT'
<?php

/**
 * @param string[] $a
 * @param callable $b {
 *     @param string   $a
 *     @param callable {
 *         @param string   $d
 *         @param int      $a
 *         @param callable $c {
 *             $param string $e
 *         }
 *     }
 *     @param mixed    $b2
 * }
 * @param mixed    $c
 * @param int      $d
 *
 * @return bool
 */
function m(array &$a, callable $b, $c = null, $d) {}
EOT;

        $input = <<<'EOT'
<?php

/**
 * @param mixed    $c
 * @param callable $b {
 *     @param string   $a
 *     @param callable {
 *         @param string   $d
 *         @param int      $a
 *         @param callable $c {
 *             $param string $e
 *         }
 *     }
 *     @param mixed    $b2
 * }
 * @param int      $d
 * @param string[] $a
 *
 * @return bool
 */
function m(array &$a, callable $b, $c = null, $d) {}
EOT;

        $this->doTest($expected, $input);
    }

    public function testMultipleNestedPhpdoc()
    {
        $expected = <<<'EOT'
<?php

/**
 * @param string[] $array
 * @param callable $callback {
 *     @param string $value
 *     @param int    $key
 *     @param mixed  $userdata {
 *         $param array $array
 *     }
 * }
 * @param mixed    $userdata
 * @param callable $foo {
 *     @param callable {
 *         @param string $inner1
 *         @param int    $inner2
 *     }
 *     @param mixed  $userdata
 * }
 * @param $superflous1 Superflous
 * @param $superflous2 Superflous
 *
 * @return bool
 */
function string_array_walk(array &$array, callable $callback, $userdata = null, $foo) {}
EOT;

        $input = <<<'EOT'
<?php

/**
 * @param $superflous1 Superflous
 * @param callable $callback {
 *     @param string $value
 *     @param int    $key
 *     @param mixed  $userdata {
 *         $param array $array
 *     }
 * }
 * @param $superflous2 Superflous
 * @param callable $foo {
 *     @param callable {
 *         @param string $inner1
 *         @param int    $inner2
 *     }
 *     @param mixed  $userdata
 * }
 * @param mixed    $userdata
 * @param string[] $array
 *
 * @return bool
 */
function string_array_walk(array &$array, callable $callback, $userdata = null, $foo) {}
EOT;

        $this->doTest($expected, $input);
    }

    public function testNonMatchingParamName()
    {
        $expected = <<<'EOT'
<?php
/**
 * @param Foo $fooBar
 * @param $fooSomethingNotMatchingTheName
 * @param OtherClassLorem $x
 */
function f(Foo $fooBar, Payment $foo, OtherClassLoremIpsum $y) {}
EOT;

        $input = <<<'EOT'
<?php
/**
 * @param $fooSomethingNotMatchingTheName
 * @param Foo $fooBar
 * @param OtherClassLorem $x
 */
function f(Foo $fooBar, Payment $foo, OtherClassLoremIpsum $y) {}
EOT;

        $this->doTest($expected, $input);
    }

    public function testPlainFunction()
    {
        $expected = <<<'EOT'
<?php
/**
 * A plain function
 *
 * @param $a
 * @param $b
 * @param $c
 * @param $d
 */
function m($a, $b, $c, $d) {}
EOT;
        $input = <<<'EOT'
<?php
/**
 * A plain function
 *
 * @param $c
 * @param $b
 * @param $d
 * @param $a
 */
function m($a, $b, $c, $d) {}
EOT;
        $this->doTest($expected, $input);
    }

    public function testCommentsInSignature()
    {
        $expected = <<<'EOT'
<?php
class C {
    /**
     * @param $a
     * @param $b
     * @param $c
     * @param $d
     */
    public/*1*/function/*2*/m/*3*/(/*4*/$a, $b,/*5*/$c, $d){}
}
EOT;
        $input = <<<'EOT'
<?php
class C {
    /**
     * @param $d
     * @param $a
     * @param $b
     * @param $c
     */
    public/*1*/function/*2*/m/*3*/(/*4*/$a, $b,/*5*/$c, $d){}
}
EOT;
        $this->doTest($expected, $input);
    }

    public function testPhp7()
    {
        $expected = <<<'EOT'
<?php
class C {
    /**
     * @param array $a
     * @param $b
     * @param bool $c
     */
    public function m(array $a, $b, bool $c) {}
}
EOT;
        $input = <<<'EOT'
<?php
class C {
    /**
     * @param $b
     * @param bool $c
     * @param array $a
     */
    public function m(array $a, $b, bool $c) {}
}
EOT;
        $this->doTest($expected, $input);
    }
}
