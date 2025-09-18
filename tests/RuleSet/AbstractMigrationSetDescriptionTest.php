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

namespace PhpCsFixer\Tests\RuleSet;

use PhpCsFixer\RuleSet\AbstractMigrationSetDescription;
use PhpCsFixer\Tests\TestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\RuleSet\AbstractMigrationSetDescription
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class AbstractMigrationSetDescriptionTest extends TestCase
{
    public function testGetDescriptionForPhpMigrationSet(): void
    {
        $set = new class extends AbstractMigrationSetDescription {
            public function getName(): string
            {
                return '@PHP9x9MigrationSet';
            }

            public function getRules(): array
            {
                return [];
            }
        };

        self::assertSame('Rules to improve code for PHP 9.9 compatibility.', $set->getDescription());
    }

    /**
     * @TODO v4 - remove the test while solving @MARKER_deprecated_migration_name_pattern
     */
    public function testGetDescriptionForPhpMigrationSetLegacy(): void
    {
        $set = new class extends AbstractMigrationSetDescription {
            public function getName(): string
            {
                return '@PHP99MigrationSet';
            }

            public function getRules(): array
            {
                return [];
            }
        };

        self::assertSame('Rules to improve code for PHP 9.9 compatibility.', $set->getDescription());
    }

    public function testGetDescriptionForPhpUnitMigrationSet(): void
    {
        $set = new class extends AbstractMigrationSetDescription {
            public function getName(): string
            {
                return '@PHPUnit30Migration';
            }

            public function getRules(): array
            {
                return [];
            }
        };

        self::assertSame('Rules to improve tests code for PHPUnit 3.0 compatibility.', $set->getDescription());
    }

    public function testGetDescriptionForNoneMigrationSet(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/^Cannot generate name of ".*" \/ "foo"\.$/');

        $set = new class extends AbstractMigrationSetDescription {
            public function getName(): string
            {
                return 'foo';
            }

            public function getRules(): array
            {
                return [];
            }
        };

        $set->getName();
    }
}
