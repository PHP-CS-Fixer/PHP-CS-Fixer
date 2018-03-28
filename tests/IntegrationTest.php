<?php

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
use PhpCsFixer\Tests\Test\InternalIntegrationCaseFactory;

/**
 * Test that parses and runs the fixture '*.test' files found in '/Fixtures/Integration'.
 *
 * @author SpacePossum
 *
 * @internal
 *
 * @coversNothing
 * @group covers-nothing
 */
final class IntegrationTest extends AbstractIntegrationTestCase
{
    /**
     * {@inheritdoc}
     */
    protected static function getFixturesDir()
    {
        return __DIR__.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'Integration';
    }

    /**
     * {@inheritdoc}
     */
    protected static function getTempFile()
    {
        return self::getFixturesDir().DIRECTORY_SEPARATOR.'.tmp.php';
    }

    /**
     * {@inheritdoc}
     */
    protected static function createIntegrationCaseFactory()
    {
        return new InternalIntegrationCaseFactory();
    }

    /**
     * {@inheritdoc}
     */
    protected static function assertRevertedOrderFixing(IntegrationCase $case, $fixedInputCode, $fixedInputCodeWithReversedFixers)
    {
        parent::assertRevertedOrderFixing($case, $fixedInputCode, $fixedInputCodeWithReversedFixers);

        $settings = $case->getSettings();

        if (!isset($settings['isExplicitPriorityCheck'])) {
            static::markTestIncomplete('Missing `isExplicitPriorityCheck` extension setting.');
        }

        if ($settings['isExplicitPriorityCheck']) {
            if ($fixedInputCode === $fixedInputCodeWithReversedFixers) {
                if (in_array($case->getFileName(), [
                    'priority'.DIRECTORY_SEPARATOR.'backtick_to_shell_exec,escape_implicit_backslashes.test',
                    'priority'.DIRECTORY_SEPARATOR.'braces,indentation_type,no_break_comment.test',
                    'priority'.DIRECTORY_SEPARATOR.'standardize_not_equals,binary_operator_spaces.test',
                ], true)) {
                    static::markTestIncomplete(sprintf(
                        'Integration test `%s` was defined as explicit priority test, but no priority conflict was detected.'
                        ."\n".'Either integration test needs to be extended or moved from `priority` to `misc`.'
                        ."\n".'But don\'t do it blindly - it deserves investigation!',
                        $case->getFileName()
                    ));
                }
            }

            static::assertTrue(
                $fixedInputCode !== $fixedInputCodeWithReversedFixers,
                sprintf('Test "%s" in "%s" is expected to be priority check.', $case->getTitle(), $case->getFileName())
            );
        }
    }
}
