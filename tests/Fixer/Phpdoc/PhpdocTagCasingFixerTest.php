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

namespace PhpCsFixer\Tests\Fixer\Phpdoc;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Phpdoc\PhpdocTagCasingFixer
 */
final class PhpdocTagCasingFixerTest extends AbstractFixerTestCase
{
    /**
     * @param array<string, mixed> $config
     *
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null, array $config = []): void
    {
        $this->fixer->configure($config);

        $this->doTest($expected, $input);
    }

    public function provideFixCases(): array
    {
        return [
            [
                '<?php /** @inheritDoc */',
                '<?php /** @inheritdoc */',
            ],
            [
                '<?php /** @inheritDoc */',
                '<?php /** @inheritdoc */',
                ['tags' => ['inheritDoc']],
            ],
            [
                '<?php /** @inheritdoc */',
                '<?php /** @inheritDoc */',
                ['tags' => ['inheritdoc']],
            ],
            [
                '<?php /** {@inheritDoc} */',
                '<?php /** {@inheritdoc} */',
            ],
            [
                '<?php /** {@inheritDoc} */',
                '<?php /** {@inheritdoc} */',
                ['tags' => ['inheritDoc']],
            ],
            [
                '<?php /** {@inheritdoc} */',
                '<?php /** {@inheritDoc} */',
                ['tags' => ['inheritdoc']],
            ],
        ];
    }
}
