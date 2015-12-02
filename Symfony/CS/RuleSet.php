<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS;

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
            'short_tag' => true,
        ),
        '@PSR2' => array(
            '@PSR1' => true,
            'braces' => true,
            'elseif' => true,
            'eof_ending' => true,
            'function_call_space' => true,
            'function_declaration' => true,
            'indentation' => true,
            'line_after_namespace' => true,
            'linefeed' => true,
            'lowercase_constants' => true,
            'lowercase_keywords' => true,
            'method_argument_space' => true,
            'multiple_use' => true,
            'parenthesis' => true,
            'php_closing_tag' => true,
            'single_line_after_imports' => true,
            'trailing_spaces' => true,
            'visibility' => true,
        ),
        '@Symfony' => array(
            '@PSR2' => true,
            'alias_functions' => true,
            'array_element_no_space_before_comma' => true,
            'array_element_white_space_after_comma' => true,
            'blankline_after_open_tag' => true,
            'concat_without_spaces' => true,
            'double_arrow_multiline_whitespaces' => true,
            'duplicate_semicolon' => true,
            'empty_return' => true,
            'extra_empty_lines' => true,
            'function_typehint_space' => true,
            'include' => true,
            'list_commas' => true,
            'method_separation' => true,
            'multiline_array_trailing_comma' => true,
            'namespace_no_leading_whitespace' => true,
            'new_with_braces' => true,
            'no_blank_lines_after_class_opening' => true,
            'no_empty_lines_after_phpdocs' => true,
            'object_operator' => true,
            'operators_spaces' => true,
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
            'remove_leading_slash_use' => true,
            'remove_lines_between_uses' => true,
            'return' => true,
            'self_accessor' => true,
            'single_array_no_trailing_comma' => true,
            'single_blank_line_before_namespace' => true,
            'single_quote' => true,
            'short_bool_cast' => true,
            'spaces_before_semicolon' => true,
            'spaces_cast' => true,
            'standardize_not_equal' => true,
            'ternary_spaces' => true,
            'trim_array_spaces' => true,
            'unalign_double_arrow' => true,
            'unalign_equals' => true,
            'unary_operators_spaces' => true,
            'unneeded_control_parentheses' => true,
            'unused_use' => true,
            'whitespacy_lines' => true,
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
