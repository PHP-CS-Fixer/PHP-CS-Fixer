=====================
Rule set ``@Symfony``
=====================

Rules that follow the official `Symfony Coding Standards <https://symfony.com/doc/current/contributing/code/standards.html>`_.

Rules
-----

- `@PSR2 <./PSR2.rst>`_
- `array_syntax <./../rules/array_notation/array_syntax.rst>`_
  config:
  ``['syntax' => 'short']``
- `backtick_to_shell_exec <./../rules/alias/backtick_to_shell_exec.rst>`_
- `binary_operator_spaces <./../rules/operator/binary_operator_spaces.rst>`_
- `blank_line_after_opening_tag <./../rules/php_tag/blank_line_after_opening_tag.rst>`_
- `blank_line_before_statement <./../rules/whitespace/blank_line_before_statement.rst>`_
  config:
  ``['statements' => ['return']]``
- `braces <./../rules/basic/braces.rst>`_
  config:
  ``['allow_single_line_anonymous_class_with_empty_body' => true, 'allow_single_line_closure' => true]``
- `cast_spaces <./../rules/cast_notation/cast_spaces.rst>`_
- `class_attributes_separation <./../rules/class_notation/class_attributes_separation.rst>`_
  config:
  ``['elements' => ['method' => 'one']]``
- `class_definition <./../rules/class_notation/class_definition.rst>`_
  config:
  ``['single_line' => true]``
- `clean_namespace <./../rules/namespace_notation/clean_namespace.rst>`_
- `concat_space <./../rules/operator/concat_space.rst>`_
- `declare_equal_normalize <./../rules/language_construct/declare_equal_normalize.rst>`_
- `echo_tag_syntax <./../rules/php_tag/echo_tag_syntax.rst>`_
- `fully_qualified_strict_types <./../rules/import/fully_qualified_strict_types.rst>`_
- `function_typehint_space <./../rules/function_notation/function_typehint_space.rst>`_
- `general_phpdoc_tag_rename <./../rules/phpdoc/general_phpdoc_tag_rename.rst>`_
  config:
  ``['replacements' => ['inheritDocs' => 'inheritDoc']]``
- `include <./../rules/control_structure/include.rst>`_
- `increment_style <./../rules/operator/increment_style.rst>`_
- `lambda_not_used_import <./../rules/function_notation/lambda_not_used_import.rst>`_
- `linebreak_after_opening_tag <./../rules/php_tag/linebreak_after_opening_tag.rst>`_
- `lowercase_cast <./../rules/cast_notation/lowercase_cast.rst>`_
- `lowercase_static_reference <./../rules/casing/lowercase_static_reference.rst>`_
- `magic_constant_casing <./../rules/casing/magic_constant_casing.rst>`_
- `magic_method_casing <./../rules/casing/magic_method_casing.rst>`_
- `method_argument_space <./../rules/function_notation/method_argument_space.rst>`_
- `native_function_casing <./../rules/casing/native_function_casing.rst>`_
- `native_function_type_declaration_casing <./../rules/casing/native_function_type_declaration_casing.rst>`_
- `new_with_braces <./../rules/operator/new_with_braces.rst>`_
- `no_alias_language_construct_call <./../rules/alias/no_alias_language_construct_call.rst>`_
- `no_alternative_syntax <./../rules/control_structure/no_alternative_syntax.rst>`_
- `no_binary_string <./../rules/string_notation/no_binary_string.rst>`_
- `no_blank_lines_after_class_opening <./../rules/class_notation/no_blank_lines_after_class_opening.rst>`_
- `no_blank_lines_after_phpdoc <./../rules/phpdoc/no_blank_lines_after_phpdoc.rst>`_
- `no_empty_comment <./../rules/comment/no_empty_comment.rst>`_
- `no_empty_phpdoc <./../rules/phpdoc/no_empty_phpdoc.rst>`_
- `no_empty_statement <./../rules/semicolon/no_empty_statement.rst>`_
- `no_extra_blank_lines <./../rules/whitespace/no_extra_blank_lines.rst>`_
  config:
  ``['tokens' => ['case', 'continue', 'curly_brace_block', 'default', 'extra', 'parenthesis_brace_block', 'square_brace_block', 'switch', 'throw', 'use', 'use_trait']]``
- `no_leading_import_slash <./../rules/import/no_leading_import_slash.rst>`_
- `no_leading_namespace_whitespace <./../rules/namespace_notation/no_leading_namespace_whitespace.rst>`_
- `no_mixed_echo_print <./../rules/alias/no_mixed_echo_print.rst>`_
- `no_multiline_whitespace_around_double_arrow <./../rules/array_notation/no_multiline_whitespace_around_double_arrow.rst>`_
- `no_short_bool_cast <./../rules/cast_notation/no_short_bool_cast.rst>`_
- `no_singleline_whitespace_before_semicolons <./../rules/semicolon/no_singleline_whitespace_before_semicolons.rst>`_
- `no_spaces_around_offset <./../rules/whitespace/no_spaces_around_offset.rst>`_
- `no_superfluous_phpdoc_tags <./../rules/phpdoc/no_superfluous_phpdoc_tags.rst>`_
  config:
  ``['allow_mixed' => true, 'allow_unused_params' => true]``
- `no_trailing_comma_in_list_call <./../rules/control_structure/no_trailing_comma_in_list_call.rst>`_
- `no_trailing_comma_in_singleline_array <./../rules/array_notation/no_trailing_comma_in_singleline_array.rst>`_
- `no_unneeded_control_parentheses <./../rules/control_structure/no_unneeded_control_parentheses.rst>`_
  config:
  ``['statements' => ['break', 'clone', 'continue', 'echo_print', 'return', 'switch_case', 'yield', 'yield_from']]``
