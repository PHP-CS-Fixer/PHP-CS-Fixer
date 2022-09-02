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
 */
final class IntegrationTest extends AbstractIntegrationTestCase
{
    /**
     * {@inheritdoc}
     */
    protected static function getFixturesDir(): string
    {
        return __DIR__.\DIRECTORY_SEPARATOR.'Fixtures'.\DIRECTORY_SEPARATOR.'Integration';
    }

    /**
     * {@inheritdoc}
     */
    protected static function getTempFile(): string
    {
        return tempnam(sys_get_temp_dir(), 'PHP-CS-Fixer');
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
            static::markTestIncomplete('Missing `isExplicitPriorityCheck` extension setting.');
        }

        if ($settings['isExplicitPriorityCheck']) {
            if ($fixedInputCode === $fixedInputCodeWithReversedFixers) {
                if (\in_array($case->getFileName(), [
                    'priority'.\DIRECTORY_SEPARATOR.'backtick_to_shell_exec,escape_implicit_backslashes.test',
                    'priority'.\DIRECTORY_SEPARATOR.'braces,indentation_type,no_break_comment.test',
                    'priority'.\DIRECTORY_SEPARATOR.'standardize_not_equals,binary_operator_spaces.test',
                ], true)) {
                    static::markTestIncomplete(sprintf(
                        'Integration test `%s` was defined as explicit priority test, but no priority conflict was detected.'
                        ."\n".'Either integration test needs to be extended or moved from `priority` to `misc`.'
                        ."\n".'But don\'t do it blindly - it deserves investigation!',
                        $case->getFileName()
                    ));
                }
            }

            static::assertNotSame(
                $fixedInputCode,
                $fixedInputCodeWithReversedFixers,
                sprintf('Test "%s" in "%s" is expected to be priority check.', $case->getTitle(), $case->getFileName())
            );
        }
    }

    protected function doTest(IntegrationCase $case): void
    {
        $requirements = $case->getRequirements();

        if (isset($requirements['php<'])) {
            $phpUpperLimit = $requirements['php<'];

            if (!\is_int($phpUpperLimit)) {
                throw new \InvalidArgumentException(sprintf(
                    'Expected int value like 50509 for "php<", got "%s". IN "%s".',
                    get_debug_type($phpUpperLimit).'#'.$phpUpperLimit,
                    $case->getFileName(),
                ));
            }

            if (\PHP_VERSION_ID >= $phpUpperLimit) {
                static::markTestSkipped(sprintf('PHP lower than %d is required for "%s", current "%d".', $phpUpperLimit, $case->getFileName(), \PHP_VERSION_ID));
            }
        }

        parent::doTest($case);
    }
}
