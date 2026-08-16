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

use PhpCsFixer\Fixer\AbstractLongToShorthandOperatorFixer;
use PhpCsFixer\Fixer\AbstractShortOperatorFixer;
use PhpCsFixer\Fixer\Operator\LongToShorthandOperatorForComplexTargetsFixer;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\AbstractLongToShorthandOperatorFixer
 * @covers \PhpCsFixer\Fixer\AbstractShortOperatorFixer
 * @covers \PhpCsFixer\Fixer\Operator\LongToShorthandOperatorForComplexTargetsFixer
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\Operator\LongToShorthandOperatorForComplexTargetsFixer>
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
#[CoversClass(AbstractLongToShorthandOperatorFixer::class)]
#[CoversClass(AbstractShortOperatorFixer::class)]
#[CoversClass(LongToShorthandOperatorForComplexTargetsFixer::class)]
final class LongToShorthandOperatorForComplexTargetsFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    #[DataProvider('provideFixCases')]
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<string, array{0: string, 1?: null|string}>
     */
    public static function provideFixCases(): iterable
    {
        // A plain variable target is out of scope here; it is handled by the
        // non-risky `long_to_shorthand_operator`.
        yield 'plain variable target left alone' => [
            '<?php $a = $a + 1;',
        ];

        // Offsets (why the rule is risky: a string offset makes PHP fatal at
        // runtime with "Cannot use assign-op operators with string offsets").
        yield 'string offset' => [
            '<?php $text[0] &= "\x7F";',
            '<?php $text[0] = $text[0] & "\x7F";',
        ];

        yield 'array offset' => [
            '<?php $a[1] += 2;',
            '<?php $a[1] = $a[1] + 2;',
        ];

        yield 'nested offsets' => [
            '<?php $a[1][2] -= 852;',
            '<?php $a[1][2] = $a[1][2] - 852;',
        ];

        yield 'offset on a property' => [
            '<?php $this->a[1] .= "x";',
            '<?php $this->a[1] = $this->a[1] . "x";',
        ];

        // Member access.
        yield 'property' => [
            '<?php $this->test += $i;',
            '<?php $this->test = $this->test + $i;',
        ];

        yield 'assign and return' => [
            '<?php
class Foo
{
    private int $test = 1;

    public function bar(int $i): int
    {
        return $this->test += $i;
    }
}',
            '<?php
class Foo
{
    private int $test = 1;

    public function bar(int $i): int
    {
        return $this->test = $this->test + $i;
    }
}',
        ];

        // The reason the rule is risky: the target is evaluated once instead of
        // twice, which changes behaviour when evaluating it has a side effect...
        yield 'side-effecting subscript' => [
            '<?php $a[f()] += 1;',
            '<?php $a[f()] = $a[f()] + 1;',
        ];

        // ...or invokes a magic accessor (no brackets involved).
        yield 'magic accessor in a chain' => [
            '<?php $obj->m->x += 1;',
            '<?php $obj->m->x = $obj->m->x + 1;',
        ];

        // variable-variable
        yield 'variable-variable' => [
            '<?php $$name += 1;',
            '<?php $$name = $$name + 1;',
        ];

        // reverse (commutative)
        yield 'array offset reverse' => [
            '<?php $ga[0] *= 5  ;',
            '<?php $ga[0] = 5 * $ga[0];',
        ];

        // do not fix; RHS is not (functionally) the same as the target
        yield 'not the same offset' => [
            '<?php $a[1] = $a[0] * 1;',
        ];

        yield 'parenthesized RHS offset' => [
            '<?php $b[0] *= 789;',
            '<?php $b[0] = ($b[0]) * 789;',
        ];
    }
}
