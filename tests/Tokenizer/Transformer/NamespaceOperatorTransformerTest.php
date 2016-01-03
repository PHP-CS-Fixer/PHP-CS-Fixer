<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Tests\Tokenizer\Transformer;

use PhpCsFixer\Test\AbstractTransformerTestCase;

/**
 * @author Gregor Harlan <gharlan@web.de>
 *
 * @internal
 */
final class NamespaceOperatorTransformerTest extends AbstractTransformerTestCase
{
    /**
     * @dataProvider provideProcessCases
     */
    public function testProcess($source, array $expectedTokens)
    {
        $this->doTest($source, $expectedTokens);
    }

    public function provideProcessCases()
    {
        return array(
            array(
                '<?php
namespace Foo;
namespace\Bar\baz();
',
                array(
                    1 => 'T_NAMESPACE',
                    6 => 'CT_NAMESPACE_OPERATOR',
                ),
            ),
        );
    }
}
