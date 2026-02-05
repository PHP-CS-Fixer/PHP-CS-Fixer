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

namespace PhpCsFixer\Tests;

use PhpCsFixer\Tests\Test\AbstractIntegrationTestCase;
use PhpCsFixer\Tests\Test\IntegrationCase;
use PhpCsFixer\Tests\Test\IntegrationCaseFactoryInterface;
use PhpCsFixer\Tests\Test\InternalIntegrationCaseFactory;

/**
 * Test that parses and runs the fixture '*.test' files found in '/Fixtures/Integration'.
 *
 * @internal
 *
 * @coversNothing
 *
 * @group covers-nothing
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class IntegrationTest extends AbstractIntegrationTestCase
{
    protected static function getFixturesDir(): string
    {
        return __DIR__.\DIRECTORY_SEPARATOR.'Fixtures'.\DIRECTORY_SEPARATOR.'Integration';
    }

    protected static function getTempFile(): string
    {
        return sys_get_temp_dir().\DIRECTORY_SEPARATOR.'MyClass.php';
    }

    protected static function createIntegrationCaseFactory(): IntegrationCaseFactoryInterface
    {
        return new InternalIntegrationCaseFactory();
    }

    protected static function assertRevertedOrderFixing(IntegrationCase $case, string $fixedInputCode, string $fixedInputCodeWithReversedFixers): void
    {
        parent::assertRevertedOrderFixing($case, $fixedInputCode, $fixedInputCodeWithReversedFixers);

        $settings = $case->getSettings();

        if (!isset($settings['isExplicitPriorityCheck'])) {
            self::markTestIncomplete('Missing `isExplicitPriorityCheck` extension setting.');
        }

        if ($settings['isExplicitPriorityCheck']) {
            self::assertNotSame(
                $fixedInputCode,
                $fixedInputCodeWithReversedFixers,
                \sprintf(
                    'Test "%s" in "%s" is expected to be priority check, but fixers applied in reversed order made the same changes.',
                    $case->getTitle(),
                    $case->getFileName(),
                ),
            );
        }
    }
}
