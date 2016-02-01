<?php

/*
 * This file is part of the PHP CS Fixer.
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
 *
 * @internal
 */
final class RuleSet implements RuleSetInterface
{
    private $setDefinitions = array(
        '@PSR1' => array(
            'encoding' => true,
            'full_opening_tag' => true,
        ),
        '@PSR2' => array(
            '@PSR1' => true,
            'braces' => true,
            'class_definition' => true,
            'elseif' => true,
            'single_blank_line_at_eof' => true,
            'no_spaces_after_function_name' => true,
            'function_declaration' => true,
            'no_tab_indentation' => true,
            'blank_line_after_namespace' => true,
            'unix_line_endings' => true,
            'lowercase_constants' => true,
            'lowercase_keywords' => true,
            'method_argument_space' => true,
            'single_import_per_statement' => true,
            'no_spaces_inside_parenthesis' => true,
            'no_closing_tag' => true,
            'single_line_after_imports' => true,
            'switch_case_semicolon_to_colon' => true,
            'switch_case_space' => true,
            'no_trailing_whitespace' => true,
            'visibility_required' => true,
        ),
        '@Symfony' => array(
            '@PSR2' => true,
            'no_alias_functions' => true,
            'no_whitespace_before_comma_in_array' => true,
            'whitespace_after_comma_in_array' => true,
            'blank_line_after_opening_tag' => true,
            'concat_without_spaces' => true,
            'double_arrow_no_multiline_whitespace' => true,
            'no_duplicate_semicolons' => true,
            'simplified_null_return' => true,
            'no_extra_consecutive_blank_lines' => true,
            'function_typehint_space' => true,
            'hash_to_slash_comment' => true,
            'heredoc_to_nowdoc' => true,
            'include' => true,
            'no_trailing_comma_in_list_call' => true,
            'no_trailing_whitespace_in_comment' => true,
            'lowercase_cast' => true,
            'no_unreachable_default_argument_value' => true,
            'method_separation' => true,
            'trailing_comma_in_multiline_array' => true,
            'no_leading_namespace_whitespace' => true,
            'native_function_casing' => true,
            'new_with_braces' => true,
            'no_blank_lines_after_class_opening' => true,
            'no_blank_lines_after_phpdoc' => true,
            'object_operator_without_whitespace' => true,
            'binary_operator_spaces' => true,
            'phpdoc_align' => true,
            'phpdoc_indent' => true,
            'phpdoc_inline_tag' => true,
            'phpdoc_no_access' => true,
            'phpdoc_no_empty_return' => true,
            'phpdoc_no_package' => true,
            'phpdoc_scalar' => true,
            'phpdoc_separation' => true,
            'phpdoc_summary' => true,
            'phpdoc_to_comment' => true,
            'phpdoc_trim' => true,
            'phpdoc_types' => true,
            'phpdoc_type_to_var' => true,
            'phpdoc_var_without_name' => true,
            'pre_increment' => true,
            'print_to_echo' => true,
            'no_leading_import_slash' => true,
            'no_blank_lines_between_uses' => true,
            'blank_line_before_return' => true,
            'self_accessor' => true,
            'no_trailing_comma_in_singleline_array' => true,
            'single_blank_line_before_namespace' => true,
            'single_quote' => true,
            'no_short_bool_cast' => true,
            'short_scalar_cast' => true,
            'space_after_semicolon' => true,
            'no_singleline_whitespace_before_semicolons' => true,
            'spaces_cast' => true,
            'standardize_not_equals' => true,
            'ternary_operator_spaces' => true,
            'trim_array_spaces' => true,
            'unalign_double_arrow' => true,
            'unalign_equals' => true,
            'unary_operator_spaces' => true,
            'no_unneeded_control_parentheses' => true,
            'no_unused_imports' => true,
            'no_whitespace_in_blank_lines' => true,
        ),
    );

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

    public function __construct(array $set = array())
    {
        $this->set = $set;
        $this->resolveSet();
    }

    public static function create(array $set = array())
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
            throw new \UnexpectedValueException(sprintf('Rule "%s" is not in the set.', $rule));
        }

        if ($this->rules[$rule] === true) {
            return;
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
     * Get definition of set.
     *
     * @param string $name name of set
     *
     * @return array
     */
    private function getSetDefinition($name)
    {
        if (!isset($this->setDefinitions[$name])) {
            throw new \UnexpectedValueException(sprintf('Set "%s" does not exist.', $name));
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
        $hasSet = null;

        // expand sets
        do {
            $hasSet = false;

            $tmpRules = $rules;
            $rules = array();

            foreach ($tmpRules as $name => $value) {
                if (!$hasSet && '@' === $name[0]) {
                    $hasSet = true;
                    $set = $this->getSetDefinition($name);

                    foreach ($set as $nestedName => $nestedValue) {
                        // if set value is falsy then disable all fixers in set, if not then get value from set item
                        $rules[$nestedName] = $value ? $nestedValue : false;
                    }

                    continue;
                }

                $rules[$name] = $value;
            }
        } while ($hasSet);

        // filter out all rules that are off
        $rules = array_filter($rules);

        $this->rules = $rules;

        return $this;
    }
}
