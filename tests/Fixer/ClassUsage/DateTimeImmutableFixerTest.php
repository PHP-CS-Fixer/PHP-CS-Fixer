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
 * @author Kuba Werłos <werlos@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ClassUsage\DateTimeImmutableFixer
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

    public function provideFixCases(): array
    {
        return [
            [
                '<?php new DateTimeImmutable();',
                '<?php new DateTime();',
            ],
            [
                '<?php new DateTimeImmutable();',
                '<?php new DATETIME();',
            ],
            [
                '<?php new \DateTimeImmutable();',
                '<?php new \DateTime();',
            ],
            [
                '<?php new Foo\DateTime();',
            ],
            [
                '<?php namespace Foo; new DateTime();',
            ],
            [
                '<?php namespace Foo; new \DateTimeImmutable();',
                '<?php namespace Foo; new \DateTime();',
            ],
            [
                '<?php namespace Foo; use DateTime; new \DateTimeImmutable();',
                '<?php namespace Foo; use DateTime; new DateTime();',
            ],
            [
                '<?php namespace Foo; use DateTime; new Bar\DateTime();',
            ],
            [
                '<?php namespace Foo; use DateTime\Bar; new DateTime();',
            ],
            [
                '<?php namespace Foo; use Bar\DateTime; new DateTime();',
            ],
            [
                '<?php namespace Foo; use DateTime\Bar; use DateTime; use Baz\DateTime as BazDateTime; new \DateTimeImmutable();',
                '<?php namespace Foo; use DateTime\Bar; use DateTime; use Baz\DateTime as BazDateTime; new DateTime();',
            ],
            [
                '<?php $foo = DateTime::ISO8601;',
            ],
            [
                '<?php $foo = \datetime::ISO8601 + 24;',
            ],
            [
                "<?php DateTimeImmutable::createFromFormat('j-M-Y', '15-Feb-2009');",
                "<?php DateTime::createFromFormat('j-M-Y', '15-Feb-2009');",
            ],
            [
                '<?php \DateTimeImmutable::getLastErrors();',
                '<?php \DateTime::getLastErrors();',
            ],
            [
                '<?php Foo\DateTime::createFromFormat();',
            ],
            [
                '<?php $foo->DateTime();',
            ],
            [
                '<?php Foo::DateTime();',
            ],
            [
                '<?php Foo\DateTime();',
            ],
            [
                '<?php date_create_immutable("now");',
                '<?php date_create("now");',
            ],
            [
                '<?php date_create_immutable();',
                '<?php Date_Create();',
            ],
            [
                '<?php \date_create_immutable();',
                '<?php \date_create();',
            ],
            [
                '<?php namespace Foo; date_create_immutable();',
                '<?php namespace Foo; date_create();',
            ],
            [
                '<?php namespace Foo; \date_create_immutable();',
                '<?php namespace Foo; \date_create();',
            ],
            [
                "<?php date_create_immutable_from_format('j-M-Y', '15-Feb-2009');",
                "<?php date_create_from_format('j-M-Y', '15-Feb-2009');",
            ],
            [
                '<?php Foo\date_create();',
            ],
            [
                '<?php $foo->date_create();',
            ],
            [
                '<?php Foo::date_create();',
            ],
            [
                '<?php new date_create();',
            ],
            [
                '<?php new \date_create();',
            ],
            [
                '<?php new Foo\date_create();',
            ],
            [
                '<?php class Foo { public function datetime() {} }',
            ],
            [
                '<?php class Foo { public function date_create() {} }',
            ],
            [
                '<?php namespace Foo; use DateTime; class Bar { public function datetime() {} }',
            ],
            [
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
            ],
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

    public function provideFix80Cases(): iterable
    {
        yield ['<?php $foo?->DateTime();'];

        yield ['<?php $foo?->date_create();'];
    }
}
