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

namespace PhpCsFixer\Tests\Fixer\Alias;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Alexander M. Turek <me@derrabus.de>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Alias\ModernizeStrposFixer
 */
final class ModernizeStrposFixerTest extends AbstractFixerTestCase
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
        yield 'yoda ===' => [
            '<?php if (  str_starts_with($haystack1, $needle)) {}',
            '<?php if (0 === strpos($haystack1, $needle)) {}',
        ];

        yield 'not zero yoda !==' => [
            '<?php if (  !str_starts_with($haystack2, $needle)) {}',
            '<?php if (0 !== strpos($haystack2, $needle)) {}',
        ];

        yield 'false yoda ===' => [
            '<?php if (  !str_contains($haystack, $needle)) {}',
            '<?php if (false === strpos($haystack, $needle)) {}',
        ];

        yield [
            '<?php if (str_starts_with($haystack3, $needle)  ) {}',
            '<?php if (strpos($haystack3, $needle) === 0) {}',
        ];

        yield 'casing call' => [
            '<?php if (str_starts_with($haystack4, $needle)  ) {}',
            '<?php if (STRPOS($haystack4, $needle) === 0) {}',
        ];

        yield 'leading namespace' => [
            '<?php if (\str_starts_with($haystack5, $needle)  ) {}',
            '<?php if (\strpos($haystack5, $needle) === 0) {}',
        ];

        yield 'leading namespace with yoda' => [
            '<?php if (  \str_starts_with($haystack5, $needle)) {}',
            '<?php if (0 === \strpos($haystack5, $needle)) {}',
        ];

        yield [
            '<?php if (!str_starts_with($haystack6, $needle)  ) {}',
            '<?php if (strpos($haystack6, $needle) !== 0) {}',
        ];

        yield [
            '<?php if (!\str_starts_with($haystack6, $needle)  ) {}',
            '<?php if (\strpos($haystack6, $needle) !== 0) {}',
        ];

        yield [
            '<?php if (  !\str_starts_with($haystack6, $needle)) {}',
            '<?php if (0 !== \strpos($haystack6, $needle)) {}',
        ];

        yield 'casing operand' => [
            '<?php if (str_contains($haystack7, $needle)  ) {}',
            '<?php if (strpos($haystack7, $needle) !== FALSE) {}',
        ];

        yield [
            '<?php if (!str_contains($haystack8, $needle)  ) {}',
            '<?php if (strpos($haystack8, $needle) === false) {}',
        ];

        yield [
            '<?php if (  !str_starts_with($haystack9, $needle)) {}',
            '<?php if (0 !== strpos($haystack9, $needle)) {}',
        ];

        yield [
            '<?php $a = !str_starts_with($haystack9a, $needle)  ;',
            '<?php $a = strpos($haystack9a, $needle) !== 0;',
        ];

        yield 'comments inside, no spacing' => [
            '<?php if (/* foo *//* bar */str_contains($haystack10,$a)) {}',
            '<?php if (/* foo */false/* bar */!==strpos($haystack10,$a)) {}',
        ];

        yield [
            '<?php $a =   !str_contains($haystack11, $needle)?>',
            '<?php $a = false === strpos($haystack11, $needle)?>',
        ];

        yield [
            '<?php $a = $input &&   str_contains($input, $method)   ? $input : null;',
            '<?php $a = $input &&   strpos($input, $method) !== FALSE ? $input : null;',
        ];

        // do not fix

        yield [
            '<?php
                $x = 1;
                $x = "strpos";
                // if (false === strpos($haystack12, $needle)) {}
                /** if (false === strpos($haystack13, $needle)) {} */
            ',
        ];

        yield 'different namespace' => [
            '<?php if (a\strpos($haystack14, $needle) === 0) {}',
        ];

        yield 'different namespace with yoda' => [
            '<?php if (0 === a\strpos($haystack14, $needle)) {}',
        ];

        yield 'non condition (hardcoded)' => [
            '<?php $x = strpos(\'foo\', \'f\');',
        ];

        yield 'non condition' => [
            '<?php $x = strpos($haystack15, $needle) ?>',
        ];

        yield 'none zero int' => [
            '<?php if (1 !== strpos($haystack16, $needle)) {}',
        ];

        yield 'greater condition' => [
            '<?php if (strpos($haystack17, $needle) > 0) {}',
        ];

        yield 'lesser condition' => [
            '<?php if (0 < strpos($haystack18, $needle)) {}',
        ];

        yield 'no argument' => [
            '<?php $z = strpos();',
        ];

        yield 'one argument' => [
            '<?php if (0 === strpos($haystack1)) {}',
        ];

        yield '3 arguments' => [
            '<?php if (0 === strpos($haystack1, $a, $b)) {}',
        ];

        yield 'higher precedence 1' => [
            '<?php if (4 + 0 !== strpos($haystack9, $needle)) {}',
        ];

        yield 'higher precedence 2' => [
            '<?php if (!false === strpos($haystack, $needle)) {}',
        ];

        yield 'higher precedence 3' => [
            '<?php $a = strpos($haystack, $needle) === 0 + 1;',
        ];

        yield 'higher precedence 4' => [
            '<?php $a = strpos($haystack, $needle) === 0 > $b;',
        ];
    }
}
