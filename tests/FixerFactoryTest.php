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

use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\FixerFactory;
use PhpCsFixer\RuleSet;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\FixerFactory
 */
final class FixerFactoryTest extends TestCase
{
    public function testInterfaceIsFluent()
    {
        $factory = new FixerFactory();

        $testInstance = $factory->registerBuiltInFixers();
        $this->assertSame($factory, $testInstance);

        $testInstance = $factory->registerCustomFixers(
            array($this->createFixerDouble('Foo/f1'), $this->createFixerDouble('Foo/f2'))
        );
        $this->assertSame($factory, $testInstance);

        $testInstance = $factory->registerFixer(
            $this->createFixerDouble('f3'),
            false
        );
        $this->assertSame($factory, $testInstance);

        $ruleSetProphecy = $this->prophesize('PhpCsFixer\RuleSetInterface');
        $ruleSetProphecy->getRules()->willReturn(array());
        $testInstance = $factory->useRuleSet(
            $ruleSetProphecy->reveal()
        );
        $this->assertSame($factory, $testInstance);
    }

    /**
     * @covers \PhpCsFixer\FixerFactory::create
     */
    public function testCreate()
    {
        $factory = FixerFactory::create();

        $this->assertInstanceOf('PhpCsFixer\FixerFactory', $factory);
    }

    /**
     * @covers \PhpCsFixer\FixerFactory::registerBuiltInFixers
     */
    public function testRegisterBuiltInFixers()
    {
        $factory = new FixerFactory();
        $factory->registerBuiltInFixers();

        $this->assertGreaterThan(0, count($factory->getFixers()));
    }

    /**
     * @covers \PhpCsFixer\FixerFactory::getFixers
     * @covers \PhpCsFixer\FixerFactory::sortFixers
     */
    public function testThatFixersAreSorted()
    {
        $factory = new FixerFactory();
        $fxs = array(
            $this->createFixerDouble('f1', 0),
            $this->createFixerDouble('f2', -10),
            $this->createFixerDouble('f3', 10),
            $this->createFixerDouble('f4', -10),
        );

        foreach ($fxs as $fx) {
            $factory->registerFixer($fx, false);
        }

        // There are no rules that forces $fxs[1] to be prioritized before $fxs[3]. We should not test against that
        $this->assertSame(array($fxs[2], $fxs[0]), array_slice($factory->getFixers(), 0, 2));
    }

    /**
     * @covers \PhpCsFixer\FixerFactory::getFixers
     * @covers \PhpCsFixer\FixerFactory::registerCustomFixers
     * @covers \PhpCsFixer\FixerFactory::registerFixer
     */
    public function testThatCanRegisterAndGetFixers()
    {
        $factory = new FixerFactory();

        $f1 = $this->createFixerDouble('f1');
        $f2 = $this->createFixerDouble('Foo/f2');
        $f3 = $this->createFixerDouble('Foo/f3');

        $factory->registerFixer($f1, false);
        $factory->registerCustomFixers(array($f2, $f3));

        $this->assertTrue(in_array($f1, $factory->getFixers(), true));
        $this->assertTrue(in_array($f2, $factory->getFixers(), true));
        $this->assertTrue(in_array($f3, $factory->getFixers(), true));
    }

    /**
     * @covers \PhpCsFixer\FixerFactory::registerFixer
     */
    public function testRegisterFixerWithOccupiedName()
    {
        $this->setExpectedException(
            'UnexpectedValueException',
            'Fixer named "non_unique_name" is already registered.'
        );

        $factory = new FixerFactory();

        $f1 = $this->createFixerDouble('non_unique_name');
        $f2 = $this->createFixerDouble('non_unique_name');
        $factory->registerFixer($f1, false);
        $factory->registerFixer($f2, false);
    }

