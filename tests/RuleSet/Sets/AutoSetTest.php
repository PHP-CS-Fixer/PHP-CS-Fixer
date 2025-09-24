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

namespace PhpCsFixer\Tests\RuleSet\Sets;

/**
 * @internal
 *
 * @covers \PhpCsFixer\RuleSet\Sets\AutoSet
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class AutoSetTest extends AbstractSetTestCase
{
    /**
     * @covers \PhpCsFixer\RuleSet\AutomaticMigrationSetTrait
     */
    public function testCorrectResolutionTowardsOurOwnRepoConfig(): void
    {
        $set = self::getSet();
        $rules = $set->getRules();

        self::assertSame(
            [
                '@PER-CS' => true,
                '@autoPHPMigration' => true,
            ],
            $rules,
        );
    }
}
