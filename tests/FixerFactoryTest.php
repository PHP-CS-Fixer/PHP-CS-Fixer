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

namespace PhpCsFixer\Tests;

use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\FixerFactory;
use PhpCsFixer\RuleSet\RuleSet;
use PhpCsFixer\RuleSet\RuleSetInterface;
use PhpCsFixer\WhitespacesFixerConfig;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\FixerFactory
 */
final class FixerFactoryTest extends TestCase
{
    public function testInterfaceIsFluent(): void
    {
        $factory = new FixerFactory();

        $testInstance = $factory->registerBuiltInFixers();
        static::assertSame($factory, $testInstance);

        $testInstance = $factory->registerCustomFixers(
            [$this->createFixerDouble('Foo/f1'), $this->createFixerDouble('Foo/f2')]
        );

        static::assertSame($factory, $testInstance);

        $testInstance = $factory->registerFixer(
            $this->createFixerDouble('f3'),
            false
        );

        static::assertSame($factory, $testInstance);

        $ruleSetProphecy = $this->prophesize(RuleSetInterface::class);
        $ruleSetProphecy->getRules()->willReturn([]);
        $testInstance = $factory->useRuleSet(
            $ruleSetProphecy->reveal()
        );

        static::assertSame($factory, $testInstance);
    }

    /**
     * @covers \PhpCsFixer\FixerFactory::registerBuiltInFixers
     */
    public function testRegisterBuiltInFixers(): void
    {
        $factory = new FixerFactory();
        $factory->registerBuiltInFixers();

        $fixerClasses = array_filter(
            get_declared_classes(),
            static function (string $className): bool {
                $class = new \ReflectionClass($className);

                return !$class->isAbstract() && $class->implementsInterface(FixerInterface::class) && str_starts_with($class->getNamespaceName(), 'PhpCsFixer\\Fixer\\');
            }
        );

        sort($fixerClasses);

        $fixers = array_map(
            static function (FixerInterface $fixer): string {
                return \get_class($fixer);
            },
            $factory->getFixers()
        );

        sort($fixers);

        static::assertSame($fixerClasses, $fixers);
    }

    /**
     * @covers \PhpCsFixer\FixerFactory::getFixers
     */
    public function testThatFixersAreSorted(): void
    {
        $factory = new FixerFactory();
        $fxs = [
            $this->createFixerDouble('f1', 0),
            $this->createFixerDouble('f2', -10),
            $this->createFixerDouble('f3', 10),
            $this->createFixerDouble('f4', -10),
        ];

        foreach ($fxs as $fx) {
            $factory->registerFixer($fx, false);
        }

        // There are no rules that forces $fxs[1] to be prioritized before $fxs[3]. We should not test against that
        static::assertSame([$fxs[2], $fxs[0]], \array_slice($factory->getFixers(), 0, 2));
    }

    /**
     * @covers \PhpCsFixer\FixerFactory::getFixers
     * @covers \PhpCsFixer\FixerFactory::registerCustomFixers
     * @covers \PhpCsFixer\FixerFactory::registerFixer
     */
    public function testThatCanRegisterAndGetFixers(): void
    {
        $factory = new FixerFactory();

        $f1 = $this->createFixerDouble('f1');
        $f2 = $this->createFixerDouble('Foo/f2');
        $f3 = $this->createFixerDouble('Foo/f3');

        $factory->registerFixer($f1, false);
        $factory->registerCustomFixers([$f2, $f3]);

        static::assertTrue(\in_array($f1, $factory->getFixers(), true));
        static::assertTrue(\in_array($f2, $factory->getFixers(), true));
        static::assertTrue(\in_array($f3, $factory->getFixers(), true));
    }

    /**
     * @covers \PhpCsFixer\FixerFactory::registerFixer
     */
    public function testRegisterFixerWithOccupiedName(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('Fixer named "non_unique_name" is already registered.');

        $factory = new FixerFactory();

        $f1 = $this->createFixerDouble('non_unique_name');
        $f2 = $this->createFixerDouble('non_unique_name');
        $factory->registerFixer($f1, false);
        $factory->registerFixer($f2, false);
    }

