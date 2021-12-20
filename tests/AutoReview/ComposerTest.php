<?php

declare(strict_types = 1);

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Tests\AutoReview;

use PhpCsFixer\Console\Application;
use PhpCsFixer\Tests\TestCase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @coversNothing
 * @group auto-review
 * @group covers-nothing
 */
final class ComposerTest extends TestCase
{
    public function testBranchAlias(): void
    {
        $composerJson = json_decode(file_get_contents(__DIR__.'/../../composer.json'), true);

        if (!isset($composerJson['extra']['branch-alias'])) {
            $this->addToAssertionCount(1); // composer.json doesn't contain branch alias, all good!

            return;
        }

        static::assertSame(
            ['dev-master' => $this->convertAppVersionToAliasedVersion(Application::VERSION)],
            $composerJson['extra']['branch-alias']
        );
    }

    private function convertAppVersionToAliasedVersion(string $version): string
    {
        $parts = explode('.', $version, 3);

        return sprintf('%d.%d-dev', $parts[0], $parts[1]);
    }
}
