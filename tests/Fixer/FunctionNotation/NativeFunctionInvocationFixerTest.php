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

        $this->setExpectedException('PhpCsFixer\ConfigurationException\InvalidConfigurationException', sprintf(
            '[native_function_invocation] Invalid configuration: The option "%s" does not exist.',
            $key
        ));

        $this->fixer->configure(array(
            $key => 'bar',
        ));
    }

    /**
     * @dataProvider providerInvalidConfigurationElement
     *
     * @param mixed $element
     */
    public function testConfigureRejectsInvalidConfigurationElement($element)
    {
        $this->setExpectedException('PhpCsFixer\ConfigurationException\InvalidConfigurationException', sprintf(
            'Each element must be a non-empty, trimmed string, got "%s" instead.',
            \is_object($element) ? \get_class($element) : \gettype($element)
        ));

        $this->fixer->configure(array(
            'exclude' => array(
                $element,
            ),
        ));
    }

    /**
     * @return array
     */
    public function providerInvalidConfigurationElement()
    {
        return array(
            'null' => array(null),
            'false' => array(false),
            'true' => array(false),
            'int' => array(1),
            'array' => array(array()),
            'float' => array(0.1),
            'object' => array(new \stdClass()),
            'not-trimmed' => array('  json_encode  '),
        );
    }

    public function testConfigureResetsExclude()
    {
        $this->fixer->configure(array(
            'exclude' => array(
                'json_encode',
            ),
        ));

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

        $this->fixer->configure(array());

        $this->doTest($after, $before);
    }

    public function testIsRisky()
    {
        $fixer = $this->createFixer();

        $this->assertTrue($fixer->isRisky());
    }

    /**
     * @dataProvider provideCasesWithDefaultConfiguration
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
    public function provideCasesWithDefaultConfiguration()
    {
        return array(
            array(
'<?php

\json_encode($foo);
',
            ),
            array(
'<?php

\json_encode($foo);
',
'<?php

json_encode($foo);
',
            ),
            array(
'<?php

class Foo
{
    public function bar($foo)
    {
        return \json_encode($foo);
    }
}
',
            ),
            array(
'<?php

class Foo
{
    public function bar($foo)
    {
        return \json_encode($foo);
    }
}
',
'<?php

class Foo
{
    public function bar($foo)
    {
        return json_encode($foo);
    }
}
',
            ),
        );
    }

    /**
     * @dataProvider provideCasesWithConfiguredExclude
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithConfiguredExclude($expected, $input = null)
    {
        $this->fixer->configure(array(
            'exclude' => array(
                'json_encode',
            ),
        ));

        $this->doTest($expected, $input);
    }

    /**
     * @return array
     */
    public function provideCasesWithConfiguredExclude()
    {
        return array(
            array(
'<?php

json_encode($foo);
',
            ),
            array(
'<?php

class Foo
{
    public function bar($foo)
    {
        return json_encode($foo);
    }
}
',
            ),
        );
    }
}
