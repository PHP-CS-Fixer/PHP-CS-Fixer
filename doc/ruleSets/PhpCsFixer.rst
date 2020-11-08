========================
Rule set ``@PhpCsFixer``
========================

Rule set as used by the PHP-CS-Fixer development team, highly opinionated.

Rules
-----

- `@Symfony <./Symfony.rst>`_
- `align_multiline_comment <./../rules/phpdoc/align_multiline_comment.rst>`_
- `array_indentation <./../rules/whitespace/array_indentation.rst>`_
- `blank_line_before_statement <./../rules/whitespace/blank_line_before_statement.rst>`_
- `combine_consecutive_issets <./../rules/language_construct/combine_consecutive_issets.rst>`_
- `combine_consecutive_unsets <./../rules/language_construct/combine_consecutive_unsets.rst>`_
- `compact_nullable_typehint <./../rules/whitespace/compact_nullable_typehint.rst>`_
- `escape_implicit_backslashes <./../rules/string_notation/escape_implicit_backslashes.rst>`_
- `explicit_indirect_variable <./../rules/language_construct/explicit_indirect_variable.rst>`_
- `explicit_string_variable <./../rules/string_notation/explicit_string_variable.rst>`_
- `fully_qualified_strict_types <./../rules/import/fully_qualified_strict_types.rst>`_
- `heredoc_to_nowdoc <./../rules/string_notation/heredoc_to_nowdoc.rst>`_
- `method_argument_space <./../rules/function_notation/method_argument_space.rst>`_
  config:
  ``['on_multiline' => 'ensure_fully_multiline']``
- `method_chaining_indentation <./../rules/whitespace/method_chaining_indentation.rst>`_
- `multiline_comment_opening_closing <./../rules/comment/multiline_comment_opening_closing.rst>`_
- `multiline_whitespace_before_semicolons <./../rules/semicolon/multiline_whitespace_before_semicolons.rst>`_
  config:
  ``['strategy' => 'new_line_for_chained_calls']``
- `no_alternative_syntax <./../rules/control_structure/no_alternative_syntax.rst>`_
- `no_binary_string <./../rules/string_notation/no_binary_string.rst>`_
- `no_extra_blank_lines <./../rules/whitespace/no_extra_blank_lines.rst>`_
  config:
  ``['tokens' => ['break', 'continue', 'curly_brace_block', 'extra', 'parenthesis_brace_block', 'return', 'square_brace_block', 'throw', 'use']]``
- `no_null_property_initialization <./../rules/class_notation/no_null_property_initialization.rst>`_
- `no_short_echo_tag <./../rules/php_tag/no_short_echo_tag.rst>`_
- `no_superfluous_elseif <./../rules/control_structure/no_superfluous_elseif.rst>`_
- `no_unset_cast <./../rules/cast_notation/no_unset_cast.rst>`_
- `no_useless_else <./../rules/control_structure/no_useless_else.rst>`_
- `no_useless_return <./../rules/return_notation/no_useless_return.rst>`_
- `ordered_class_elements <./../rules/class_notation/ordered_class_elements.rst>`_
- `php_unit_internal_class <./../rules/php_unit/php_unit_internal_class.rst>`_
- `php_unit_method_casing <./../rules/php_unit/php_unit_method_casing.rst>`_
- `php_unit_ordered_covers <./../rules/php_unit/php_unit_ordered_covers.rst>`_
- `php_unit_test_class_requires_covers <./../rules/php_unit/php_unit_test_class_requires_covers.rst>`_
- `phpdoc_add_missing_param_annotation <./../rules/phpdoc/phpdoc_add_missing_param_annotation.rst>`_
- `phpdoc_no_empty_return <./../rules/phpdoc/phpdoc_no_empty_return.rst>`_
- `phpdoc_order <./../rules/phpdoc/phpdoc_order.rst>`_
- `phpdoc_types_order <./../rules/phpdoc/phpdoc_types_order.rst>`_
- `phpdoc_var_annotation_correct_order <./../rules/phpdoc/phpdoc_var_annotation_correct_order.rst>`_
- `protected_to_private <./../rules/class_notation/protected_to_private.rst>`_
- `return_assignment <./../rules/return_notation/return_assignment.rst>`_
- `simple_to_complex_string_variable <./../rules/string_notation/simple_to_complex_string_variable.rst>`_
- `single_line_comment_style <./../rules/comment/single_line_comment_style.rst>`_
- `single_line_throw <./../rules/function_notation/single_line_throw.rst>`_
