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

namespace PhpCsFixer\Tests\Priority;

use PhpCsFixer\FixerFactory;
use PhpCsFixer\Priority\PrioritiesCalculator;
use PhpCsFixer\Tests\TestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Priority\PrioritiesCalculator
 */
final class PrioritiesCalculatorTest extends TestCase
{
    public function testAllFixersGetPriority()
    {
        $calculator = new PrioritiesCalculator();
        $priorities = $calculator->calculate();

        $fixers = new FixerFactory();
        $fixers->registerBuiltInFixers();

        foreach ($fixers->getFixers() as $fixer) {
            if (!isset($priorities[$fixer->getName()])) {
                continue;
            }
            static::assertArrayHasKey($fixer->getName(), $priorities);
            static::assertSame($fixer->getPriority(), $priorities[$fixer->getName()], sprintf('Fixer "%s" has incorrect priority', $fixer->getName()));
        }
    }
}
