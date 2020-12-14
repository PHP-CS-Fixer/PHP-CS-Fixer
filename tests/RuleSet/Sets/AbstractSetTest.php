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

        $setName = $set->getName();
        $setDescription = $set->getDescription();
        $isRiskySet = $set->isRisky();
        $setRules = $set->getRules();

        $factory = new FixerFactory();
        $factory->registerBuiltInFixers();

        static::assertSanityString($setName);
        static::assertSanityString($setDescription);
        static::assertSame('.', substr($setDescription, -1), sprintf('Ruleset description of "%s" must end with ".", got "%s".', $setName, $setDescription));
        static::assertIsBool($isRiskySet);
        static::assertIsArray($setRules);

        try {
            $factory->useRuleSet(new RuleSet($set->getRules()));
        } catch (InvalidForEnvFixerConfigurationException $e) {
            static::markTestSkipped(sprintf('Cannot test set "%s" on this environment. %s', $setName, $e->getMessage()));
        }

        foreach ($factory->getFixers() as $fixer) {
            $fixerName = $fixer->getName();
            static::assertSame($isRiskySet, $fixer->isRisky(), sprintf('Is risky mismatch between set "%s" and rule "%s".', $setName, $fixerName));

            if (isset($setRules[$fixerName])) {
                static::assertTrue(\is_bool($setRules[$fixerName]) || \is_array($setRules[$fixerName]));
            }
        }
    }

    private static function assertSanityString($string)
    {
        static::assertIsString($string);
        static::assertSame(trim($string), $string);
        static::assertNotSame('', $string);
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
