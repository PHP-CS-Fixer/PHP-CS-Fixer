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

namespace PhpCsFixer\Tests\AutoReview;

use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\FixerFactory;
use PhpCsFixer\Tests\Test\IntegrationCaseFactory;
use PhpCsFixer\Tests\TestCase;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @coversNothing
 *
 * @group auto-review
 * @group covers-nothing
 */
final class FixerFactoryTest extends TestCase
{
    public function testFixersPriorityEdgeFixers(): void
    {
        $factory = new FixerFactory();
        $factory->registerBuiltInFixers();
        $fixers = $factory->getFixers();

        foreach (self::getFixerWithFixedPosition() as $fixerName => $offset) {
            if ($offset < 0) {
                static::assertSame($fixerName, $fixers[\count($fixers) + $offset]->getName(), $fixerName);
            } else {
                static::assertSame($fixerName, $fixers[$offset]->getName(), $fixerName);
            }
        }
    }

    public function testFixersPriority(): void
    {
        $fixers = self::getAllFixers();

        $graphs = [
            self::getFixersPriorityGraph(),
            self::getPhpDocFixersPriorityGraph(),
        ];

        foreach ($graphs as $graph) {
            foreach ($graph as $fixerName => $edges) {
                $first = $fixers[$fixerName];

                foreach ($edges as $edge) {
                    $second = $fixers[$edge];

                    static::assertLessThan($first->getPriority(), $second->getPriority(), sprintf('"%s" should have less priority than "%s"', $edge, $fixerName));
                }
            }
        }
    }

    /**
     * @param string[] $edges
     *
     * @dataProvider provideFixersPriorityCasesHaveIntegrationCases
     */
    public function testFixersPriorityCasesHaveIntegrationTest(string $fixerName, array $edges): void
    {
        static $forPerformanceEdgesOnly = [
            'function_to_constant' => [
                'native_function_casing' => true,
            ],
            'no_unused_imports' => [
                'no_leading_import_slash' => true,
            ],
            'no_useless_sprintf' => [
                'native_function_casing' => true,
            ],
            'pow_to_exponentiation' => [
                'method_argument_space' => true,
                'native_function_casing' => true,
                'no_spaces_after_function_name' => true,
                'no_spaces_inside_parenthesis' => true,
            ],
        ];

        $missingIntegrationsTests = [];

        foreach ($edges as $edge) {
            if (isset($forPerformanceEdgesOnly[$fixerName][$edge])) {
                continue;
            }

            $file = self::getIntegrationPriorityDirectory().$fixerName.','.$edge.'.test';

            if (!is_file($file)) {
                $missingIntegrationsTests[] = $file;

                continue;
            }

            $file = realpath($file);
            $factory = new IntegrationCaseFactory();
            $test = $factory->create(new SplFileInfo($file, './', __DIR__));
            $rules = $test->getRuleset()->getRules();
            $expected = [$fixerName, $edge];
            $actual = array_keys($rules);

            sort($expected);
            sort($actual);

            static::assertSame(
                sprintf('Integration of fixers: %s,%s.', $fixerName, $edge),
                $test->getTitle(),
                sprintf('Please fix the title in "%s".', $file)
            );

            static::assertCount(2, $rules, sprintf('Only the two rules that are tested for priority should be in the ruleset of "%s".', $file));

            foreach ($rules as $name => $config) {
                static::assertNotFalse($config, sprintf('The rule "%s" in "%s" may not be disabled for the test.', $name, $file));
            }

            static::assertSame($expected, $actual, sprintf('The ruleset of "%s" must contain the rules for the priority test.', $file));
        }

        static::assertCount(0, $missingIntegrationsTests, sprintf("There shall be an integration test. How do you know that priority set up is good, if there is no integration test to check it?\nMissing:\n- %s", implode("\n- ", $missingIntegrationsTests)));
    }

    public function provideFixersPriorityCasesHaveIntegrationCases(): iterable
    {
        foreach (self::getFixersPriorityGraph() as $fixerName => $edges) {
            yield $fixerName => [$fixerName, $edges];
        }
    }

