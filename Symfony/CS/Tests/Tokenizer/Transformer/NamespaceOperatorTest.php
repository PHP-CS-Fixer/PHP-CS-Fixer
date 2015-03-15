<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Tokenizer\Transformer;

use Symfony\CS\Tests\Tokenizer\AbstractTransformerTestBase;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Gregor Harlan <gharlan@web.de>
 */
class NamespaceOperatorTest extends AbstractTransformerTestBase
{
    /**
     * @dataProvider provideProcessCases
     */
    public function testProcess($source, array $expectedTokens)
    {
        $tokens = Tokens::fromCode($source);

        foreach ($expectedTokens as $index => $name) {
            $this->assertSame(constant($name), $tokens[$index]->getId());
            $this->assertSame($name, $tokens[$index]->getName());
        }
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
