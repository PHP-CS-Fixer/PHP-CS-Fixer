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
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Alias\NoAliasLanguageConstructCallFixer
 */
final class NoAliasLanguageConstructCallFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): array
    {
        return [
            [
                '<?php exit;',
                '<?php die;',
            ],
            [
                '<?php exit ("foo");',
                '<?php die ("foo");',
            ],
            [
                '<?php exit (1); EXIT(1);',
                '<?php DIE (1); EXIT(1);',
            ],
            [
                '<?php
                    echo "die";
                    // die;
                    /* die(1); */
                    echo $die;
                    echo $die(1);
                    echo $$die;
                ',
            ],
        ];
    }
}
