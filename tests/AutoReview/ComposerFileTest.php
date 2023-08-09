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

namespace PhpCsFixer\Tests\AutoReview;

use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 *
 * @group covers-nothing
 * @group auto-review
 */
final class ComposerFileTest extends TestCase
{
    public function testScriptAreHavingDescriptions(): void
    {
        $composerJsonContent = file_get_contents(__DIR__.'/../../composer.json');
        $composerJson = json_decode($composerJsonContent, true);

        $scripts = array_keys($composerJson['scripts']);
        $descriptions = array_keys($composerJson['scripts-descriptions']);

        self::assertSame([], array_diff($scripts, $descriptions), 'There should be no scripts with missing description.');
        self::assertSame([], array_diff($descriptions, $scripts), 'There should be no description for not defined script.');
    }
}
