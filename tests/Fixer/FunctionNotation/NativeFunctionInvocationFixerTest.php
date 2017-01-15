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

use PhpCsFixer\Test\AbstractFixerTestCase;

/**
 * @author Andreas Möller <am@localheinz.com>
 *
 * @internal
 */
final class NativeFunctionInvocationFixerTest extends AbstractFixerTestCase
{
    public function testIsRisky()
    {
        $fixer = $this->createFixer();

        $this->assertTrue($fixer->isRisky());
    }

    /**
     * @dataProvider provideCasesNotWithinNamespace
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixNotWithinNamespace($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return array
     */
    public function provideCasesNotWithinNamespace()
    {
        return array(
            array(
'<?php

json_encode($foo);
',
            ),
            array(
'<?php

class WithoutNamespace
{
    public function bar($foo)
    {
        return json_encode($foo);
    }
}
',
            ),
            array(
'<?php

namespace OneNamespaceWithBraces {}

json_encode($foo);

namespace AnotherNamespaceWithBraces {}
',
            ),
        );
    }

    /**
     * @dataProvider provideCasesWithinNamespace
     *
     * @param string      $expected
     * @param null|string $input
     */
    public function testFixWithinNamespace($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return array
     */
    public function provideCasesWithinNamespace()
    {
        return array(
            array(
'<?php

namespace WithoutClassPrefixed;

if (isset($foo)) {
    \json_encode($foo);
}
',
            ),
            array(
'<?php

namespace WithoutClassNotPrefixed;

if (isset($foo)) {
    \json_encode($foo);
}
',
'<?php

namespace WithoutClassNotPrefixed;

if (isset($foo)) {
    json_encode($foo);
}
',
            ),
            array(
'<?php

namespace Foo;

class WithClassPrefixed
{
    public function baz($foo)
    {
        if (isset($foo)) {
            \json_encode($foo);
        }
    }
}',
            ),
            array(
'<?php

namespace WithClassNotPrefixed;

class Bar
{
    public function baz($foo)
    {
        if (isset($foo)) {
            \json_encode($foo);
        }
    }
}',
'<?php

namespace WithClassNotPrefixed;

class Bar
{
    public function baz($foo)
    {
        if (isset($foo)) {
            json_encode($foo);
        }
    }
}',
            ),
            array(
'<?php

namespace OneNamespaceWithBraces {}

namespace WithoutClassInNamespaceWithBracesNotPrefixed
{
    if (isset($foo)) {
        \json_encode($foo);
    }
}',
            ),
            array(
'<?php

namespace OneNamespaceWithBraces {}

namespace WithoutClassInNamespaceWithBracesPrefixed
{
    if (isset($foo)) {
        \json_encode($foo);
    }
}
',
'<?php

namespace OneNamespaceWithBraces {}

namespace WithoutClassInNamespaceWithBracesPrefixed
{
    if (isset($foo)) {
        json_encode($foo);
    }
}
',
            ),
        );
    }
}
