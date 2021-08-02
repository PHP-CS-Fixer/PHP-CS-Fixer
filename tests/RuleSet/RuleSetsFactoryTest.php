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

use PhpCsFixer\RuleSet\RuleSetsFactory;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;


/**
 * @author Krystian Marcisz <simivar@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\RuleSet\RuleSetsFactory
 */
final class RuleSetsFactoryTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @covers \PhpCsFixer\RuleSet\RuleSetsFactory::registerBuiltInRuleSets
     */
    public function testRegisterBuiltInRuleSets(): void
    {
        $factory = new RuleSetsFactory();
        $factory->registerBuiltInRuleSets();

        static::assertGreaterThan(0, \count($factory->getRuleSets()));
    }

    /**
     * @covers \PhpCsFixer\RuleSet\RuleSetsFactory::getRuleSets
     */
    public function testThatRuleSetsAreSorted(): void
    {
        $factory = new RuleSetsFactory();
        $ruleSets = [
            $this->createRuleSetDouble('@Rs3'),
            $this->createRuleSetDouble('@Rs2'),
            $this->createRuleSetDouble('@Rs4'),
            $this->createRuleSetDouble('@Rs1'),
        ];

        foreach ($ruleSets as $ruleSet) {
            $factory->registerRuleSet($ruleSet, false);
        }

        static::assertSame(
            ['@Rs1' => $ruleSets[3], '@Rs2' => $ruleSets[1], '@Rs3' => $ruleSets[0], '@Rs4' => $ruleSets[2]],
            $factory->getRuleSets()
        );
    }

    /**
     * @covers \PhpCsFixer\RuleSet\RuleSetsFactory::getRuleSets
     * @covers \PhpCsFixer\RuleSet\RuleSetsFactory::registerCustomRuleSets
     * @covers \PhpCsFixer\RuleSet\RuleSetsFactory::registerRuleSet
     */
    public function testThatCanRegisterAndGetRuleSets(): void
    {
        $factory = new RuleSetsFactory();

        $rs1 = $this->createRuleSetDouble('@Rs1');
        $rs2 = $this->createRuleSetDouble('@Foo/Rs2');
        $rs3 = $this->createRuleSetDouble('@Foo/Rs3');

        $factory->registerRuleSet($rs1, false);
        $factory->registerCustomRuleSets([$rs2, $rs3]);

        static::assertTrue(\in_array($rs1, $factory->getRuleSets(), true));
        static::assertTrue(\in_array($rs2, $factory->getRuleSets(), true));
        static::assertTrue(\in_array($rs3, $factory->getRuleSets(), true));
    }

    /**
     * @covers \PhpCsFixer\RuleSet\RuleSetsFactory::registerRuleSet
     */
    public function testRegisterRuleSetWithOccupiedName(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('Rule Set named "@NonUniqueName" is already registered.');

        $factory = new RuleSetsFactory();

        $rs1 = $this->createRuleSetDouble('@NonUniqueName');
        $rs2 = $this->createRuleSetDouble('@NonUniqueName');
        $factory->registerRuleSet($rs1, false);
        $factory->registerRuleSet($rs2, false);
    }

    /**
     * @covers \PhpCsFixer\RuleSet\RuleSetsFactory::registerRuleSet
     */
    public function testRegisterRuleSetInvalidName(): void
    {
        $factory = new RuleSetsFactory();

        $fixer = $this->createRuleSetDouble('0');

        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('Rule Set named "0" has invalid name.');

        $factory->registerRuleSet($fixer, false);
    }

    /**
     * @covers \PhpCsFixer\RuleSet\RuleSetsFactory::getRuleSet
     */
    public function testGetSetDefinitionUnregisteredRuleSet(): void
    {
        $factory = new RuleSetsFactory();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectDeprecationMessage('Rule Set "@Test" does not exist.');

        $factory->getRuleSet('@Test');
    }

    /**
     * @covers \PhpCsFixer\RuleSet\RuleSetsFactory::getRuleSetsNames
     */
    public function testGetRuleSetsNamesIsOrdered(): void
    {
        $factory = new RuleSetsFactory();
        $rs1 = $this->createRuleSetDouble('@TheUniqueName');
        $rs2 = $this->createRuleSetDouble('@AUniqueName');
        $factory->registerRuleSet($rs1, false);
        $factory->registerRuleSet($rs2, false);

        self::assertSame(['@NonUniqueName', '@TheUniqueName'], $factory->getRuleSetsNames());
    }

    private function createRuleSetDouble(string $name)
    {
        $ruleSet = $this->prophesize(\PhpCsFixer\RuleSet\AbstractRuleSetDescription::class);
        $ruleSet->getName()->willReturn($name);
        $ruleSet->getRules()->willReturn([]);

        return $ruleSet->reveal();
    }
}
