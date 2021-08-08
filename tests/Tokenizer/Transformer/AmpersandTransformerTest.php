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

namespace PhpCsFixer\Tests\Tokenizer\Transformer;

use PhpCsFixer\Tests\Test\AbstractTransformerTestCase;
use PhpCsFixer\Tokenizer\CT;

    /**
     * @internal
     *
     * @covers \PhpCsFixer\Tokenizer\Transformer\AmpersandTransformer
     */
    final class AmpersandTransformerTest extends AbstractTransformerTestCase
    {
        /**
         * @dataProvider provideProcessCases
         * @requires PHP 8.1
         */
        public function testProcess(string $source, array $expectedTokens): void
        {
            $this->doTest($source, $expectedTokens);
        }

        public function provideProcessCases()
        {
            yield [
                '<?php $foo & $bar;',
                [
                    3 => CT::T_AMPERSAND,
                ],
            ];

            yield [
                '<?php FOO & BAR;',
                [
                    3 => CT::T_AMPERSAND,
                ],
            ];

            yield [
                '<?php $foo &
                $bar;',
                [
                    3 => CT::T_AMPERSAND,
                ],
            ];

            yield [
                '<?php $foo
                & $bar;',
                [
                    3 => CT::T_AMPERSAND,
                ],
            ];
        }
    }
