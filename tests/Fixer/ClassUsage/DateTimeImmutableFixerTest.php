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

namespace PhpCsFixer\Tests\Fixer\ClassUsage;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\ClassUsage\DateTimeImmutableFixer>
 *
 * @covers \PhpCsFixer\Fixer\ClassUsage\DateTimeImmutableFixer
 *
 * @author Kuba Werłos <werlos@gmail.com>
 */
final class DateTimeImmutableFixerTest extends AbstractFixerTestCase
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
            '<?php new DateTimeImmutable();',
            '<?php new DateTime();',
        ];

        yield [
            '<?php new DateTimeImmutable();',
            '<?php new DATETIME();',
        ];

        yield [
            '<?php new \DateTimeImmutable();',
            '<?php new \DateTime();',
        ];

        yield [
            '<?php new Foo\DateTime();',
        ];

        yield [
            '<?php namespace Foo; new DateTime();',
        ];

        yield [
            '<?php namespace Foo; new \DateTimeImmutable();',
            '<?php namespace Foo; new \DateTime();',
        ];

        yield [
            '<?php namespace Foo; use DateTime; new \DateTimeImmutable();',
            '<?php namespace Foo; use DateTime; new DateTime();',
        ];

        yield [
            '<?php namespace Foo; use DateTime; new Bar\DateTime();',
        ];

        yield [
            '<?php namespace Foo; use DateTime\Bar; new DateTime();',
        ];

        yield [
            '<?php namespace Foo; use Bar\DateTime; new DateTime();',
        ];

        yield [
            '<?php namespace Foo; use DateTime\Bar; use DateTime; use Baz\DateTime as BazDateTime; new \DateTimeImmutable();',
            '<?php namespace Foo; use DateTime\Bar; use DateTime; use Baz\DateTime as BazDateTime; new DateTime();',
        ];

        yield [
            '<?php $foo = DateTime::ISO8601;',
        ];

        yield [
            '<?php $foo = \datetime::ISO8601 + 24;',
        ];

        yield [
            "<?php DateTimeImmutable::createFromFormat('j-M-Y', '15-Feb-2009');",
            "<?php DateTime::createFromFormat('j-M-Y', '15-Feb-2009');",
        ];

        yield [
            '<?php \DateTimeImmutable::getLastErrors();',
            '<?php \DateTime::getLastErrors();',
        ];

        yield [
            '<?php Foo\DateTime::createFromFormat();',
        ];

        yield [
            '<?php $foo->DateTime();',
        ];

        yield [
            '<?php Foo::DateTime();',
        ];

        yield [
            '<?php Foo\DateTime();',
        ];

        yield [
            '<?php date_create_immutable("now");',
            '<?php date_create("now");',
        ];

        yield [
            '<?php date_create_immutable();',
            '<?php Date_Create();',
        ];

        yield [
            '<?php \date_create_immutable();',
            '<?php \date_create();',
        ];

        yield [
            '<?php namespace Foo; date_create_immutable();',
            '<?php namespace Foo; date_create();',
        ];

        yield [
            '<?php namespace Foo; \date_create_immutable();',
            '<?php namespace Foo; \date_create();',
        ];

        yield [
            "<?php date_create_immutable_from_format('j-M-Y', '15-Feb-2009');",
            "<?php date_create_from_format('j-M-Y', '15-Feb-2009');",
        ];

        yield [
            '<?php Foo\date_create();',
        ];

        yield [
            '<?php $foo->date_create();',
        ];

        yield [
            '<?php Foo::date_create();',
        ];

        yield [
            '<?php new date_create();',
        ];

        yield [
            '<?php new \date_create();',
        ];

        yield [
            '<?php new Foo\date_create();',
        ];

        yield [
            '<?php class Foo { public function datetime() {} }',
        ];

        yield [
            '<?php class Foo { public function date_create() {} }',
        ];

        yield [
            '<?php namespace Foo; use DateTime; class Bar { public function datetime() {} }',
        ];

        yield [
            '<?php
                namespace Foo;
                use DateTime\Bar;
                use DateTime;
                use Baz\DateTime as BazDateTime;
                new \DateTimeImmutable();
                new \DateTimeImmutable();
                new \DateTimeImmutable();
                new \DateTimeImmutable();
                ',
            '<?php
                namespace Foo;
                use DateTime\Bar;
                use DateTime;
                use Baz\DateTime as BazDateTime;
                new DateTime();
                new DateTime();
                new DateTime();
                new DateTime();
                ',
        ];
    }

    /**
     * @dataProvider provideFix80Cases
     *
     * @requires PHP 8.0
     */
    public function testFix80(string $expected): void
    {
        $this->doTest($expected);
    }

    /**
     * @return iterable<int, array{string}>
     */
    public static function provideFix80Cases(): iterable
    {
        yield ['<?php $foo?->DateTime();'];

        yield ['<?php $foo?->date_create();'];
    }
}
