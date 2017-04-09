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

namespace PhpCsFixer\Tests\Tokenizer\Transformer;

use PhpCsFixer\Test\AbstractTransformerTestCase;
use PhpCsFixer\Tokenizer\CT;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Tokenizer\Transformer\NullableTypeTransformer
 */
final class NullableTypeTransformerTest extends AbstractTransformerTestCase
{
    /**
     * @param string $source
     *
     * @dataProvider provideProcessCases
     * @requires PHP 7.1
     */
    public function testProcess($source, array $expectedTokens = array())
    {
        $this->doTest(
            $source,
            $expectedTokens,
            array(
                CT::T_NULLABLE_TYPE,
            )
        );
    }

    public function provideProcessCases()
    {
        return array(
            array(
                '<?php function foo(?Barable $barA, ?Barable $barB): ?Fooable {}',
                array(
                    5 => CT::T_NULLABLE_TYPE,
                    11 => CT::T_NULLABLE_TYPE,
                    18 => CT::T_NULLABLE_TYPE,
                ),
            ),
            array(
                '<?php interface Fooable { function foo(): ?Fooable; }',
                array(
                    14 => CT::T_NULLABLE_TYPE,
                ),
            ),
            array(
                '<?php
                    $a = 1 ? "aaa" : "bbb";
                    $b = 1 ? fnc() : [];
                    $c = 1 ?: [];
                ',
            ),
        );
    }
}
