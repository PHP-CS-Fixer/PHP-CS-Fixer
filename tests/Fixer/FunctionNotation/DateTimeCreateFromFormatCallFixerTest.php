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

namespace PhpCsFixer\Tests\Fixer\FunctionNotation;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\FunctionNotation\DateTimeCreateFromFormatCallFixer
 */
final class DateTimeCreateFromFormatCallFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): iterable
    {
        foreach (['DateTime', 'DateTimeImmutable'] as $class) {
            $lowerCaseClass = strtolower($class);
            $upperCaseClass = strtoupper($class);

            yield [
                "<?php \\{$class}::createFromFormat('!Y-m-d', '2022-02-11');",
                "<?php \\{$class}::createFromFormat('Y-m-d', '2022-02-11');",
            ];

            yield [
                "<?php use {$class}; {$class}::createFromFormat('!Y-m-d', '2022-02-11');",
                "<?php use {$class}; {$class}::createFromFormat('Y-m-d', '2022-02-11');",
            ];

            yield [
                "<?php {$class}::createFromFormat('!Y-m-d', '2022-02-11');",
                "<?php {$class}::createFromFormat('Y-m-d', '2022-02-11');",
            ];

            yield [
                "<?php use \\Example\\{$class}; {$class}::createFromFormat('Y-m-d', '2022-02-11');",
            ];

            yield [
                "<?php use \\Example\\{$lowerCaseClass}; {$upperCaseClass}::createFromFormat('Y-m-d', '2022-02-11');",
            ];

            yield [
                "<?php \\{$class}::createFromFormat(\"!Y-m-d\", '2022-02-11');",
                "<?php \\{$class}::createFromFormat(\"Y-m-d\", '2022-02-11');",
            ];

            yield [
                "<?php \\{$class}::createFromFormat(\$foo, '2022-02-11');",
            ];

            yield [
                "<?php \\{$upperCaseClass}::createFromFormat( \"!Y-m-d\", '2022-02-11');",
                "<?php \\{$upperCaseClass}::createFromFormat( \"Y-m-d\", '2022-02-11');",
            ];

            yield [
                "<?php \\{$class}::createFromFormat(/* aaa */ '!Y-m-d', '2022-02-11');",
                "<?php \\{$class}::createFromFormat(/* aaa */ 'Y-m-d', '2022-02-11');",
            ];

            yield [
                "<?php /*1*//*2*/{$class}/*3*/::/*4*/createFromFormat/*5*/(/*6*/\"!Y-m-d\"/*7*/,/*8*/\"2022-02-11\"/*9*/)/*10*/ ?>",
                "<?php /*1*//*2*/{$class}/*3*/::/*4*/createFromFormat/*5*/(/*6*/\"Y-m-d\"/*7*/,/*8*/\"2022-02-11\"/*9*/)/*10*/ ?>",
            ];

            yield [
                "<?php \\{$class}::createFromFormat('Y-m-d');",
            ];

            yield [
                "<?php \\{$class}::createFromFormat(\$a, \$b);",
            ];

            yield [
                "<?php \\{$class}::createFromFormat('Y-m-d', \$b, \$c);",
            ];

            yield [
                "<?php A\\{$class}::createFromFormat('Y-m-d', '2022-02-11');",
            ];

            yield [
                "<?php A\\{$class}::createFromFormat('Y-m-d'.\"a\", '2022-02-11');",
            ];

            yield ["<?php \\{$class}::createFromFormat(123, '2022-02-11');"];

            yield [
                "<?php namespace {
    \\{$class}::createFromFormat('!Y-m-d', '2022-02-11');
}

namespace Bar {
    class {$class} extends Foo {}
    {$class}::createFromFormat('Y-m-d', '2022-02-11');
}
",
                "<?php namespace {
    \\{$class}::createFromFormat('Y-m-d', '2022-02-11');
}

namespace Bar {
    class {$class} extends Foo {}
    {$class}::createFromFormat('Y-m-d', '2022-02-11');
}
",
            ];

            yield $class.': binary string' => [
                "<?php \\{$class}::createFromFormat(b'!Y-m-d', '2022-02-11');",
                "<?php \\{$class}::createFromFormat(b'Y-m-d', '2022-02-11');",
            ];

            yield $class.': empty string' => [
                "<?php \\{$class}::createFromFormat('', '2022-02-11');",
            ];
        }
    }
}