    /**
     * @dataProvider provideIntegrationTestFilesCases
     */
    public function testPriorityIntegrationTestFilesAreListedAsPriorityCases(\SplFileInfo $file): void
    {
        $fileName = $file->getFilename();

        static::assertTrue($file->isFile(), sprintf('Expected only files in the priority integration test directory, got "%s".', $fileName));
        static::assertFalse($file->isLink(), sprintf('No (sym)links expected the priority integration test directory, got "%s".', $fileName));
        static::assertSame(
            1,
            preg_match('#^([a-z][a-z0-9_]*),([a-z][a-z_]*)(?:_\d{1,3})?\.test(-(in|out)\.php)?$#', $fileName, $matches),
            sprintf('File with unexpected name "%s" in the priority integration test directory.', $fileName)
        );

        [, $fixerName1, $fixerName2] = $matches;
        $graph = self::getFixersPriorityGraph();

        static::assertTrue(
            isset($graph[$fixerName1]) && \in_array($fixerName2, $graph[$fixerName1], true),
            sprintf('Missing priority test entry for file "%s".', $fileName)
        );
    }

    public function provideIntegrationTestFilesCases(): iterable
    {
        foreach (new \DirectoryIterator(self::getIntegrationPriorityDirectory()) as $candidate) {
            if (!$candidate->isDot()) {
                yield [clone $candidate];
            }
        }
    }

    public function testFixersPriorityGraphIsSorted(): void
    {
        $previous = '';

        foreach (self::getFixersPriorityGraph() as $fixerName => $edges) {
            static::assertLessThan(0, strcmp($previous, $fixerName), sprintf('Not sorted "%s" "%s".', $previous, $fixerName));

            $edgesSorted = $edges;
            sort($edgesSorted);

            static::assertSame($edgesSorted, $edges, sprintf('Fixer "%s" edges are not sorted', $fixerName));
            $previous = $fixerName;
        }
    }

    public function testFixersPriorityComment(): void
    {
        $fixersPhpDocIssues = [];
        $fixers = self::getAllFixers();

        foreach ($fixers as $name => $fixer) {
            $reflection = new \ReflectionObject($fixer);
            $fixers[$name] = ['reflection' => $reflection, 'short_classname' => $reflection->getShortName()];
        }

        $mergedGraph = array_merge_recursive(
            self::getFixersPriorityGraph(),
            self::getPhpDocFixersPriorityGraph()
        );

        // expend $graph

        $graph = [];

        foreach ($mergedGraph as $fixerName => $edges) {
            if (!isset($graph[$fixerName]['before'])) {
                $graph[$fixerName] = ['before' => []];
            }

            foreach ($mergedGraph as $candidateFixer => $candidateEdges) {
                if (\in_array($fixerName, $candidateEdges, true)) {
                    $graph[$fixerName]['after'][$candidateFixer] = true;
                }
            }

            foreach ($edges as $edge) {
                if (!isset($graph[$edge]['after'])) {
                    $graph[$edge] = ['after' => []];
                }

                $graph[$edge]['after'][$fixerName] = true;
                $graph[$fixerName]['before'][$edge] = true;
            }
        }

        foreach ($graph as $fixerName => $edges) {
            $expectedMessage = "/**\n     * {@inheritdoc}\n     *";

            foreach ($edges as $label => $others) {
                if (\count($others) > 0) {
                    $shortClassNames = [];

                    foreach ($others as $other => $foo) {
                        $shortClassNames[$other] = $fixers[$other]['short_classname'];
                    }

                    sort($shortClassNames);
                    $expectedMessage .= sprintf("\n     * Must run %s %s.", $label, implode(', ', $shortClassNames));
                }
            }

            $expectedMessage .= "\n     */";

            $method = $fixers[$fixerName]['reflection']->getMethod('getPriority');
            $phpDoc = $method->getDocComment();

            if (false === $phpDoc) {
                $fixersPhpDocIssues[$fixerName] = sprintf("PHPDoc for %s::getPriority is missing.\nExpected:\n%s", $fixerName, $expectedMessage);
            } elseif ($expectedMessage !== $phpDoc) {
                $fixersPhpDocIssues[$fixerName] = sprintf("PHPDoc for %s::getPriority is not as expected.\nExpected:\n%s", $fixerName, $expectedMessage);
            }
        }

        if (0 === \count($fixersPhpDocIssues)) {
            $this->addToAssertionCount(1);
        } else {
            $message = sprintf("There are %d priority PHPDoc issues found.\n", \count($fixersPhpDocIssues));
            ksort($fixersPhpDocIssues);

            foreach ($fixersPhpDocIssues as $fixerName => $issue) {
                $message .= sprintf("\n--------------------------------------------------\n%s\n%s", $fixers[$fixerName]['short_classname'], $issue);
            }

            static::fail($message);
        }
    }

