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
 * @author Gregor Harlan <gharlan@web.de>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Operator\UnaryOperatorSpacesFixer
 */
final class UnaryOperatorSpacesFixerTest extends AbstractFixerTestCase
{
    /**
     * @param array<string, mixed> $configuration
     *
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null, array $configuration = []): void
    {
        $this->fixer->configure($configuration);
        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): iterable
    {
        yield [
            '<?php $a= 1;$a#
++#
;#',
        ];

        yield [
            '<?php $a++;',
            '<?php $a ++;',
        ];

        yield [
            '<?php $a--;',
            '<?php $a --;',
        ];

        yield [
            '<?php ++$a;',
            '<?php ++ $a;',
        ];

        yield [
            '<?php --$a;',
            '<?php -- $a;',
        ];

        yield [
            '<?php $a = !$b;',
            '<?php $a = ! $b;',
        ];

        yield [
            '<?php $a = !!$b;',
            '<?php $a = ! ! $b;',
        ];

        yield [
            '<?php $a = ~$b;',
            '<?php $a = ~ $b;',
        ];

        yield [
            '<?php $a = &$b;',
            '<?php $a = & $b;',
        ];

        yield [
            '<?php $a=&$b;',
        ];

        yield [
            '<?php $a * -$b;',
            '<?php $a * - $b;',
        ];

        yield [
            '<?php $a *-$b;',
            '<?php $a *- $b;',
        ];

        yield [
            '<?php $a*-$b;',
        ];

        yield [
            '<?php function &foo(){}',
            '<?php function & foo(){}',
        ];

        yield [
            '<?php function &foo(){}',
            '<?php function &   foo(){}',
        ];

        yield [
            '<?php function foo(&$a, array &$b, Bar &$c) {}',
            '<?php function foo(& $a, array & $b, Bar & $c) {}',
        ];

        yield [
            '<?php function foo($a, ...$b) {}',
            '<?php function foo($a, ... $b) {}',
        ];

        yield [
            '<?php function foo(&...$a) {}',
            '<?php function foo(& ... $a) {}',
        ];

        yield [
            '<?php function foo(array ...$a) {}',
        ];

        yield [
            '<?php foo(...$a);',
            '<?php foo(... $a);',
        ];

        yield [
            '<?php foo($a, ...$b);',
            '<?php foo($a, ... $b);',
        ];

        yield [
            '<?php function foo($a, ...$b) { return (--$a) * ($b++);}',
            '<?php function foo($a, ...   $b) { return (--   $a) * ($b   ++);}',
            ['only_dec_inc' => false],
        ];

        yield [
            '<?php function foo($a, ...   $b) { return (--$a) * ($b++);}',
            '<?php function foo($a, ...   $b) { return (--   $a) * ($b   ++);}',
            ['only_dec_inc' => true],
        ];

        yield [
            '<?php static fn(Token $t): bool => 8 === ($t->flags & 8);',
        ];
    }
}
