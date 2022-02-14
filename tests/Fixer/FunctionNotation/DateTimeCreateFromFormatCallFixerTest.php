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

    public function provideFixCases(): \Generator
    {
        yield [
            '<?php \DateTime::createFromFormat(\'!Y-m-d\', \'2022-02-11\');',
            '<?php \DateTime::createFromFormat(\'Y-m-d\', \'2022-02-11\');',
        ];

        yield [
            '<?php use DateTime; DateTime::createFromFormat(\'!Y-m-d\', \'2022-02-11\');',
            '<?php use DateTime; DateTime::createFromFormat(\'Y-m-d\', \'2022-02-11\');',
        ];

        yield [
            '<?php DateTime::createFromFormat(\'!Y-m-d\', \'2022-02-11\');',
            '<?php DateTime::createFromFormat(\'Y-m-d\', \'2022-02-11\');',
        ];

        yield [
            '<?php use \Example\DateTime; DateTime::createFromFormat(\'Y-m-d\', \'2022-02-11\');',
        ];

        yield [
            '<?php use \Example\datetime; DATETIME::createFromFormat(\'Y-m-d\', \'2022-02-11\');',
        ];

        yield [
            '<?php \DateTime::createFromFormat("!Y-m-d", \'2022-02-11\');',
            '<?php \DateTime::createFromFormat("Y-m-d", \'2022-02-11\');',
        ];

        yield [
            '<?php \DateTime::createFromFormat($foo, \'2022-02-11\');',
        ];

        yield [
            '<?php \DATETIME::createFromFormat( "!Y-m-d", \'2022-02-11\');',
            '<?php \DATETIME::createFromFormat( "Y-m-d", \'2022-02-11\');',
        ];

        yield [
            '<?php \DateTime::createFromFormat(/* aaa */ \'!Y-m-d\', \'2022-02-11\');',
            '<?php \DateTime::createFromFormat(/* aaa */ \'Y-m-d\', \'2022-02-11\');',
        ];

        yield [
            '<?php /*1*//*2*/DateTime/*3*/::/*4*/createFromFormat/*5*/(/*6*/"!Y-m-d"/*7*/,/*8*/"2022-02-11"/*9*/)/*10*/ ?>',
            '<?php /*1*//*2*/DateTime/*3*/::/*4*/createFromFormat/*5*/(/*6*/"Y-m-d"/*7*/,/*8*/"2022-02-11"/*9*/)/*10*/ ?>',
        ];

        yield [
            '<?php \DateTime::createFromFormat(\'Y-m-d\');',
        ];

        yield [
            '<?php \DateTime::createFromFormat($a, $b);',
        ];

        yield [
            '<?php \DateTime::createFromFormat(\'Y-m-d\', $b, $c);',
        ];

        yield [
            '<?php A\DateTime::createFromFormat(\'Y-m-d\', \'2022-02-11\');',
        ];

        yield [
            '<?php A\DateTime::createFromFormat(\'Y-m-d\'."a", \'2022-02-11\');',
        ];

        yield ['<?php \DateTime::createFromFormat(123, \'2022-02-11\');'];

        yield [
            '<?php namespace {
    \DateTime::createFromFormat(\'!Y-m-d\', \'2022-02-11\');
}

namespace Bar {
    class DateTime extends Foo {}
    DateTime::createFromFormat(\'Y-m-d\', \'2022-02-11\');
}
',
            '<?php namespace {
    \DateTime::createFromFormat(\'Y-m-d\', \'2022-02-11\');
}

namespace Bar {
    class DateTime extends Foo {}
    DateTime::createFromFormat(\'Y-m-d\', \'2022-02-11\');
}
',
        ];
    }
}
