<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests;

use Symfony\CS\Fixer\Contrib\Psr0Fixer;
use Symfony\CS\FixerFactory;
use Symfony\CS\FixerInterface;
use Symfony\CS\RuleSet;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class FixerFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testInterfaceIsFluent()
    {
        $factory = new FixerFactory();

        $testInstance = $factory->registerBuiltInFixers();
        $this->assertSame($factory, $testInstance);

        $mocks = array($this->getMock('Symfony\CS\FixerInterface'), $this->getMock('Symfony\CS\FixerInterface'));
        $testInstance = $factory->registerCustomFixers($mocks);
        $this->assertSame($factory, $testInstance);

        $mock = $this->getMock('Symfony\CS\FixerInterface');
        $testInstance = $factory->registerFixer($mock);
        $this->assertSame($factory, $testInstance);

        $mock = $this->getMock('Symfony\CS\RuleSetInterface');
        $mock->expects($this->any())->method('getRules')->willReturn(array());
        $testInstance = $factory->useRuleSet($mock);
        $this->assertSame($factory, $testInstance);

        $mock = $this->getMock('Symfony\CS\ConfigInterface');
        $testInstance = $factory->attachConfig($mock);
        $this->assertSame($factory, $testInstance);
    }

    /**
     * @covers Symfony\CS\FixerFactory::create
     */
    public function testCreate()
    {
        $factory = FixerFactory::create();

        $this->assertInstanceOf('Symfony\CS\FixerFactory', $factory);
    }

    /**
     * @covers Symfony\CS\FixerFactory::registerBuiltInFixers
     */
    public function testRegisterBuiltInFixers()
    {
        $factory = new FixerFactory();
        $factory->registerBuiltInFixers();

        $this->assertGreaterThan(0, count($factory->getFixers()));
    }

    /**
     * @covers Symfony\CS\FixerFactory::attachConfig
     */
    public function testMethodAttachConfig()
    {
        $factory = new FixerFactory();

        $fixer = new Psr0Fixer();
        $factory->registerFixer($fixer);

        $mock = $this->getMock('Symfony\CS\ConfigInterface');
        $testInstance = $factory->attachConfig($mock);

        $classReflection = new \ReflectionClass($fixer);
        $propertyReflection = $classReflection->getProperty('config');
        $propertyReflection->setAccessible(true);
        $property = $propertyReflection->getValue($fixer);

        $this->assertSame($mock, $property);
    }

    /**
     * @covers Symfony\CS\FixerFactory::getFixers
     * @covers Symfony\CS\FixerFactory::sortFixers
     */
    public function testThatFixersAreSorted()
    {
        $factory = new FixerFactory();

        $fxPrototypes = array(
            array('getPriority' => 0),
            array('getPriority' => -10),
            array('getPriority' => 10),
            array('getPriority' => -10),
        );

        $fxs = array();

        foreach ($fxPrototypes as $fxPrototype) {
            $fx = $this->getMock('Symfony\CS\FixerInterface');
            $fx->expects($this->any())->method('getPriority')->willReturn($fxPrototype['getPriority']);

            $factory->registerFixer($fx);
            $fxs[] = $fx;
        }

        // There are no rules that forces $fxs[1] to be prioritized before $fxs[3]. We should not test against that
        $this->assertSame(array($fxs[2], $fxs[0]), array_slice($factory->getFixers(), 0, 2));
    }

    /**
     * @covers Symfony\CS\FixerFactory::getFixers
     * @covers Symfony\CS\FixerFactory::registerCustomFixers
     * @covers Symfony\CS\FixerFactory::registerFixer
     */
    public function testThatCanRegisterAndGetFixers()
    {
        $factory = new FixerFactory();
        $factory->registerBuiltInFixers();

        $f1 = $this->getMock('Symfony\CS\FixerInterface');
        $f2 = $this->getMock('Symfony\CS\FixerInterface');
        $f3 = $this->getMock('Symfony\CS\FixerInterface');
        $factory->registerFixer($f1);
        $factory->registerCustomFixers(array($f2, $f3));

        $this->assertTrue(in_array($f1, $factory->getFixers(), true));
        $this->assertTrue(in_array($f2, $factory->getFixers(), true));
        $this->assertTrue(in_array($f3, $factory->getFixers(), true));
    }

    /**
     * @covers Symfony\CS\FixerFactory::useRuleSet
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
            ->useRuleSet(new RuleSet(array('strict' => true, 'return' => false)))
        ;
        $fixers = $factory->getFixers();
        $this->assertCount(1, $fixers);
        $this->assertSame('strict', $fixers[0]->getName());
    }

    /**
     * @covers Symfony\CS\FixerFactory::useRuleSet
     * @expectedException        \UnexpectedValueException
     * @expectedExceptionMessage Rule "non_existing_rule" does not exist.
     */
    public function testUseRuleSetWithNonExistingRule()
    {
        $factory = FixerFactory::create()
            ->registerBuiltInFixers()
            ->useRuleSet(new RuleSet(array('non_existing_rule' => true)))
        ;
        $fixers = $factory->getFixers();
        $this->assertCount(1, $fixers);
        $this->assertSame('strict', $fixers[0]->getName());
    }

    public function testFixersPriorityEdgeFixers()
    {
        $factory = new FixerFactory();
        $factory->registerBuiltInFixers();
        $fixers = $factory->getFixers();

        $this->assertSame('encoding', $fixers[0]->getName());
        $this->assertSame('eof_ending', $fixers[count($fixers) - 1]->getName());
    }

    /**
     * @dataProvider getFixersPriorityCases
     */
    public function testFixersPriority(FixerInterface $first, FixerInterface $second)
    {
        $this->assertLessThan($first->getPriority(), $second->getPriority());
    }

    public function getFixersPriorityCases()
    {
        $factory = new FixerFactory();
        $factory->registerBuiltInFixers();

        $fixers = array();

        foreach ($factory->getFixers() as $fixer) {
            $fixers[$fixer->getName()] = $fixer;
        }

        $cases = array(
            array($fixers['php_closing_tag'], $fixers['short_tag']),
            array($fixers['unused_use'], $fixers['extra_empty_lines']),
            array($fixers['multiple_use'], $fixers['unused_use']),
            array($fixers['multiple_use'], $fixers['ordered_use']),
            array($fixers['remove_lines_between_uses'], $fixers['ordered_use']),
            array($fixers['unused_use'], $fixers['remove_leading_slash_use']),
            array($fixers['multiple_use'], $fixers['remove_leading_slash_use']),
            array($fixers['concat_without_spaces'], $fixers['concat_with_spaces']),
            array($fixers['elseif'], $fixers['braces']),
            array($fixers['duplicate_semicolon'], $fixers['braces']),
            array($fixers['duplicate_semicolon'], $fixers['spaces_before_semicolon']),
            array($fixers['duplicate_semicolon'], $fixers['multiline_spaces_before_semicolon']),
            array($fixers['standardize_not_equal'], $fixers['strict']),
            array($fixers['double_arrow_multiline_whitespaces'], $fixers['multiline_array_trailing_comma']),
            array($fixers['double_arrow_multiline_whitespaces'], $fixers['align_double_arrow']),
            array($fixers['operators_spaces'], $fixers['align_double_arrow']), // tested also in: align_double_arrow,operators_spaces.test
            array($fixers['operators_spaces'], $fixers['align_equals']), // tested also in: align_double_arrow,align_equals.test
            array($fixers['indentation'], $fixers['phpdoc_indent']),
            array($fixers['phpdoc_order'], $fixers['phpdoc_separation']),
            array($fixers['phpdoc_no_access'], $fixers['phpdoc_separation']),
            array($fixers['phpdoc_no_access'], $fixers['phpdoc_order']),
            array($fixers['phpdoc_no_empty_return'], $fixers['phpdoc_separation']),
            array($fixers['phpdoc_no_empty_return'], $fixers['phpdoc_order']),
            array($fixers['phpdoc_no_package'], $fixers['phpdoc_separation']),
            array($fixers['phpdoc_no_package'], $fixers['phpdoc_order']),
            array($fixers['phpdoc_no_access'], $fixers['phpdoc_trim']),
            array($fixers['phpdoc_no_empty_return'], $fixers['phpdoc_trim']),
            array($fixers['phpdoc_no_package'], $fixers['phpdoc_trim']),
            array($fixers['phpdoc_separation'], $fixers['phpdoc_trim']),
            array($fixers['phpdoc_short_description'], $fixers['phpdoc_trim']),
            array($fixers['phpdoc_var_without_name'], $fixers['phpdoc_trim']),
            array($fixers['phpdoc_order'], $fixers['phpdoc_trim']),
            array($fixers['unused_use'], $fixers['line_after_namespace']),
            array($fixers['linefeed'], $fixers['eof_ending']),
            array($fixers['php_unit_strict'], $fixers['php_unit_construct']),
            array($fixers['unary_operators_spaces'], $fixers['logical_not_operators_with_spaces']),
            array($fixers['unary_operators_spaces'], $fixers['logical_not_operators_with_successor_space']),
            array($fixers['method_separation'], $fixers['braces']),
            array($fixers['method_separation'], $fixers['indentation']),
        );

        // prepare bulk tests for phpdoc fixers to test that:
        // * `phpdoc_to_comment` is first
        // * `phpdoc_indent` is second
        // * `phpdoc_types` is third
        // * `phpdoc_scalar` is fourth
        // * `phpdoc_align` is last
        $cases[] = array($fixers['phpdoc_to_comment'], $fixers['phpdoc_indent']);
        $cases[] = array($fixers['phpdoc_indent'], $fixers['phpdoc_types']);
        $cases[] = array($fixers['phpdoc_types'], $fixers['phpdoc_scalar']);

        $docFixerNames = array_filter(
            array_keys($fixers),
            function ($name) {
                return false !== strpos($name, 'phpdoc');
            }
        );

        foreach ($docFixerNames as $docFixerName) {
            if (!in_array($docFixerName, array('phpdoc_to_comment', 'phpdoc_indent', 'phpdoc_types', 'phpdoc_scalar'), true)) {
                $cases[] = array($fixers['phpdoc_to_comment'], $fixers[$docFixerName]);
                $cases[] = array($fixers['phpdoc_indent'], $fixers[$docFixerName]);
                $cases[] = array($fixers['phpdoc_types'], $fixers[$docFixerName]);
                $cases[] = array($fixers['phpdoc_scalar'], $fixers[$docFixerName]);
            }

            if ('phpdoc_align' !== $docFixerName) {
                $cases[] = array($fixers[$docFixerName], $fixers['phpdoc_align']);
            }
        }

        return $cases;
    }

    public function testHasRule()
    {
        $factory = new FixerFactory();
        $factory->registerBuiltInFixers();

        $f1 = $this->getMock('Symfony\CS\FixerInterface');
        $f1->expects($this->any())->method('getName')->willReturn('f1');
        $f2 = $this->getMock('Symfony\CS\FixerInterface');
        $f2->expects($this->any())->method('getName')->willReturn('f2');
        $f3 = $this->getMock('Symfony\CS\FixerInterface');
        $f3->expects($this->any())->method('getName')->willReturn('f3');
        $factory->registerFixer($f1);
        $factory->registerCustomFixers(array($f2, $f3));

        $this->assertTrue($factory->hasRule('f1'), 'Should have f1 fixer');
        $this->assertTrue($factory->hasRule('f2'), 'Should have f2 fixer');
        $this->assertTrue($factory->hasRule('f3'), 'Should have f3 fixer');
        $this->assertFalse($factory->hasRule('dummy'), 'Should not have dummy fixer');
    }

    public function testHasRuleWithChangedRuleSet()
    {
        $factory = new FixerFactory();
        $factory->registerBuiltInFixers();

        $f1 = $this->getMock('Symfony\CS\FixerInterface');
        $f1->expects($this->any())->method('getName')->willReturn('f1');
        $f2 = $this->getMock('Symfony\CS\FixerInterface');
        $f2->expects($this->any())->method('getName')->willReturn('f2');
        $factory->registerFixer($f1);
        $factory->registerFixer($f2);

        $this->assertTrue($factory->hasRule('f1'), 'Should have f1 fixer');
        $this->assertTrue($factory->hasRule('f2'), 'Should have f2 fixer');

        $factory->useRuleSet(new RuleSet(array('f2' => true)));
        $this->assertFalse($factory->hasRule('f1'), 'Should not have f1 fixer');
        $this->assertTrue($factory->hasRule('f2'), 'Should have f2 fixer');
    }
}