    public function testFixerWithNoneDefaultPriorityIsTested(): void
    {
        $knownIssues = [ // should only shrink
            'final_class' => true,
            'no_trailing_comma_in_singleline_function_call' => true, // had prio case but no longer, left prio the same for BC reasons, rule has been deprecated
            'psr_autoloading' => true,
            'simple_to_complex_string_variable' => true, // had prio case but no longer, left prio the same for BC reasons
            'single_blank_line_before_namespace' => true,
        ];

        $factory = new FixerFactory();
        $factory->registerBuiltInFixers();
        $fixers = $factory->getFixers();

        $fixerNamesWithTests = [];

        foreach (self::getFixerWithFixedPosition() as $fixerName => $priority) {
            $fixerNamesWithTests[$fixerName] = true;
        }

        foreach ([
            self::getFixersPriorityGraph(),
            self::getPhpDocFixersPriorityGraph(),
        ] as $set) {
            foreach ($set as $fixerName => $edges) {
                $fixerNamesWithTests[$fixerName] = true;

                foreach ($edges as $edge) {
                    $fixerNamesWithTests[$edge] = true;
                }
            }
        }

        $missing = [];

        foreach ($fixers as $fixer) {
            $fixerName = $fixer->getName();

            if (0 !== $fixer->getPriority() && !isset($fixerNamesWithTests[$fixerName])) {
                $missing[$fixerName] = true;
            }
        }

        foreach ($knownIssues as $knownIssue => $true) {
            if (isset($missing[$knownIssue])) {
                unset($missing[$knownIssue]);
            } else {
                static::fail(sprintf('No longer found known issue "%s", please update the set.', $knownIssue));
            }
        }

        static::assertEmpty($missing, 'Fixers without default priority and without priority tests: "'.implode('", "', array_keys($missing)).'."');
    }

