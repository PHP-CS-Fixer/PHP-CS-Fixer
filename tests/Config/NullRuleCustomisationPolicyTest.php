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

namespace PhpCsFixer\Tests\Config;

use PhpCsFixer\Config\NullRuleCustomisationPolicy;
use PhpCsFixer\Config\RuleCustomisationPolicyInterface;
use PhpCsFixer\Tests\TestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Config\NullRuleCustomisationPolicy
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class NullRuleCustomisationPolicyTest extends TestCase
{
    public function testImplementsRuleCustomisationPolicyInterface(): void
    {
        $reflection = new \ReflectionClass(NullRuleCustomisationPolicy::class);

        self::assertTrue($reflection->implementsInterface(RuleCustomisationPolicyInterface::class));
    }

    public function testIsFinal(): void
    {
        $reflection = new \ReflectionClass(NullRuleCustomisationPolicy::class);

        self::assertTrue($reflection->isFinal());
    }

    public function testValues(): void
    {
        $policy = new NullRuleCustomisationPolicy();
        self::assertSame('', $policy->policyVersionForCache());
        self::assertSame([], $policy->getRuleCustomisers());
    }
}
