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

use PhpCsFixer\Tests\TestCase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @coversNothing
 *
 * @group auto-review
 * @group covers-nothing
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class PublicApiSurfaceTest extends TestCase
{
    /**
     * Validates that public interface does not expose internal types.
     *
     * Public methods in non-internal classes must not expose @internal types
     * in their parameters or return types. This ensures the public API surface
     * is stable and doesn't leak internal implementation details.
     */
    public function testPublicMethodsDoNotExposeInternalTypes(): void
    {
        $scriptPath = __DIR__.'/../../dev-tools/check_public_api_surface.php';
        self::assertFileExists($scriptPath, 'Public API surface checker script must exist');

        exec(\sprintf('php %s 2>&1', escapeshellarg($scriptPath)), $output, $exitCode);

        $outputString = implode("\n", $output);

        self::assertSame(
            0,
            $exitCode,
            \sprintf(
                "Public API surface check failed:\n%s",
                $outputString
            )
        );
    }
}