    /**
     * @return array<string, string[]>
     */
    private static function getFixersPriorityGraph(): array
    {
        return [
            'align_multiline_comment' => [
                'phpdoc_trim_consecutive_blank_line_separation',
            ],
            'array_indentation' => [
                'align_multiline_comment',
                'binary_operator_spaces',
            ],
            'array_syntax' => [
                'binary_operator_spaces',
                'ternary_operator_spaces',
            ],
            'assign_null_coalescing_to_coalesce_equal' => [
                'binary_operator_spaces',
                'no_whitespace_in_blank_line',
            ],
            'backtick_to_shell_exec' => [
                'escape_implicit_backslashes',
                'explicit_string_variable',
                'native_function_invocation',
                'single_quote',
            ],
            'blank_line_after_opening_tag' => [
                'no_blank_lines_before_namespace',
            ],
            'braces' => [
                'heredoc_indentation',
            ],
            'class_attributes_separation' => [
                'braces',
                'indentation_type',
                'no_extra_blank_lines',
                'statement_indentation',
            ],
            'class_definition' => [
                'braces',
            ],
            'class_keyword_remove' => [
                'no_unused_imports',
            ],
            'combine_consecutive_issets' => [
                'multiline_whitespace_before_semicolons',
                'no_singleline_whitespace_before_semicolons',
                'no_spaces_inside_parenthesis',
                'no_trailing_whitespace',
                'no_whitespace_in_blank_line',
            ],
            'combine_consecutive_unsets' => [
                'no_extra_blank_lines',
                'no_trailing_whitespace',
                'no_whitespace_in_blank_line',
                'space_after_semicolon',
            ],
            'combine_nested_dirname' => [
                'method_argument_space',
                'no_spaces_inside_parenthesis',
            ],
            'control_structure_braces' => [
                'control_structure_continuation_position',
                'curly_braces_position',
                'no_multiple_statements_per_line',
            ],
            'declare_strict_types' => [
                'blank_line_after_opening_tag',
                'declare_equal_normalize',
                'header_comment',
            ],
            'dir_constant' => [
                'combine_nested_dirname',
            ],
            'doctrine_annotation_array_assignment' => [
                'doctrine_annotation_spaces',
            ],
            'echo_tag_syntax' => [
                'no_mixed_echo_print',
            ],
            'empty_loop_body' => [
                'braces',
                'no_extra_blank_lines',
                'no_trailing_whitespace',
            ],
            'empty_loop_condition' => [
                'no_extra_blank_lines',
                'no_trailing_whitespace',
            ],
            'escape_implicit_backslashes' => [
                'heredoc_to_nowdoc',
                'single_quote',
            ],
            'final_internal_class' => [
                'protected_to_private',
                'self_static_accessor',
            ],
            'fully_qualified_strict_types' => [
                'no_superfluous_phpdoc_tags',
            ],
            'function_declaration' => [
                'method_argument_space',
            ],
            'function_to_constant' => [
                'native_function_casing',
                'no_extra_blank_lines',
                'no_singleline_whitespace_before_semicolons',
                'no_trailing_whitespace',
                'no_whitespace_in_blank_line',
                'self_static_accessor',
            ],
            'general_phpdoc_annotation_remove' => [
                'no_empty_phpdoc',
                'phpdoc_line_span',
                'phpdoc_separation',
                'phpdoc_trim',
            ],
            'general_phpdoc_tag_rename' => [
                'phpdoc_add_missing_param_annotation',
            ],
            'get_class_to_class_keyword' => [
                'multiline_whitespace_before_semicolons',
            ],
            'global_namespace_import' => [
                'no_unused_imports',
                'ordered_imports',
            ],
            'header_comment' => [
                'single_line_comment_style',
            ],
            'implode_call' => [
                'method_argument_space',
            ],
            'increment_style' => [
                'no_spaces_inside_parenthesis',
            ],
            'indentation_type' => [
                'phpdoc_indent',
            ],
            'is_null' => [
                'yoda_style',
            ],
            'lambda_not_used_import' => [
                'method_argument_space',
                'no_spaces_inside_parenthesis',
            ],
            'list_syntax' => [
                'binary_operator_spaces',
                'ternary_operator_spaces',
            ],
            'method_argument_space' => [
                'array_indentation',
            ],
            'modernize_strpos' => [
                'binary_operator_spaces',
                'no_extra_blank_lines',
                'no_spaces_inside_parenthesis',
                'no_trailing_whitespace',
                'not_operator_with_space',
                'not_operator_with_successor_space',
                'php_unit_dedicate_assert',
                'single_space_after_construct',
            ],
            'multiline_whitespace_before_semicolons' => [
                'space_after_semicolon',
            ],
            'native_constant_invocation' => [
                'global_namespace_import',
            ],
            'native_function_invocation' => [
                'global_namespace_import',
            ],
            'new_with_braces' => [
                'class_definition',
            ],
            'no_alias_functions' => [
                'implode_call',
                'php_unit_dedicate_assert',
            ],
            'no_alternative_syntax' => [
                'braces',
                'elseif',
                'no_superfluous_elseif',
                'no_unneeded_control_parentheses',
                'no_useless_else',
                'switch_continue_to_break',
            ],
            'no_binary_string' => [
                'no_useless_concat_operator',
                'php_unit_dedicate_assert_internal_type',
                'regular_callable_call',
                'set_type_to_cast',
            ],
            'no_blank_lines_after_phpdoc' => [
                'header_comment',
            ],
            'no_empty_comment' => [
                'no_extra_blank_lines',
                'no_trailing_whitespace',
                'no_whitespace_in_blank_line',
            ],
            'no_empty_phpdoc' => [
                'no_extra_blank_lines',
                'no_trailing_whitespace',
                'no_whitespace_in_blank_line',
            ],
            'no_empty_statement' => [
                'braces',
                'combine_consecutive_unsets',
                'empty_loop_body',
                'multiline_whitespace_before_semicolons',
                'no_extra_blank_lines',
                'no_multiple_statements_per_line',
                'no_singleline_whitespace_before_semicolons',
                'no_trailing_whitespace',
                'no_useless_else',
                'no_useless_return',
                'no_whitespace_in_blank_line',
                'return_assignment',
                'space_after_semicolon',
                'switch_case_semicolon_to_colon',
            ],
            'no_extra_blank_lines' => [
                'blank_line_before_statement',
            ],
            'no_leading_import_slash' => [
                'ordered_imports',
            ],
            'no_multiline_whitespace_around_double_arrow' => [
                'binary_operator_spaces',
                'method_argument_space',
                'trailing_comma_in_multiline',
            ],
            'no_php4_constructor' => [
                'ordered_class_elements',
            ],
            'no_short_bool_cast' => [
                'cast_spaces',
            ],
            'no_spaces_after_function_name' => [
                'function_to_constant',
                'get_class_to_class_keyword',
            ],
            'no_spaces_inside_parenthesis' => [
                'function_to_constant',
                'get_class_to_class_keyword',
                'string_length_to_empty',
            ],
            'no_superfluous_elseif' => [
                'simplified_if_return',
            ],
            'no_superfluous_phpdoc_tags' => [
                'no_empty_phpdoc',
                'void_return',
            ],
            'no_unneeded_control_parentheses' => [
                'concat_space',
                'no_trailing_whitespace',
            ],
            'no_unneeded_curly_braces' => [
                'no_useless_else',
                'no_useless_return',
                'return_assignment',
                'simplified_if_return',
            ],
            'no_unneeded_import_alias' => [
                'no_singleline_whitespace_before_semicolons',
            ],
            'no_unset_cast' => [
                'binary_operator_spaces',
            ],
            'no_unset_on_property' => [
                'combine_consecutive_unsets',
            ],
            'no_unused_imports' => [
                'blank_line_after_namespace',
                'no_extra_blank_lines',
                'no_leading_import_slash',
                'single_line_after_imports',
            ],
            'no_useless_concat_operator' => [
                'date_time_create_from_format_call',
                'ereg_to_preg',
                'php_unit_dedicate_assert_internal_type',
                'regular_callable_call',
                'set_type_to_cast',
            ],
            'no_useless_else' => [
                'braces',
                'combine_consecutive_unsets',
                'no_break_comment',
                'no_extra_blank_lines',
                'no_trailing_whitespace',
                'no_useless_return',
                'no_whitespace_in_blank_line',
                'simplified_if_return',
            ],
            'no_useless_return' => [
                'blank_line_before_statement',
                'no_extra_blank_lines',
                'no_whitespace_in_blank_line',
                'single_line_comment_style',
            ],
            'no_useless_sprintf' => [
                'method_argument_space',
                'native_function_casing',
                'no_empty_statement',
                'no_extra_blank_lines',
                'no_spaces_inside_parenthesis',
            ],
            'nullable_type_declaration_for_default_null_value' => [
                'no_unreachable_default_argument_value',
            ],
            'ordered_class_elements' => [
                'class_attributes_separation',
                'no_blank_lines_after_class_opening',
                'space_after_semicolon',
            ],
            'ordered_imports' => [
                'blank_line_between_import_groups',
            ],
            'php_unit_construct' => [
                'php_unit_dedicate_assert',
            ],
            'php_unit_dedicate_assert' => [
                'no_unused_imports',
                'php_unit_dedicate_assert_internal_type',
            ],
            'php_unit_fqcn_annotation' => [
                'no_unused_imports',
                'phpdoc_order_by_value',
            ],
            'php_unit_internal_class' => [
                'final_internal_class',
            ],
            'php_unit_no_expectation_annotation' => [
                'no_empty_phpdoc',
                'php_unit_expectation',
            ],
            'php_unit_test_annotation' => [
                'no_empty_phpdoc',
                'php_unit_method_casing',
                'phpdoc_trim',
            ],
            'php_unit_test_case_static_method_calls' => [
                'self_static_accessor',
            ],
            'phpdoc_add_missing_param_annotation' => [
                'no_empty_phpdoc',
                'no_superfluous_phpdoc_tags',
                'phpdoc_align',
                'phpdoc_order',
            ],
            'phpdoc_annotation_without_dot' => [
                'phpdoc_types',
                'phpdoc_types_order',
            ],
            'phpdoc_line_span' => [
                'no_superfluous_phpdoc_tags',
            ],
            'phpdoc_no_access' => [
                'no_empty_phpdoc',
                'phpdoc_separation',
                'phpdoc_trim',
            ],
            'phpdoc_no_alias_tag' => [
                'phpdoc_add_missing_param_annotation',
                'phpdoc_single_line_var_spacing',
            ],
            'phpdoc_no_empty_return' => [
                'no_empty_phpdoc',
                'phpdoc_order',
                'phpdoc_separation',
                'phpdoc_trim',
            ],
            'phpdoc_no_package' => [
                'no_empty_phpdoc',
                'phpdoc_separation',
                'phpdoc_trim',
            ],
            'phpdoc_no_useless_inheritdoc' => [
                'no_empty_phpdoc',
                'no_trailing_whitespace_in_comment',
            ],
            'phpdoc_order' => [
                'phpdoc_separation',
                'phpdoc_trim',
            ],
            'phpdoc_return_self_reference' => [
                'no_superfluous_phpdoc_tags',
            ],
            'phpdoc_scalar' => [
                'phpdoc_to_return_type',
            ],
            'phpdoc_to_comment' => [
                'no_empty_comment',
                'phpdoc_no_useless_inheritdoc',
                'single_line_comment_spacing',
                'single_line_comment_style',
            ],
            'phpdoc_to_param_type' => [
                'no_superfluous_phpdoc_tags',
            ],
            'phpdoc_to_property_type' => [
                'no_superfluous_phpdoc_tags',
            ],
            'phpdoc_to_return_type' => [
                'fully_qualified_strict_types',
                'no_superfluous_phpdoc_tags',
                'return_type_declaration',
            ],
            'phpdoc_types' => [
                'phpdoc_to_return_type',
            ],
            'pow_to_exponentiation' => [
                'binary_operator_spaces',
                'method_argument_space',
                'native_function_casing',
                'no_spaces_after_function_name',
                'no_spaces_inside_parenthesis',
            ],
            'protected_to_private' => [
                'ordered_class_elements',
            ],
            'regular_callable_call' => [
                'native_function_invocation',
            ],
            'return_assignment' => [
                'blank_line_before_statement',
            ],
            'semicolon_after_instruction' => [
                'simplified_if_return',
            ],
            'simplified_if_return' => [
                'multiline_whitespace_before_semicolons',
                'no_singleline_whitespace_before_semicolons',
            ],
            'simplified_null_return' => [
                'no_useless_return',
                'void_return',
            ],
            'single_class_element_per_statement' => [
                'class_attributes_separation',
            ],
            'single_import_per_statement' => [
                'multiline_whitespace_before_semicolons',
                'no_leading_import_slash',
                'no_singleline_whitespace_before_semicolons',
                'no_unused_imports',
                'space_after_semicolon',
            ],
            'single_line_throw' => [
                'braces',
                'concat_space',
            ],
            'single_quote' => [
                'no_useless_concat_operator',
            ],
            'single_space_after_construct' => [
                'braces',
                'function_declaration',
            ],
            'single_trait_insert_per_statement' => [
                'braces',
                'space_after_semicolon',
            ],
            'standardize_increment' => [
                'increment_style',
            ],
            'standardize_not_equals' => [
                'binary_operator_spaces',
            ],
            'statement_indentation' => [
                'heredoc_indentation',
            ],
            'strict_comparison' => [
                'binary_operator_spaces',
                'modernize_strpos',
            ],
            'strict_param' => [
                'method_argument_space',
                'native_function_invocation',
            ],
            'string_length_to_empty' => [
                'no_extra_blank_lines',
                'no_trailing_whitespace',
            ],
            'ternary_to_elvis_operator' => [
                'no_trailing_whitespace',
                'ternary_operator_spaces',
            ],
            'ternary_to_null_coalescing' => [
                'assign_null_coalescing_to_coalesce_equal',
            ],
            'unary_operator_spaces' => [
                'not_operator_with_space',
                'not_operator_with_successor_space',
            ],
            'visibility_required' => [
                'class_attributes_separation',
            ],
            'void_return' => [
                'phpdoc_no_empty_return',
                'return_type_declaration',
            ],
        ];
    }

