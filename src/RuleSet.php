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

namespace PhpCsFixer;

/**
 * Set of rules to be used by fixer.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author SpacePossum
 *
 * @internal
 */
final class RuleSet implements RuleSetInterface
{
    private $setDefinitions = [
        '@PSR1' => [
            'encoding' => true,
            'full_opening_tag' => true,
        ],
        '@PSR2' => [
            '@PSR1' => true,
            'blank_line_after_namespace' => true,
            'braces' => true,
            'class_definition' => true,
            'elseif' => true,
            'function_declaration' => true,
            'indentation_type' => true,
            'line_ending' => true,
            'lowercase_constants' => true,
            'lowercase_keywords' => true,
            'method_argument_space' => ['ensure_fully_multiline' => true],
            'no_break_comment' => true,
            'no_closing_tag' => true,
            'no_spaces_after_function_name' => true,
            'no_spaces_inside_parenthesis' => true,
            'no_trailing_whitespace' => true,
            'no_trailing_whitespace_in_comment' => true,
            'single_blank_line_at_eof' => true,
            'single_class_element_per_statement' => ['elements' => ['property']],
            'single_import_per_statement' => true,
            'single_line_after_imports' => true,
            'switch_case_semicolon_to_colon' => true,
            'switch_case_space' => true,
            'visibility_required' => true,
        ],
        '@Symfony' => [
            '@PSR2' => true,
            'binary_operator_spaces' => [
                'align_double_arrow' => false,
                'align_equals' => false,
            ],
            'blank_line_after_opening_tag' => true,
            'blank_line_before_return' => true,
            'braces' => [
                'allow_single_line_closure' => true,
            ],
            'cast_spaces' => true,
            'class_definition' => ['singleLine' => true],
            'concat_space' => ['spacing' => 'none'],
            'declare_equal_normalize' => true,
            'function_typehint_space' => true,
            'hash_to_slash_comment' => true,
            'include' => true,
            'lowercase_cast' => true,
            'magic_constant_casing' => true,
            'method_argument_space' => true,
            'method_separation' => true,
            'native_function_casing' => true,
            'new_with_braces' => true,
            'no_blank_lines_after_class_opening' => true,
            'no_blank_lines_after_phpdoc' => true,
            'no_empty_comment' => true,
            'no_empty_phpdoc' => true,
            'no_empty_statement' => true,
            'no_extra_consecutive_blank_lines' => ['tokens' => [
                'curly_brace_block',
                'extra',
                'parenthesis_brace_block',
                'square_brace_block',
                'throw',
                'use',
            ]],
            'no_leading_import_slash' => true,
            'no_leading_namespace_whitespace' => true,
            'no_mixed_echo_print' => ['use' => 'echo'],
            'no_multiline_whitespace_around_double_arrow' => true,
            'no_short_bool_cast' => true,
            'no_singleline_whitespace_before_semicolons' => true,
            'no_spaces_around_offset' => true,
            'no_trailing_comma_in_list_call' => true,
            'no_trailing_comma_in_singleline_array' => true,
            'no_unneeded_control_parentheses' => true,
            'no_unused_imports' => true,
            'no_whitespace_before_comma_in_array' => true,
            'no_whitespace_in_blank_line' => true,
            'normalize_index_brace' => true,
            'object_operator_without_whitespace' => true,
            'php_unit_fqcn_annotation' => true,
            'phpdoc_align' => true,
            'phpdoc_annotation_without_dot' => true,
            'phpdoc_indent' => true,
            'phpdoc_inline_tag' => true,
            'phpdoc_no_access' => true,
            'phpdoc_no_alias_tag' => true,
            'phpdoc_no_empty_return' => true,
            'phpdoc_no_package' => true,
            'phpdoc_no_useless_inheritdoc' => true,
            'phpdoc_return_self_reference' => true,
            'phpdoc_scalar' => true,
            'phpdoc_separation' => true,
            'phpdoc_single_line_var_spacing' => true,
            'phpdoc_summary' => true,
            'phpdoc_to_comment' => true,
            'phpdoc_trim' => true,
            'phpdoc_types' => true,
            'phpdoc_var_without_name' => true,
            'pre_increment' => true,
            'protected_to_private' => true,
            'return_type_declaration' => true,
            'self_accessor' => true,
            'short_scalar_cast' => true,
            'single_blank_line_before_namespace' => true,
            'single_class_element_per_statement' => true,
            'single_quote' => true,
            'space_after_semicolon' => true,
            'standardize_not_equals' => true,
            'ternary_operator_spaces' => true,
            'trailing_comma_in_multiline_array' => true,
            'trim_array_spaces' => true,
            'unary_operator_spaces' => true,
            'whitespace_after_comma_in_array' => true,
        ],
        '@Symfony:risky' => [
            'dir_constant' => true,
            'ereg_to_preg' => true,
            'function_to_constant' => true,
            'is_null' => true,
            'modernize_types_casting' => true,
            'no_alias_functions' => true,
            'non_printable_character' => true,
            'php_unit_construct' => true,
            'php_unit_dedicate_assert' => true,
            'psr4' => true,
            'silenced_deprecation_error' => true,
        ],
        '@PHP56Migration' => [
            'pow_to_exponentiation' => true,
        ],
        '@PHP70Migration' => [
            '@PHP56Migration' => true,
            'random_api_migration' => [
                'mt_rand' => 'random_int',
                'rand' => 'random_int',
            ],
            'ternary_to_null_coalescing' => true,
        ],
        '@PHP70Migration:risky' => [
            'declare_strict_types' => true,
        ],
        '@PHP71Migration' => [
            '@PHP70Migration' => true,
            'visibility_required' => [
                'const',
                'method',
                'property',
            ],
        ],
        '@PHP71Migration:risky' => [
            '@PHP70Migration:risky' => true,
            'void_return' => true,
        ],
    ];

