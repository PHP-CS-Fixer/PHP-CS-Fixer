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

use PhpCsFixer\RuleSet;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\RuleSet
 *
 * @deprecated will be removed in 3.0, use \PhpCsFixer\RuleSet\RuleSet
 */
final class RuleSetTest extends TestCase
{
    public function testRuleSetExtendsNewClass()
    {
        $set = new RuleSet();

        static::assertInstanceOf(RuleSet\RuleSet::class, $set);
    }
}