    /**
     * @return array<string, list<string>>
     */
    private static function getPhpDocFixersPriorityGraph(): array
    {
        // Prepare bulk tests for phpdoc fixers to test that:
        // * `align_multiline_comment` is first
        // * `comment_to_phpdoc` is second
        // * `phpdoc_to_comment` is third
        // * `phpdoc_indent` is fourth
        // * `phpdoc_types` is fifth
        // * `phpdoc_scalar` is sixth
        // * `phpdoc_align` is last

        $cases = [
            'align_multiline_comment' => ['comment_to_phpdoc'],
            'comment_to_phpdoc' => ['phpdoc_to_comment'],
            'phpdoc_to_comment' => ['phpdoc_indent'],
            'phpdoc_indent' => ['phpdoc_types'],
            'phpdoc_types' => ['phpdoc_scalar'],
            'phpdoc_scalar' => [],
        ];

        $docFixerNames = array_filter(
            array_keys(self::getAllFixers()),
            static function (string $name): bool {
                return str_contains($name, 'phpdoc');
            }
        );

        foreach ($docFixerNames as $docFixerName) {
            if (!\in_array($docFixerName, ['comment_to_phpdoc', 'phpdoc_to_comment', 'phpdoc_indent', 'phpdoc_types', 'phpdoc_scalar'], true)) {
                $cases['align_multiline_comment'][] = $docFixerName;
                $cases['comment_to_phpdoc'][] = $docFixerName;
                $cases['phpdoc_indent'][] = $docFixerName;
                $cases['phpdoc_to_comment'][] = $docFixerName;

                if ('phpdoc_annotation_without_dot' !== $docFixerName) {
                    $cases['phpdoc_scalar'][] = $docFixerName;
                    $cases['phpdoc_types'][] = $docFixerName;
                }
            }

            if ('phpdoc_align' !== $docFixerName) {
                $cases[$docFixerName][] = 'phpdoc_align';
            }
        }

        return $cases;
    }

    /**
     * @return array<string, int>
     */
    private static function getFixerWithFixedPosition(): array
    {
        return [
            'encoding' => 0, // Expected "encoding" fixer to have the highest priority.
            'full_opening_tag' => 1, // Expected "full_opening_tag" fixer has second-highest priority.
            'single_blank_line_at_eof' => -1, // Expected "single_blank_line_at_eof" to have the lowest priority.
        ];
    }

    /**
     * @return array<string, FixerInterface>
     */
    private static function getAllFixers(): array
    {
        $factory = new FixerFactory();
        $factory->registerBuiltInFixers();
        $fixers = [];

        foreach ($factory->getFixers() as $fixer) {
            $fixers[$fixer->getName()] = $fixer;
        }

        return $fixers;
    }

    private static function getIntegrationPriorityDirectory(): string
    {
        return __DIR__.'/../Fixtures/Integration/priority/';
    }
}