    /**
     * Set that was used to generate group of rules.
     *
     * The key is name of rule or set, value is bool if the rule/set should be used.
     *
     * @var array
     */
    private $set;

    /**
     * Group of rules generated from input set.
     *
     * The key is name of rule, value is bool if the rule/set should be used.
     * The key must not point to any set.
     *
     * @var array
     */
    private $rules;

    public function __construct(array $set = [])
    {
        foreach ($set as $key => $value) {
            if (is_int($key)) {
                throw new \InvalidArgumentException(sprintf('Missing value for "%s" rule/set.', $value));
            }
        }

        $this->set = $set;
        $this->resolveSet();
    }

    public static function create(array $set = [])
    {
        return new self($set);
    }

    /**
     * {@inheritdoc}
     */
    public function hasRule($rule)
    {
        return array_key_exists($rule, $this->rules);
    }

    /**
     * {@inheritdoc}
     */
    public function getRuleConfiguration($rule)
    {
        if (!$this->hasRule($rule)) {
            throw new \InvalidArgumentException(sprintf('Rule "%s" is not in the set.', $rule));
        }

        if ($this->rules[$rule] === true) {
            return null;
        }

        return $this->rules[$rule];
    }

    /**
     * {@inheritdoc}
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * {@inheritdoc}
     */
    public function getSetDefinitionNames()
    {
        return array_keys($this->setDefinitions);
    }

    /**
     * @param string $name name of set
     *
     * @return array
     */
    private function getSetDefinition($name)
    {
        if (!isset($this->setDefinitions[$name])) {
            throw new \InvalidArgumentException(sprintf('Set "%s" does not exist.', $name));
        }

        return $this->setDefinitions[$name];
    }

    /**
     * Resolve input set into group of rules.
     *
     * @return $this
     */
    private function resolveSet()
    {
        $rules = $this->set;
        $resolvedRules = [];

        // expand sets
        foreach ($rules as $name => $value) {
            if ('@' === $name[0]) {
                if (!is_bool($value)) {
                    throw new \UnexpectedValueException(sprintf('Nested rule set "%s" configuration must be a boolean.', $name));
                }

                $set = $this->resolveSubset($name, $value);
                $resolvedRules = array_merge($resolvedRules, $set);
            } else {
                $resolvedRules[$name] = $value;
            }
        }

        // filter out all resolvedRules that are off
        $resolvedRules = array_filter($resolvedRules);

        $this->rules = $resolvedRules;

        return $this;
    }

    /**
     * Resolve set rules as part of another set.
     *
     * If set value is false then disable all fixers in set,
     * if not then get value from set item.
     *
     * @param string $setName
     * @param bool   $setValue
     *
     * @return array
     */
    private function resolveSubset($setName, $setValue)
    {
        $rules = $this->getSetDefinition($setName);
        foreach ($rules as $name => $value) {
            if ('@' === $name[0]) {
                $set = $this->resolveSubset($name, $setValue);
                unset($rules[$name]);
                $rules = array_merge($rules, $set);
            } elseif (!$setValue) {
                $rules[$name] = false;
            } else {
                $rules[$name] = $value;
            }
        }

        return $rules;
    }
}
