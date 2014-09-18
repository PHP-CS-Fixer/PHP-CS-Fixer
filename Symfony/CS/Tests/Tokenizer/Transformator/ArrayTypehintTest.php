<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Tokenizer\Transformator;

use Symfony\CS\Tests\Tokenizer\AbstractTransformatorTestBase;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class ArrayTypehintTest extends AbstractTransformatorTestBase
{
    /**
     * @dataProvider provideProcessCases
     */
    public function testProcess($source, array $expectedTokens)
    {
        $tokens = Tokens::fromCode($source);

        foreach ($expectedTokens as $index => $name) {
            $this->assertSame(constant($name), $tokens[$index]->id);
            $this->assertSame($name, $tokens[$index]->getName());
        }
    }

    public function provideProcessCases()
    {
        return array(
            array(
                '<?php
$a = array(1, 2, 3);
function foo (array /** @type array */ $bar)
{
}',
                array(
                    5 => 'T_ARRAY',
                    22 => 'CT_ARRAY_TYPEHINT',
                ),
            ),
        );
    }
}
