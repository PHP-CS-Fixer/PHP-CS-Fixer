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

namespace PhpCsFixer\Tests\RuleSet\Sets;

use PhpCsFixer\ConfigurationException\InvalidForEnvFixerConfigurationException;
use PhpCsFixer\FixerFactory;
use PhpCsFixer\RuleSet\RuleSet;
use PhpCsFixer\RuleSet\RuleSetDescriptionInterface;
use PhpCsFixer\Tests\TestCase;

/**
 * @internal
 */
abstract class AbstractSetTest extends TestCase
{
    public function testSet()
    {
        $set = self::getSet();

        static::assertTrue($set instanceof RuleSetDescriptionInterface);
        self::assertSanityString($set->getName());
        self::assertSanityString($set->getDescription());

        $rules = $set->getRules();

        static::assertIsArray($rules);

        foreach ($rules as $rule => $config) {
            self::assertSanityString($rule);
        }
    }

    public function testIsRisky()
    {
        $set = self::getSet();
        $isRisky = $set->isRisky();

        static::assertIsBool($isRisky);

        $factory = new FixerFactory();
        $factory->registerBuiltInFixers();

        try {
            $factory->useRuleSet(new RuleSet($set->getRules()));
        } catch (InvalidForEnvFixerConfigurationException $e) {
            static::markTestSkipped(sprintf('Cannot test set "%s" on this environment. %s', $set->getName(), $e->getMessage()));
        }

        foreach ($factory->getFixers() as $fixer) {
            static::assertSame($isRisky, $fixer->isRisky());
        }
    }

    private static function assertSanityString($string)
    {
        static::assertIsString($string);
        static::assertSame(trim($string), $string);
        static::assertFalse('' === $string);
    }

    /**
     * @return RuleSetDescriptionInterface
     */
    private static function getSet()
    {
        $setClassName = preg_replace('/^(PhpCsFixer)\\\\Tests(\\\\.+)Test$/', '$1$2', static::class);

        return new $setClassName();
    }
}
