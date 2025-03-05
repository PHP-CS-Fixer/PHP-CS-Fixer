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
 * @covers \PhpCsFixer\Fixer\Operator\ObjectOperatorWithoutWhitespaceFixer
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\Operator\ObjectOperatorWithoutWhitespaceFixer>
 *
 * @author Farhad Safarov <farhad.safarov@gmail.com>
 */
final class ObjectOperatorWithoutWhitespaceFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<int, array{0: string, 1?: string}>
     */
    public static function provideFixCases(): iterable
    {
        yield [
            '<?php $object->method();',
            '<?php $object   ->method();',
        ];

        yield [
            '<?php $object->method();',
            '<?php $object   ->   method();',
        ];

        yield [
            '<?php $object->method();',
            '<?php $object->   method();',
        ];

        yield [
            '<?php $object->method();',
            '<?php $object	->method();',
        ];

        yield [
            '<?php $object->method();',
            '<?php $object->	method();',
        ];

        yield [
            '<?php $object->method();',
            '<?php $object	->	method();',
        ];

        yield [
            '<?php echo "use it as -> you want";',
        ];

        // Ensure that doesn't break chained multi-line statements
        yield [
            '<?php $object->method()
                        ->method2()
                        ->method3();',
        ];

        yield [
            '<?php $this
             ->add()
             // Some comment
             ->delete();',
        ];
    }

    /**
     * @dataProvider provideFix80Cases
     *
     * @requires PHP 8.0
     */
    public function testFix80(string $expected, string $input): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<int, array{string, string}>
     */
    public static function provideFix80Cases(): iterable
    {
        yield [
            '<?php $object?->method();',
            '<?php $object?->   method();',
        ];

        yield [
            '<?php $object?->method();',
            '<?php $object   ?->   method();',
        ];
    }
}
