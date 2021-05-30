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

    public function provideFixCases(): array
    {
        return [
            ['<?php $x = 1;'],
            ['<?php $x = "strpos";'],
            ['<?php $x = strpos(\'foo\', \'f\');'],
            ['<?php if (strpos($haystack, $needle) > 0) {}'],

            ['<?php if (str_starts_with($haystack, $needle)) {}', '<?php if (strpos($haystack, $needle) === 0) {}'],
            ['<?php if (!str_starts_with($haystack, $needle)) {}', '<?php if (strpos($haystack, $needle) !== 0) {}'],
            ['<?php if (str_contains($haystack, $needle)) {}', '<?php if (strpos($haystack, $needle) !== false) {}'],
            ['<?php if (!str_contains($haystack, $needle)) {}', '<?php if (strpos($haystack, $needle) === false) {}'],

            ['<?php if (str_starts_with($haystack, $needle)) {}', '<?php if (0 === strpos($haystack, $needle)) {}'],
            ['<?php if (!str_starts_with($haystack, $needle)) {}', '<?php if (0 !== strpos($haystack, $needle)) {}'],
            ['<?php if (str_contains($haystack, $needle)) {}', '<?php if (false !== strpos($haystack, $needle)) {}'],
            ['<?php if (!str_contains($haystack, $needle)) {}', '<?php if (false === strpos($haystack, $needle)) {}'],
        ];
    }
}