    /**
     * @covers \PhpCsFixer\FixerFactory::useRuleSet
     */
    public function testUseRuleSet()
    {
        $factory = FixerFactory::create()
            ->registerBuiltInFixers()
            ->useRuleSet(new RuleSet(array()))
        ;
        $this->assertCount(0, $factory->getFixers());

        $factory = FixerFactory::create()
            ->registerBuiltInFixers()
            ->useRuleSet(new RuleSet(array('strict_comparison' => true, 'blank_line_before_return' => false)))
        ;
        $fixers = $factory->getFixers();
        $this->assertCount(1, $fixers);
        $this->assertSame('strict_comparison', $fixers[0]->getName());
    }

    /**
     * @covers \PhpCsFixer\FixerFactory::useRuleSet
     */
    public function testUseRuleSetWithNonExistingRule()
    {
        $this->setExpectedException(
            'UnexpectedValueException',
            'Rule "non_existing_rule" does not exist.'
        );

        $factory = FixerFactory::create()
            ->registerBuiltInFixers()
            ->useRuleSet(new RuleSet(array('non_existing_rule' => true)))
        ;
        $fixers = $factory->getFixers();
        $this->assertCount(1, $fixers);
        $this->assertSame('strict_comparison', $fixers[0]->getName());
    }

    public function testHasRule()
    {
        $factory = new FixerFactory();

        $f1 = $this->createFixerDouble('f1');
        $f2 = $this->createFixerDouble('Foo/f2');
        $f3 = $this->createFixerDouble('Foo/f3');
        $factory->registerFixer($f1, false);
        $factory->registerCustomFixers(array($f2, $f3));

        $this->assertTrue($factory->hasRule('f1'), 'Should have f1 fixer');
        $this->assertTrue($factory->hasRule('Foo/f2'), 'Should have f2 fixer');
        $this->assertTrue($factory->hasRule('Foo/f3'), 'Should have f3 fixer');
        $this->assertFalse($factory->hasRule('dummy'), 'Should not have dummy fixer');
    }

    public function testHasRuleWithChangedRuleSet()
    {
        $factory = new FixerFactory();

        $f1 = $this->createFixerDouble('f1');
        $f2 = $this->createFixerDouble('f2');
        $factory->registerFixer($f1, false);
        $factory->registerFixer($f2, false);

        $this->assertTrue($factory->hasRule('f1'), 'Should have f1 fixer');
        $this->assertTrue($factory->hasRule('f2'), 'Should have f2 fixer');

        $factory->useRuleSet(new RuleSet(array('f2' => true)));
        $this->assertFalse($factory->hasRule('f1'), 'Should not have f1 fixer');
        $this->assertTrue($factory->hasRule('f2'), 'Should have f2 fixer');
    }

    /**
     * @dataProvider provideConflictingFixersCases
     */
    public function testConflictingFixers(RuleSet $ruleSet)
    {
        $this->setExpectedExceptionRegExp(
            'UnexpectedValueException',
            '#^Rule contains conflicting fixers:\n#'
        );

        FixerFactory::create()->registerBuiltInFixers()->useRuleSet($ruleSet);
    }

    public function provideConflictingFixersCases()
    {
        return array(
            array(new RuleSet(array('no_blank_lines_before_namespace' => true, 'single_blank_line_before_namespace' => true))),
            array(new RuleSet(array('single_blank_line_before_namespace' => true, 'no_blank_lines_before_namespace' => true))),
        );
    }

    public function testNoDoubleConflictReporting()
    {
        $factory = new FixerFactory();
        $method = new \ReflectionMethod($factory, 'generateConflictMessage');
        $method->setAccessible(true);
        $this->assertSame(
            'Rule contains conflicting fixers:
- "a" with "b"
- "c" with "d", "e", "f"
- "d" with "g", "h"
- "e" with "a"',
            $method->invoke(
                $factory,
                array(
                    'a' => array('b'),
                    'b' => array('a'),
                    'c' => array('d', 'e', 'f'),
                    'd' => array('c', 'g', 'h'),
                    'e' => array('a'),
                )
            )
        );
    }

    private function createFixerDouble($name, $priority = 0)
    {
        /** @var FixerInterface $fixer */
        $fixer = $this->prophesize('PhpCsFixer\Fixer\FixerInterface');
        $fixer->getName()->willReturn($name);
        $fixer->getPriority()->willReturn($priority);

        return $fixer->reveal();
    }
}
