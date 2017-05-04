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

namespace PhpCsFixer\Tests\Test;

use PhpCsFixer\RuleSet;
use PhpCsFixer\Test\IntegrationCase;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Test\IntegrationCase
 */
final class IntegrationCaseTest extends TestCase
{
    /**
     * @group legacy
     * @expectedDeprecation The "PhpCsFixer\Test\IntegrationCase::shouldCheckPriority" method is deprecated. You should stop using it, as it will be removed in 3.0 version.
     */
    public function testLegacyShouldCheckPriority()
    {
        $integrationCase = new IntegrationCase(
            'foo',
            'Foo',
            array(),
            array(),
            array(),
            new RuleSet(),
            'Bar',
            'Baz'
        );

        $integrationCase->shouldCheckPriority();
    }
}
