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

use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\FixerDefinition\VersionSpecificCodeSampleInterface;
use PhpCsFixer\FixerFactory;
use PhpCsFixer\RuleSet;
use PhpCsFixer\StdinFileInfo;
use PhpCsFixer\Tokenizer\Tokens;

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

    public function testFixersPriorityEdgeFixers()
    {
        $factory = new FixerFactory();
        $factory->registerBuiltInFixers();
        $fixers = $factory->getFixers();

        $this->assertSame('encoding', $fixers[0]->getName());
        $this->assertSame('full_opening_tag', $fixers[1]->getName());
        $this->assertSame('single_blank_line_at_eof', $fixers[count($fixers) - 1]->getName());
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
            array($fixers['elseif'], $fixers['braces']),
            array($fixers['method_separation'], $fixers['braces']),
            array($fixers['method_separation'], $fixers['indentation_type']),
            array($fixers['no_leading_import_slash'], $fixers['ordered_imports']), // tested also in: no_leading_import_slash,ordered_imports.test
            array($fixers['no_multiline_whitespace_around_double_arrow'], $fixers['binary_operator_spaces']), // tested also in: no_multiline_whitespace_around_double_arrow,binary_operator_spaces.test
            array($fixers['no_multiline_whitespace_around_double_arrow'], $fixers['trailing_comma_in_multiline_array']), // tested also in: no_multiline_whitespace_around_double_arrow,trailing_comma_in_multiline_array.test
            array($fixers['no_php4_constructor'], $fixers['ordered_class_elements']), // tested also in: no_php4_constructor,ordered_class_elements.test
            array($fixers['no_short_bool_cast'], $fixers['cast_spaces']), // tested also in: no_short_bool_cast,cast_spaces.test
            array($fixers['no_short_echo_tag'], $fixers['no_mixed_echo_print']), // tested also in: no_mixed_echo_print,no_short_echo_tag.test
            array($fixers['indentation_type'], $fixers['phpdoc_indent']),
            array($fixers['no_unneeded_control_parentheses'], $fixers['no_trailing_whitespace']), // tested also in: no_trailing_whitespace,no_unneeded_control_parentheses.test
            array($fixers['no_unused_imports'], $fixers['blank_line_after_namespace']), // tested also in: no_unused_imports,blank_line_after_namespace.test and no_unused_imports,blank_line_after_namespace_2.test
            array($fixers['no_unused_imports'], $fixers['no_extra_consecutive_blank_lines']), // tested also in: no_unused_imports,no_extra_consecutive_blank_lines.test
            array($fixers['no_unused_imports'], $fixers['no_leading_import_slash']), // no priority issue; for speed only
            array($fixers['ordered_class_elements'], $fixers['method_separation']), // tested also in: ordered_class_elements,method_separation.test
            array($fixers['ordered_class_elements'], $fixers['no_blank_lines_after_class_opening']), // tested also in: ordered_class_elements,no_blank_lines_after_class_opening.test
            array($fixers['ordered_class_elements'], $fixers['space_after_semicolon']), // tested also in: ordered_class_elements,space_after_semicolon.test
            array($fixers['php_unit_strict'], $fixers['php_unit_construct']),
            array($fixers['phpdoc_no_access'], $fixers['phpdoc_order']),
            array($fixers['phpdoc_no_access'], $fixers['phpdoc_separation']),
            array($fixers['phpdoc_no_access'], $fixers['phpdoc_trim']),
            array($fixers['phpdoc_no_empty_return'], $fixers['phpdoc_order']), // tested also in: phpdoc_no_empty_return,phpdoc_separation.test
            array($fixers['phpdoc_no_empty_return'], $fixers['phpdoc_separation']), // tested also in: phpdoc_no_empty_return,phpdoc_separation.test
            array($fixers['phpdoc_no_empty_return'], $fixers['phpdoc_trim']),
            array($fixers['phpdoc_no_package'], $fixers['phpdoc_order']),
            array($fixers['phpdoc_no_package'], $fixers['phpdoc_separation']), // tested also in: phpdoc_no_package,phpdoc_separation.test
            array($fixers['phpdoc_no_package'], $fixers['phpdoc_trim']),
            array($fixers['phpdoc_order'], $fixers['phpdoc_separation']),
            array($fixers['phpdoc_order'], $fixers['phpdoc_trim']),
            array($fixers['phpdoc_separation'], $fixers['phpdoc_trim']),
            array($fixers['phpdoc_summary'], $fixers['phpdoc_trim']),
            array($fixers['phpdoc_var_without_name'], $fixers['phpdoc_trim']),
            array($fixers['pow_to_exponentiation'], $fixers['binary_operator_spaces']), // tested also in: pow_to_exponentiation,binary_operator_spaces.test
            array($fixers['pow_to_exponentiation'], $fixers['method_argument_space']), // no priority issue; for speed only
            array($fixers['pow_to_exponentiation'], $fixers['native_function_casing']), // no priority issue; for speed only
            array($fixers['pow_to_exponentiation'], $fixers['no_spaces_after_function_name']), // no priority issue; for speed only
            array($fixers['pow_to_exponentiation'], $fixers['no_spaces_inside_parenthesis']), // no priority issue; for speed only
            array($fixers['single_import_per_statement'], $fixers['ordered_imports']), // tested also in: single_import_per_statement,ordered_imports.test
            array($fixers['single_import_per_statement'], $fixers['no_singleline_whitespace_before_semicolons']), // tested also in: single_import_per_statement,no_singleline_whitespace_before_semicolons.test
            array($fixers['single_import_per_statement'], $fixers['space_after_semicolon']), // tested also in: single_import_per_statement,space_after_semicolon.test
            array($fixers['single_import_per_statement'], $fixers['no_multiline_whitespace_before_semicolons']), // single_import_per_statement,no_multiline_whitespace_before_semicolons.test
            array($fixers['single_import_per_statement'], $fixers['no_leading_import_slash']), // tested also in: single_import_per_statement,no_leading_import_slash.test
            array($fixers['single_import_per_statement'], $fixers['no_unused_imports']), // tested also in: single_import_per_statement,no_unused_imports.test
            array($fixers['unary_operator_spaces'], $fixers['not_operator_with_space']),
            array($fixers['unary_operator_spaces'], $fixers['not_operator_with_successor_space']),
            array($fixers['line_ending'], $fixers['single_blank_line_at_eof']),
            array($fixers['simplified_null_return'], $fixers['no_useless_return']), // tested also in: simplified_null_return,no_useless_return.test
            array($fixers['no_useless_return'], $fixers['no_whitespace_in_blank_line']), // tested also in: no_useless_return,no_whitespace_in_blank_line.test
            array($fixers['no_useless_return'], $fixers['no_extra_consecutive_blank_lines']), // tested also in: no_useless_return,no_extra_consecutive_blank_lines.test
            array($fixers['no_useless_return'], $fixers['blank_line_before_return']), // tested also in: no_useless_return,blank_line_before_return.test
            array($fixers['no_empty_phpdoc'], $fixers['no_extra_consecutive_blank_lines']), // tested also in: no_empty_phpdoc,no_extra_consecutive_blank_lines.test
            array($fixers['no_empty_phpdoc'], $fixers['no_trailing_whitespace']), // tested also in: no_empty_phpdoc,no_trailing_whitespace.test
            array($fixers['no_empty_phpdoc'], $fixers['no_whitespace_in_blank_line']), // tested also in: no_empty_phpdoc,no_whitespace_in_blank_line.test
            array($fixers['phpdoc_no_access'], $fixers['no_empty_phpdoc']), // tested also in: phpdoc_no_access,no_empty_phpdoc.test
            array($fixers['phpdoc_no_empty_return'], $fixers['no_empty_phpdoc']), // tested also in: phpdoc_no_empty_return,no_empty_phpdoc.test
            array($fixers['phpdoc_no_package'], $fixers['no_empty_phpdoc']), // tested also in: phpdoc_no_package,no_empty_phpdoc.test
            array($fixers['combine_consecutive_unsets'], $fixers['space_after_semicolon']), // tested also in: combine_consecutive_unsets,space_after_semicolon.test
            array($fixers['combine_consecutive_unsets'], $fixers['no_whitespace_in_blank_line']), // tested also in: combine_consecutive_unsets,no_whitespace_in_blank_line.test
            array($fixers['combine_consecutive_unsets'], $fixers['no_trailing_whitespace']), // tested also in: combine_consecutive_unsets,no_trailing_whitespace.test
            array($fixers['combine_consecutive_unsets'], $fixers['no_extra_consecutive_blank_lines']), // tested also in: combine_consecutive_unsets,no_extra_consecutive_blank_lines.test
            array($fixers['phpdoc_no_alias_tag'], $fixers['phpdoc_single_line_var_spacing']), // tested also in: phpdoc_no_alias_tag,phpdoc_single_line_var_spacing.test
            array($fixers['blank_line_after_opening_tag'], $fixers['no_blank_lines_before_namespace']), // tested also in: blank_line_after_opening_tag,no_blank_lines_before_namespace.test
            array($fixers['phpdoc_to_comment'], $fixers['no_empty_comment']), // tested also in: phpdoc_to_comment,no_empty_comment.test
            array($fixers['no_empty_comment'], $fixers['no_extra_consecutive_blank_lines']), // tested also in: no_empty_comment,no_extra_consecutive_blank_lines.test
            array($fixers['no_empty_comment'], $fixers['no_trailing_whitespace']), // tested also in: no_empty_comment,no_trailing_whitespace.test
            array($fixers['no_empty_comment'], $fixers['no_whitespace_in_blank_line']), // tested also in: no_empty_comment,no_whitespace_in_blank_line.test
            array($fixers['no_alias_functions'], $fixers['php_unit_dedicate_assert']), // tested also in: no_alias_functions,php_unit_dedicate_assert.test
            array($fixers['no_empty_statement'], $fixers['braces']),
            array($fixers['no_empty_statement'], $fixers['combine_consecutive_unsets']), // tested also in: no_empty_statement,combine_consecutive_unsets.test
            array($fixers['no_empty_statement'], $fixers['no_extra_consecutive_blank_lines']), // tested also in: no_empty_statement,no_extra_consecutive_blank_lines.test
            array($fixers['no_empty_statement'], $fixers['no_multiline_whitespace_before_semicolons']),
            array($fixers['no_empty_statement'], $fixers['no_singleline_whitespace_before_semicolons']),
            array($fixers['no_empty_statement'], $fixers['no_trailing_whitespace']), // tested also in: no_empty_statement,no_trailing_whitespace.test
            array($fixers['no_empty_statement'], $fixers['no_useless_else']), // tested also in: no_empty_statement,no_useless_else.test
            array($fixers['no_empty_statement'], $fixers['no_useless_return']), // tested also in: no_empty_statement,no_useless_return.test
            array($fixers['no_empty_statement'], $fixers['no_whitespace_in_blank_line']), // tested also in: no_empty_statement,no_whitespace_in_blank_line.test
            array($fixers['no_empty_statement'], $fixers['space_after_semicolon']), // tested also in: no_empty_statement,space_after_semicolon.test
            array($fixers['no_empty_statement'], $fixers['switch_case_semicolon_to_colon']), // tested also in: no_empty_statement,switch_case_semicolon_to_colon.test
            array($fixers['no_useless_else'], $fixers['braces']),
            array($fixers['no_useless_else'], $fixers['combine_consecutive_unsets']), // tested also in: no_useless_else,combine_consecutive_unsets.test
            array($fixers['no_useless_else'], $fixers['no_extra_consecutive_blank_lines']), // tested also in: no_useless_else,no_extra_consecutive_blank_lines.test
            array($fixers['no_useless_else'], $fixers['no_useless_return']), // tested also in: no_useless_else,no_useless_return.test
            array($fixers['no_useless_else'], $fixers['no_trailing_whitespace']), // tested also in: no_useless_else,no_trailing_whitespace.test
            array($fixers['no_useless_else'], $fixers['no_whitespace_in_blank_line']), // tested also in: no_useless_else,no_whitespace_in_blank_line.test
            array($fixers['declare_strict_types'], $fixers['single_blank_line_before_namespace']), // tested also in: declare_strict_types,single_blank_line_before_namespace.test
            array($fixers['declare_strict_types'], $fixers['blank_line_after_opening_tag']), // tested also in: declare_strict_types,blank_line_after_opening_tag.test
            array($fixers['array_syntax'], $fixers['binary_operator_spaces']), // tested also in: array_syntax,binary_operator_spaces.test
            array($fixers['array_syntax'], $fixers['ternary_operator_spaces']), // tested also in: array_syntax,ternary_operator_spaces.test
            array($fixers['class_keyword_remove'], $fixers['no_unused_imports']), // tested also in: class_keyword_remove,no_unused_imports.test
            array($fixers['no_blank_lines_after_phpdoc'], $fixers['single_blank_line_before_namespace']), // tested also in: no_blank_lines_after_phpdoc,single_blank_line_before_namespace.test
            array($fixers['php_unit_fqcn_annotation'], $fixers['no_unused_imports']), // tested also in: php_unit_fqcn_annotation,unused_use.test
            array($fixers['protected_to_private'], $fixers['ordered_class_elements']), // tested also in: protected_to_private,ordered_class_elements.test
            array($fixers['phpdoc_add_missing_param_annotation'], $fixers['phpdoc_align']), // tested also in: phpdoc_add_missing_param_annotation,phpdoc_align.test
            array($fixers['phpdoc_no_alias_tag'], $fixers['phpdoc_add_missing_param_annotation']), // tested also in: phpdoc_no_alias_tag,phpdoc_add_missing_param_annotation.test
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
     * @param FixerInterface $fixer
     *
     * @dataProvider provideFixerDefinitionsCases
     */
    public function testFixerDefinitions(FixerInterface $fixer)
    {
        $this->assertInstanceOf('PhpCsFixer\Fixer\DefinedFixerInterface', $fixer);

        $definition = $fixer->getDefinition();

        $this->assertRegExp('/^[A-Z@].*\.$/', $definition->getSummary(), sprintf('[%s] Description must start with capital letter or an @ and end with dot.', $fixer->getName()));

        $samples = $definition->getCodeSamples();
        $this->assertNotEmpty($samples, sprintf('[%s] Code samples are required.', $fixer->getName()));

        $dummyFileInfo = new StdinFileInfo();
        $sampleCounter = 0;
        foreach ($samples as $sample) {
            ++$sampleCounter;
            $this->assertInstanceOf('PhpCsFixer\FixerDefinition\CodeSampleInterface', $sample, sprintf('[%s] Sample #%d', $fixer->getName(), $sampleCounter));
            $code = $sample->getCode();
            $this->assertStringIsNotEmpty($code, sprintf('[%s] Sample #%d', $fixer->getName(), $sampleCounter));

            if ($sample instanceof VersionSpecificCodeSampleInterface && !$sample->isSuitableFor(PHP_VERSION_ID)) {
                continue;
            }

            $config = $sample->getConfiguration();
            if (null !== $config) {
                $this->assertInternalType('array', $config, sprintf('[%s] Sample #%d configuration must be an array or null.', $fixer->getName(), $sampleCounter));
                if ($fixer instanceof ConfigurableFixerInterface) {
                    $fixer->configure($config);
                } else {
                    $this->assertInternalType('array', $config, sprintf('[%s] Sample #%d has configuration, but the fixer is not configurable.', $fixer->getName(), $sampleCounter));
                }
            }

            Tokens::clearCache();
            $tokens = Tokens::fromCode($code);
            $fixer->fix($dummyFileInfo, $tokens);
            $this->assertTrue($tokens->isChanged(), sprintf('[%s] Sample #%d is not changed during fixing.', $fixer->getName(), $sampleCounter));
        }

        if ($fixer instanceof ConfigurableFixerInterface) {
            $this->assertStringIsNotEmpty($definition->getConfigurationDescription(), sprintf('[%s] Configuration description is required.', $fixer->getName()));
            $default = $definition->getDefaultConfiguration();
            $this->assertInternalType('array', $default, sprintf('[%s] Default configuration must be an array.', $fixer->getName()));
            $this->assertNotEmpty('array', $default, sprintf('[%s] Default configuration is required.', $fixer->getName()));
        } else {
            $this->assertNull($definition->getConfigurationDescription(), sprintf('[%s] No configuration description expected.', $fixer->getName()));
            $this->assertNull($definition->getDefaultConfiguration(), sprintf('[%s] No default configuration expected.', $fixer->getName()));
        }

        if ($fixer->isRisky()) {
            $this->assertStringIsNotEmpty($definition->getRiskyDescription(), sprintf('[%s] Risky reasoning is required.', $fixer->getName()));
        } else {
            $this->assertNull($definition->getRiskyDescription(), sprintf('[%s] Fixer is not risky so no description of it expected.', $fixer->getName()));
        }
    }

    public function provideFixerDefinitionsCases()
    {
        return array_map(function (FixerInterface $fixer) {
            return array($fixer);
        }, $this->getAllFixers());
    }

    /**
     * @dataProvider provideFixerDefinitionsCases
     */
    public function testFixersAreFinal(FixerInterface $fixer)
    {
        $reflection = new \ReflectionClass($fixer);

        $this->assertTrue($reflection->isFinal(), sprintf('Fixer "%s" must be declared "final".', $fixer->getName()));
    }

    /**
     * @dataProvider provideFixerDefinitionsCases
     */
    public function testFixersAreDefined(FixerInterface $fixer)
    {
        $this->assertInstanceOf('PhpCsFixer\Fixer\DefinedFixerInterface', $fixer);
    }

    /**
     * @dataProvider provideConflictingFixersRules
     */
    public function testConflictingFixers(RuleSet $ruleSet)
    {
        $this->setExpectedExceptionRegExp(
            'UnexpectedValueException',
            '#^Rule contains conflicting fixers:\n#'
        );

        FixerFactory::create()->registerBuiltInFixers()->useRuleSet($ruleSet);
    }

    public function provideConflictingFixersRules()
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

    private function getAllFixers()
    {
        $factory = new FixerFactory();

        return $factory->registerBuiltInFixers()->getFixers();
    }

    private function createFixerDouble($name, $priority = 0)
    {
        /** @var FixerInterface $fixer */
        $fixer = $this->prophesize('PhpCsFixer\Fixer\FixerInterface');
        $fixer->getName()->willReturn($name);
        $fixer->getPriority()->willReturn($priority);
        //$fixer->configure(Argument::is(null))->willReturn(null); Needed?

        return $fixer->reveal();
    }

    /**
     * copy paste from GeckoPackages/GeckoPHPUnit StringsAssertTrait, to replace with Trait when possible.
     *
     * @param mixed $actual
     * @param mixed $message
     */
    private static function assertStringIsNotEmpty($actual, $message = '')
    {
        self::assertThat($actual, new \PHPUnit_Framework_Constraint_IsType('string'), $message);
        self::assertNotEmpty($actual, $message);
    }
}
