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

        $this->setExpectedException(\PhpCsFixer\ConfigurationException\InvalidConfigurationException::class, sprintf(
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
        $this->setExpectedException(\PhpCsFixer\ConfigurationException\InvalidConfigurationException::class, sprintf(
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
     * @dataProvider provideFixWithNamespaceConfigurationCases
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithNamespaceConfiguration($expected, $input = null)
    {
        $this->fixer->configure(['scope' => 'namespaced']);
        $this->doTest($expected, $input);
    }

    public function provideFixWithNamespaceConfigurationCases()
    {
        return [
            [
                '<?php echo count([1]);',
            ],
            [
                '<?php
namespace space1 { ?>
<?php echo \count([2]) ?>
<?php }namespace {echo count([1]);}
',
                '<?php
namespace space1 { ?>
<?php echo count([2]) ?>
<?php }namespace {echo count([1]);}
',
            ],
            [
                '<?php
namespace Bar {
    echo \strtoLOWER("in 1");
}

namespace {
    echo strtolower("out 1");
}

namespace {
    echo strtolower("out 2");
}

namespace Bar{
    echo \strtolower("in 2");
}

namespace {
    echo strtolower("out 3");
}
',
                '<?php
namespace Bar {
    echo strtoLOWER("in 1");
}

namespace {
    echo strtolower("out 1");
}

namespace {
    echo strtolower("out 2");
}

namespace Bar{
    echo strtolower("in 2");
}

namespace {
    echo strtolower("out 3");
}
',
            ],
            [
                '<?php
namespace space11 ?>

    <?php
echo \strtolower(__NAMESPACE__);
namespace space2;
echo \strtolower(__NAMESPACE__);
',
                '<?php
namespace space11 ?>

    <?php
echo strtolower(__NAMESPACE__);
namespace space2;
echo strtolower(__NAMESPACE__);
',
            ],
            [
                '<?php namespace PhpCsFixer\Tests\Fixer\Casing;\count([1]);',
                '<?php namespace PhpCsFixer\Tests\Fixer\Casing;count([1]);',
            ],
            [
                '<?php
namespace Space12;

echo \count([1]);

namespace Space2;

echo \count([1]);
?>
',
                '<?php
namespace Space12;

echo count([1]);

namespace Space2;

echo count([1]);
?>
',
            ],
            [
                '<?php namespace {echo strtolower("out 2");}',
            ],
            [
                '<?php
namespace space13 {
    echo \strtolower("in 1");
}

namespace space2 {
    echo \strtolower("in 2");
}

namespace { // global
    echo strtolower("global 1");
}
',
                '<?php
namespace space13 {
    echo strtolower("in 1");
}

namespace space2 {
    echo strtolower("in 2");
}

namespace { // global
    echo strtolower("global 1");
}
',
            ],
        ];
    }
}