- `no_unneeded_curly_braces <./../rules/control_structure/no_unneeded_curly_braces.rst>`_
  config:
  ``['namespaces' => true]``
- `no_unset_cast <./../rules/cast_notation/no_unset_cast.rst>`_
- `no_unused_imports <./../rules/import/no_unused_imports.rst>`_
- `no_whitespace_before_comma_in_array <./../rules/array_notation/no_whitespace_before_comma_in_array.rst>`_
- `no_whitespace_in_blank_line <./../rules/whitespace/no_whitespace_in_blank_line.rst>`_
- `normalize_index_brace <./../rules/array_notation/normalize_index_brace.rst>`_
- `object_operator_without_whitespace <./../rules/operator/object_operator_without_whitespace.rst>`_
- `ordered_imports <./../rules/import/ordered_imports.rst>`_
- `php_unit_fqcn_annotation <./../rules/php_unit/php_unit_fqcn_annotation.rst>`_
- `php_unit_method_casing <./../rules/php_unit/php_unit_method_casing.rst>`_
- `phpdoc_align <./../rules/phpdoc/phpdoc_align.rst>`_
  config:
  ``['tags' => ['method', 'param', 'property', 'return', 'throws', 'type', 'var']]``
- `phpdoc_annotation_without_dot <./../rules/phpdoc/phpdoc_annotation_without_dot.rst>`_
- `phpdoc_indent <./../rules/phpdoc/phpdoc_indent.rst>`_
- `phpdoc_inline_tag_normalizer <./../rules/phpdoc/phpdoc_inline_tag_normalizer.rst>`_
- `phpdoc_no_access <./../rules/phpdoc/phpdoc_no_access.rst>`_
- `phpdoc_no_alias_tag <./../rules/phpdoc/phpdoc_no_alias_tag.rst>`_
- `phpdoc_no_package <./../rules/phpdoc/phpdoc_no_package.rst>`_
- `phpdoc_no_useless_inheritdoc <./../rules/phpdoc/phpdoc_no_useless_inheritdoc.rst>`_
- `phpdoc_return_self_reference <./../rules/phpdoc/phpdoc_return_self_reference.rst>`_
- `phpdoc_scalar <./../rules/phpdoc/phpdoc_scalar.rst>`_
- `phpdoc_separation <./../rules/phpdoc/phpdoc_separation.rst>`_
- `phpdoc_single_line_var_spacing <./../rules/phpdoc/phpdoc_single_line_var_spacing.rst>`_
- `phpdoc_summary <./../rules/phpdoc/phpdoc_summary.rst>`_
- `phpdoc_tag_type <./../rules/phpdoc/phpdoc_tag_type.rst>`_
  config:
  ``['tags' => ['inheritDoc' => 'inline']]``
- `phpdoc_to_comment <./../rules/phpdoc/phpdoc_to_comment.rst>`_
- `phpdoc_trim <./../rules/phpdoc/phpdoc_trim.rst>`_
- `phpdoc_trim_consecutive_blank_line_separation <./../rules/phpdoc/phpdoc_trim_consecutive_blank_line_separation.rst>`_
- `phpdoc_types <./../rules/phpdoc/phpdoc_types.rst>`_
- `phpdoc_types_order <./../rules/phpdoc/phpdoc_types_order.rst>`_
  config:
  ``['null_adjustment' => 'always_last', 'sort_algorithm' => 'none']``
- `phpdoc_var_without_name <./../rules/phpdoc/phpdoc_var_without_name.rst>`_
- `protected_to_private <./../rules/class_notation/protected_to_private.rst>`_
- `return_type_declaration <./../rules/function_notation/return_type_declaration.rst>`_
- `semicolon_after_instruction <./../rules/semicolon/semicolon_after_instruction.rst>`_
- `short_scalar_cast <./../rules/cast_notation/short_scalar_cast.rst>`_
- `single_blank_line_before_namespace <./../rules/namespace_notation/single_blank_line_before_namespace.rst>`_
- `single_class_element_per_statement <./../rules/class_notation/single_class_element_per_statement.rst>`_
- `single_line_comment_style <./../rules/comment/single_line_comment_style.rst>`_
  config:
  ``['comment_types' => ['hash']]``
- `single_line_throw <./../rules/function_notation/single_line_throw.rst>`_
- `single_quote <./../rules/string_notation/single_quote.rst>`_
- `single_space_after_construct <./../rules/language_construct/single_space_after_construct.rst>`_
- `single_trait_insert_per_statement <./../rules/class_notation/single_trait_insert_per_statement.rst>`_
- `space_after_semicolon <./../rules/semicolon/space_after_semicolon.rst>`_
  config:
  ``['remove_in_empty_for_expressions' => true]``
- `standardize_increment <./../rules/operator/standardize_increment.rst>`_
- `standardize_not_equals <./../rules/operator/standardize_not_equals.rst>`_
- `switch_continue_to_break <./../rules/control_structure/switch_continue_to_break.rst>`_
- `ternary_operator_spaces <./../rules/operator/ternary_operator_spaces.rst>`_
- `trailing_comma_in_multiline_array <./../rules/array_notation/trailing_comma_in_multiline_array.rst>`_
- `trim_array_spaces <./../rules/array_notation/trim_array_spaces.rst>`_
- `unary_operator_spaces <./../rules/operator/unary_operator_spaces.rst>`_
- `whitespace_after_comma_in_array <./../rules/array_notation/whitespace_after_comma_in_array.rst>`_
- `yoda_style <./../rules/control_structure/yoda_style.rst>`_
