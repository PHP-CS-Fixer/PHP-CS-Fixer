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

namespace PhpCsFixer\Tests\Fixer\Operator;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @requires PHP 8.0
 *
 * @covers \PhpCsFixer\Fixer\Operator\NoUselessNullsafeOperatorFixer
 */
final class NoUselessNullsafeOperatorFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases(): iterable
    {
        yield 'simple case + comment' => [
            '<?php $a = new class extends foo {
                public function bar() {
                    $this->g();
                    // $this?->g();
                }
            };',
            '<?php $a = new class extends foo {
                public function bar() {
                    $this?->g();
                    // $this?->g();
                }
            };',
        ];

        yield 'multiple casing cases + comment + no candidate' => [
            '<?php $a = new class extends foo {
                public function bar() {
                    return $THIS /*1*/ -> g().$THis->g().$this->do()?->o();
                }
            };',
            '<?php $a = new class extends foo {
                public function bar() {
                    return $THIS /*1*/ ?-> g().$THis?->g().$this->do()?->o();
                }
            };',
        ];
    }
}