    /**
     * @covers \PhpCsFixer\FixerFactory::useRuleSet
     */
    public function testUseRuleSet(): void
    {
        $factory = (new FixerFactory())
            ->registerBuiltInFixers()
            ->useRuleSet(new RuleSet([]))
        ;

        static::assertCount(0, $factory->getFixers());

        $factory = (new FixerFactory())
            ->registerBuiltInFixers()
            ->useRuleSet(new RuleSet(['strict_comparison' => true, 'blank_line_before_statement' => false]))
        ;

        $fixers = $factory->getFixers();
        static::assertCount(1, $fixers);
        static::assertSame('strict_comparison', $fixers[0]->getName());
    }

    /**
     * @covers \PhpCsFixer\FixerFactory::useRuleSet
     */
    public function testUseRuleSetWithNonExistingRule(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('Rule "non_existing_rule" does not exist.');

        $factory = (new FixerFactory())
            ->registerBuiltInFixers()
            ->useRuleSet(new RuleSet(['non_existing_rule' => true]))
        ;

        $factory->getFixers();
    }

    /**
     * @covers \PhpCsFixer\FixerFactory::useRuleSet
     */
    public function testUseRuleSetWithInvalidConfigForRule(): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);
        $this->expectExceptionMessage('Configuration must be an array and may not be empty.');

        $testRuleSet = new class() implements RuleSetInterface {
            public function __construct(array $set = [])
            {
                if ([] !== $set) {
                    throw new \RuntimeException('Set is not used in test.');
                }
            }

            public function getRuleConfiguration(string $rule): ?array
            {
                return $this->getRules()[$rule];
            }

            public function getRules(): array
            {
                return ['header_comment' => []];
            }

            public function hasRule(string $rule): bool
            {
                return isset($this->getRules()[$rule]);
            }
        };

        $factory = (new FixerFactory())
            ->registerBuiltInFixers()
            ->useRuleSet($testRuleSet)
        ;

        $factory->getFixers();
    }

    public function testHasRule(): void
    {
        $factory = new FixerFactory();

        $f1 = $this->createFixerDouble('f1');
        $f2 = $this->createFixerDouble('Foo/f2');
        $f3 = $this->createFixerDouble('Foo/f3');
        $factory->registerFixer($f1, false);
        $factory->registerCustomFixers([$f2, $f3]);

        static::assertTrue($factory->hasRule('f1'), 'Should have f1 fixer');
        static::assertTrue($factory->hasRule('Foo/f2'), 'Should have f2 fixer');
        static::assertTrue($factory->hasRule('Foo/f3'), 'Should have f3 fixer');
        static::assertFalse($factory->hasRule('dummy'), 'Should not have dummy fixer');
    }

    public function testHasRuleWithChangedRuleSet(): void
    {
        $factory = new FixerFactory();

        $f1 = $this->createFixerDouble('f1');
        $f2 = $this->createFixerDouble('f2');
        $factory->registerFixer($f1, false);
        $factory->registerFixer($f2, false);

        static::assertTrue($factory->hasRule('f1'), 'Should have f1 fixer');
        static::assertTrue($factory->hasRule('f2'), 'Should have f2 fixer');

        $factory->useRuleSet(new RuleSet(['f2' => true]));
        static::assertFalse($factory->hasRule('f1'), 'Should not have f1 fixer');
        static::assertTrue($factory->hasRule('f2'), 'Should have f2 fixer');
    }

    /**
     * @dataProvider provideConflictingFixersCases
     */
    public function testConflictingFixers(RuleSet $ruleSet): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessageMatches('#^Rule contains conflicting fixers:\n#');

        (new FixerFactory())
            ->registerBuiltInFixers()->useRuleSet($ruleSet)
        ;
    }

    public static function provideConflictingFixersCases(): array
    {
        return [
            [new RuleSet(['no_blank_lines_before_namespace' => true, 'single_blank_line_before_namespace' => true])],
            [new RuleSet(['single_blank_line_before_namespace' => true, 'no_blank_lines_before_namespace' => true])],
        ];
    }

    public function testNoDoubleConflictReporting(): void
    {
        $factory = new FixerFactory();
        $method = new \ReflectionMethod($factory, 'generateConflictMessage');
        $method->setAccessible(true);
        static::assertSame(
            'Rule contains conflicting fixers:
- "a" with "b"
- "c" with "d", "e", "f"
- "d" with "g", "h"
- "e" with "a"',
            $method->invoke(
                $factory,
                [
                    'a' => ['b'],
                    'b' => ['a'],
                    'c' => ['d', 'e', 'f'],
                    'd' => ['c', 'g', 'h'],
                    'e' => ['a'],
                ]
            )
        );
    }

    public function testSetWhitespacesConfig(): void
    {
        $factory = new FixerFactory();
        $config = new WhitespacesFixerConfig();

        $fixer = $this->prophesize(\PhpCsFixer\Fixer\WhitespacesAwareFixerInterface::class);
        $fixer->getName()->willReturn('foo');
        $fixer->setWhitespacesConfig($config)->shouldBeCalled();

        $factory->registerFixer($fixer->reveal(), false);
        $factory->setWhitespacesConfig($config);
    }

    public function testRegisterFixerInvalidName(): void
    {
        $factory = new FixerFactory();

        $fixer = $this->createFixerDouble('0');

        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('Fixer named "0" has invalid name.');

        $factory->registerFixer($fixer, false);
    }

    public function testConfigureNonConfigurableFixer(): void
    {
        $factory = new FixerFactory();

        $fixer = $this->createFixerDouble('non_configurable');
        $factory->registerFixer($fixer, false);

        $this->expectException(InvalidFixerConfigurationException::class);

        $this->expectExceptionMessage('[non_configurable] Is not configurable.');

        $factory->useRuleSet(new RuleSet([
            'non_configurable' => ['bar' => 'baz'],
        ]));
    }

    /**
     * @param mixed $value
     *
     * @dataProvider provideConfigureFixerWithNonArrayCases
     */
    public function testConfigureFixerWithNonArray($value): void
    {
        $factory = new FixerFactory();

        $fixer = $this->prophesize(ConfigurableFixerInterface::class);
        $fixer->getName()->willReturn('foo');

        $factory->registerFixer($fixer->reveal(), false);

        $this->expectException(InvalidFixerConfigurationException::class);

        $this->expectExceptionMessage(
            '[foo] Rule must be enabled (true), disabled (false) or configured (non-empty, assoc array). Other values are not allowed.'
        );

        $factory->useRuleSet(new RuleSet([
            'foo' => $value,
        ]));
    }

    public static function provideConfigureFixerWithNonArrayCases(): array
    {
        return [
            ['bar'],
            [new \stdClass()],
            [5],
            [5.5],
        ];
    }

    public function testConfigurableFixerIsConfigured(): void
    {
        $fixer = $this->prophesize(ConfigurableFixerInterface::class);
        $fixer->getName()->willReturn('foo');
        $fixer->configure(['bar' => 'baz'])->shouldBeCalled();

        $factory = new FixerFactory();
        $factory->registerFixer($fixer->reveal(), false);
        $factory->useRuleSet(new RuleSet([
            'foo' => ['bar' => 'baz'],
        ]));
    }

    private function createFixerDouble(string $name, int $priority = 0): FixerInterface
    {
        $fixer = $this->prophesize(\PhpCsFixer\Fixer\FixerInterface::class);
        $fixer->getName()->willReturn($name);
        $fixer->getPriority()->willReturn($priority);

        return $fixer->reveal();
    }
}
