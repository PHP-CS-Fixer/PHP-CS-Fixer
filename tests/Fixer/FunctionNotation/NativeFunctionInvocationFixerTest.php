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

namespace PhpCsFixer\Tests\Fixer\FunctionNotation;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Andreas Möller <am@localheinz.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\FunctionNotation\NativeFunctionInvocationFixer
 */
final class NativeFunctionInvocationFixerTest extends AbstractFixerTestCase
{
    public function testConfigureRejectsUnknownConfigurationKey()
    {
        $key = 'foo';

        $this->expectException(\PhpCsFixer\ConfigurationException\InvalidConfigurationException::class);
        $this->expectExceptionMessage(sprintf(
            '[native_function_invocation] Invalid configuration: The option "%s" does not exist.',
            $key
        ));

        $this->fixer->configure([
            $key => 'bar',
        ]);
    }

    /**
     * @dataProvider provideInvalidConfigurationElementCases
     *
     * @param mixed $element
     */
    public function testConfigureRejectsInvalidConfigurationElement($element)
    {
        $this->expectException(\PhpCsFixer\ConfigurationException\InvalidConfigurationException::class);
        $this->expectExceptionMessage(sprintf(
            'Each element must be a non-empty, trimmed string, got "%s" instead.',
            \is_object($element) ? \get_class($element) : \gettype($element)
        ));

        $this->fixer->configure([
            'exclude' => [
                $element,
            ],
        ]);
    }

    /**
     * @return array
     */
    public function provideInvalidConfigurationElementCases()
    {
        return [
            'null' => [null],
            'false' => [false],
            'true' => [false],
            'int' => [1],
            'array' => [[]],
            'float' => [0.1],
            'object' => [new \stdClass()],
            'not-trimmed' => ['  json_encode  '],
        ];
    }

    public function testConfigureResetsExclude()
    {
        $this->fixer->configure([
            'exclude' => [
                'json_encode',
            ],
        ]);

        $before = <<<'PHP'
<?php

namespace WithClassNotPrefixed;

class Bar
{
    public function baz($foo)
    {
        if (isset($foo)) {
            json_encode($foo);
        }
    }
}
PHP;

        $after = <<<'PHP'
<?php

namespace WithClassNotPrefixed;

class Bar
{
    public function baz($foo)
    {
        if (isset($foo)) {
            \json_encode($foo);
        }
    }
}
PHP;

        $this->doTest($before);

        $this->fixer->configure([]);

        $this->doTest($after, $before);
    }

    public function testIsRisky()
    {
        $fixer = $this->createFixer();

        $this->assertTrue($fixer->isRisky());
    }

    /**
     * @dataProvider provideFixWithDefaultConfigurationCases
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithDefaultConfiguration($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return array
     */
    public function provideFixWithDefaultConfigurationCases()
    {
        return [
            [
                '<?php

\json_encode($foo);
',
            ],
            [
                '<?php

\json_encode($foo);
',
                '<?php

json_encode($foo);
',
            ],
            [
                '<?php

class Foo
{
    public function bar($foo)
    {
        return \json_encode($foo);
    }
}
',
            ],
            [
                '<?php

class Foo
{
    public function bar($foo)
    {
        return \JSON_ENCODE($foo);
    }
}
',
                '<?php

class Foo
{
    public function bar($foo)
    {
        return JSON_ENCODE($foo);
    }
}
',
            ],
            [
                '<?php
echo \/**/strlen($a);
echo \ strlen($a);
echo \#
#
strlen($a);
echo \strlen($a);
',
                '<?php
echo \/**/strlen($a);
echo \ strlen($a);
echo \#
#
strlen($a);
echo strlen($a);
',
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithConfiguredExcludeCases
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithConfiguredExclude($expected, $input = null)
    {
        $this->fixer->configure([
            'exclude' => [
                'json_encode',
            ],
        ]);

        $this->doTest($expected, $input);
    }

    /**
     * @return array
     */
    public function provideFixWithConfiguredExcludeCases()
    {
        return [
            [
                '<?php

json_encode($foo);
',
            ],
            [
                '<?php

class Foo
{
    public function bar($foo)
    {
        return json_encode($foo);
    }
}
',
            ],
        ];
    }

    /**
     * @dataProvider provideFixWithConfiguredOpcacheOnyCases
     *
     * @param string $expected
     * @param string $input
     */
    public function testFixWithConfiguredOpcacheOny($expected, $input)
    {
        $this->fixer->configure([
            'opcache-only' => true,
        ]);

        $this->doTest($expected, $input);
    }

    /**
     * @return array
     */
    public function provideFixWithConfiguredOpcacheOnyCases()
    {
        return [
            [
                '<?php

// These methods should not get prefixed
json_encode();
substr();
in_array();
in_array($foo, [$foo], false);

// These methods should get prefixed
\array_slice();
\assert();
\boolval();
\call_user_func();
\call_user_func_array();
\chr();
\count();
\defined();
\doubleval();
\floatval();
\func_get_args();
\func_num_args();
\get_called_class();
\get_class();
\gettype();
\in_array($foo, [$foo], true);
\intval();
\is_array();
\is_bool();
\is_double();
\is_float();
\is_int();
\is_integer();
\is_long();
\is_null();
\is_object();
\is_real();
\is_resource();
\is_string();
\ord();
\strlen();
\strval();
\function_exists();
\is_callable();
\extension_loaded();
\dirname();
\constant();
\define();
',
                '<?php

// These methods should not get prefixed
json_encode();
substr();
in_array();
in_array($foo, [$foo], false);

// These methods should get prefixed
array_slice();
assert();
boolval();
call_user_func();
call_user_func_array();
chr();
count();
defined();
doubleval();
floatval();
func_get_args();
func_num_args();
get_called_class();
get_class();
gettype();
in_array($foo, [$foo], true);
intval();
is_array();
is_bool();
is_double();
is_float();
is_int();
is_integer();
is_long();
is_null();
is_object();
is_real();
is_resource();
is_string();
ord();
strlen();
strval();
function_exists();
is_callable();
extension_loaded();
dirname();
constant();
define();
',
            ],
        ];
    }
}
