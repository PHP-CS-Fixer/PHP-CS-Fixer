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

namespace PhpCsFixer\Tests\Fixer\Naming;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Naming\NoHomoglyphNamesFixer
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\Naming\NoHomoglyphNamesFixer>
 *
 * @author Fred Cox <mcfedr@gmail.com>
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class NoHomoglyphNamesFixerTest extends AbstractFixerTestCase
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
        yield ['<?php $øøøøa = 1;'];

        yield ['<?php $name = "This should not be changed";'];

        yield ['<?php $name = "Это не меняется";'];

        yield ['<?php $name = \'Это не меняется\';'];

        yield ['<?php // This should not be chаnged'];

        yield ['<?php /* This should not be chаnged */'];

        yield [
            '<?php $name = \'wrong\';',
            '<?php $nаmе = \'wrong\';', // 'а' in name is a cyrillic letter
        ];

        yield [
            '<?php $a->name = \'wrong\';',
            '<?php $a->nаmе = \'wrong\';',
        ];

        yield [
            '<?php class A { private $name; }',
            '<?php class A { private $nаmе; }',
        ];

        yield [
            '<?php class Broken {}',
            '<?php class Вroken {}', // 'В' in Broken is a cyrillic letter
        ];

        yield [
            '<?php interface Broken {}',
            '<?php interface Вroken {}',
        ];

        yield [
            '<?php trait Broken {}',
            '<?php trait Вroken {}',
        ];

        yield [
            '<?php $a = new Broken();',
            '<?php $a = new Вroken();',
        ];

        yield [
            '<?php class A extends Broken {}',
            '<?php class A extends Вroken {}',
        ];

        yield [
            '<?php class A implements Broken {}',
            '<?php class A implements Вroken {}',
        ];

        yield [
            '<?php class A { use Broken; }',
            '<?php class A { use Вroken; }',
        ];

        yield [
            '<?php echo Broken::class;',
            '<?php echo Вroken::class;',
        ];

        yield [
            '<?php function name() {}',
            '<?php function nаmе() {}',
        ];

        yield [
            '<?php name();',
            '<?php nаmе();',
        ];

        yield [
            '<?php $first_name = "a";',
            '<?php $first＿name = "a";', // Weird underscore symbol
        ];

        yield [
            '<?php class A { private string $name; }',
            '<?php class A { private string $nаmе; }',
        ];

        yield [
            '<?php class A { private ? Foo\Bar $name; }',
            '<?php class A { private ? Foo\Bar $nаmе; }',
        ];

        yield [
            '<?php class A { private array $name; }',
            '<?php class A { private array $nаmе; }',
        ];
    }
}
