<?php

declare(strict_types=1);

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *     and contributors <https://github.com/PHP-CS-Fixer/PHP-CS-Fixer/graphs/contributors>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Tests\RuleSet;

use PhpCsFixer\Tests\Fixtures\TestRuleSet;
use PhpCsFixer\Tests\TestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\RuleSet\AbstractRuleSetDescription
 */
final class AbstractRuleSetDescriptionTest extends TestCase
{
    public function testAbstractRuleSet(): void
    {
        $set = new TestRuleSet();

        self::assertSame('@TestRule', $set->getName());
        self::assertFalse($set->isRisky());
    }
}
