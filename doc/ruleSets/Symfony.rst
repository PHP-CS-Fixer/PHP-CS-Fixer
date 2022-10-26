=====================
Rule set ``@Symfony``
=====================

Rules that follow the official `Symfony Coding Standards <https://symfony.com/doc/current/contributing/code/standards.html>`_.

Rules
-----

- `@PSR12 <./PSR12.rst>`_
- `array_syntax <./../rules/array_notation/array_syntax.rst>`_
- `backtick_to_shell_exec <./../rules/alias/backtick_to_shell_exec.rst>`_
- `binary_operator_spaces <./../rules/operator/binary_operator_spaces.rst>`_
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
- `class_reference_name_casing <./../rules/casing/class_reference_name_casing.rst>`_
- `clean_namespace <./../rules/namespace_notation/clean_namespace.rst>`_
- `concat_space <./../rules/operator/concat_space.rst>`_
- `echo_tag_syntax <./../rules/php_tag/echo_tag_syntax.rst>`_
- `empty_loop_body <./../rules/control_structure/empty_loop_body.rst>`_
  config:
  ``['style' => 'braces']``
- `empty_loop_condition <./../rules/control_structure/empty_loop_condition.rst>`_
- `fully_qualified_strict_types <./../rules/import/fully_qualified_strict_types.rst>`_
- `function_typehint_space <./../rules/function_notation/function_typehint_space.rst>`_
- `general_phpdoc_tag_rename <./../rules/phpdoc/general_phpdoc_tag_rename.rst>`_
  config:
  ``['replacements' => ['inheritDocs' => 'inheritDoc']]``
- `global_namespace_import <./../rules/import/global_namespace_import.rst>`_
  config:
  ``['import_classes' => false, 'import_constants' => false, 'import_functions' => false]``
- `include <./../rules/control_structure/include.rst>`_
- `increment_style <./../rules/operator/increment_style.rst>`_
- `integer_literal_case <./../rules/casing/integer_literal_case.rst>`_
- `lambda_not_used_import <./../rules/function_notation/lambda_not_used_import.rst>`_
- `linebreak_after_opening_tag <./../rules/php_tag/linebreak_after_opening_tag.rst>`_
- `magic_constant_casing <./../rules/casing/magic_constant_casing.rst>`_
- `magic_method_casing <./../rules/casing/magic_method_casing.rst>`_
- `method_argument_space <./../rules/function_notation/method_argument_space.rst>`_
  config:
  ``['on_multiline' => 'ignore']``
- `native_function_casing <./../rules/casing/native_function_casing.rst>`_
- `native_function_type_declaration_casing <./../rules/casing/native_function_type_declaration_casing.rst>`_
- `no_alias_language_construct_call <./../rules/alias/no_alias_language_construct_call.rst>`_
- `no_alternative_syntax <./../rules/control_structure/no_alternative_syntax.rst>`_
- `no_binary_string <./../rules/string_notation/no_binary_string.rst>`_
- `no_blank_lines_after_phpdoc <./../rules/phpdoc/no_blank_lines_after_phpdoc.rst>`_
- `no_empty_comment <./../rules/comment/no_empty_comment.rst>`_
- `no_empty_phpdoc <./../rules/phpdoc/no_empty_phpdoc.rst>`_
- `no_empty_statement <./../rules/semicolon/no_empty_statement.rst>`_
- `no_extra_blank_lines <./../rules/whitespace/no_extra_blank_lines.rst>`_
  config:
  ``['tokens' => ['attribute', 'case', 'continue', 'curly_brace_block', 'default', 'extra', 'parenthesis_brace_block', 'square_brace_block', 'switch', 'throw', 'use']]``
- `no_leading_namespace_whitespace <./../rules/namespace_notation/no_leading_namespace_whitespace.rst>`_
- `no_mixed_echo_print <./../rules/alias/no_mixed_echo_print.rst>`_
- `no_multiline_whitespace_around_double_arrow <./../rules/array_notation/no_multiline_whitespace_around_double_arrow.rst>`_
- `no_short_bool_cast <./../rules/cast_notation/no_short_bool_cast.rst>`_
- `no_singleline_whitespace_before_semicolons <./../rules/semicolon/no_singleline_whitespace_before_semicolons.rst>`_
- `no_spaces_around_offset <./../rules/whitespace/no_spaces_around_offset.rst>`_
- `no_superfluous_phpdoc_tags <./../rules/phpdoc/no_superfluous_phpdoc_tags.rst>`_
  config:
  ``['allow_mixed' => true, 'allow_unused_params' => true]``
- `no_trailing_comma_in_singleline <./../rules/basic/no_trailing_comma_in_singleline.rst>`_
- `no_unneeded_control_parentheses <./../rules/control_structure/no_unneeded_control_parentheses.rst>`_
  config:
  ``['statements' => ['break', 'clone', 'continue', 'echo_print', 'others', 'return', 'switch_case', 'yield', 'yield_from']]``
- `no_unneeded_curly_braces <./../rules/control_structure/no_unneeded_curly_braces.rst>`_
  config:
  ``['namespaces' => true]``
- `no_unneeded_import_alias <./../rules/import/no_unneeded_import_alias.rst>`_
- `no_unset_cast <./../rules/cast_notation/no_unset_cast.rst>`_
- `no_unused_imports <./../rules/import/no_unused_imports.rst>`_
- `no_useless_concat_operator <./../rules/operator/no_useless_concat_operator.rst>`_
- `no_useless_nullsafe_operator <./../rules/operator/no_useless_nullsafe_operator.rst>`_
- `no_whitespace_before_comma_in_array <./../rules/array_notation/no_whitespace_before_comma_in_array.rst>`_
- `normalize_index_brace <./../rules/array_notation/normalize_index_brace.rst>`_
- `object_operator_without_whitespace <./../rules/operator/object_operator_without_whitespace.rst>`_
- `ordered_imports <./../rules/import/ordered_imports.rst>`_
- `php_unit_fqcn_annotation <./../rules/php_unit/php_unit_fqcn_annotation.rst>`_
- `php_unit_method_casing <./../rules/php_unit/php_unit_method_casing.rst>`_
- `phpdoc_align <./../rules/phpdoc/phpdoc_align.rst>`_
- `phpdoc_annotation_without_dot <./../rules/phpdoc/phpdoc_annotation_without_dot.rst>`_
- `phpdoc_indent <./../rules/phpdoc/phpdoc_indent.rst>`_
- `phpdoc_inline_tag_normalizer <./../rules/phpdoc/phpdoc_inline_tag_normalizer.rst>`_
- `phpdoc_no_access <./../rules/phpdoc/phpdoc_no_access.rst>`_
- `phpdoc_no_alias_tag <./../rules/phpdoc/phpdoc_no_alias_tag.rst>`_
- `phpdoc_no_package <./../rules/phpdoc/phpdoc_no_package.rst>`_
- `phpdoc_no_useless_inheritdoc <./../rules/phpdoc/phpdoc_no_useless_inheritdoc.rst>`_
- `phpdoc_order <./../rules/phpdoc/phpdoc_order.rst>`_
  config:
  ``['order' => ['param', 'return', 'throws']]``
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
- `semicolon_after_instruction <./../rules/semicolon/semicolon_after_instruction.rst>`_
- `simple_to_complex_string_variable <./../rules/string_notation/simple_to_complex_string_variable.rst>`_
- `single_class_element_per_statement <./../rules/class_notation/single_class_element_per_statement.rst>`_
- `single_import_per_statement <./../rules/import/single_import_per_statement.rst>`_
- `single_line_comment_spacing <./../rules/comment/single_line_comment_spacing.rst>`_
- `single_line_comment_style <./../rules/comment/single_line_comment_style.rst>`_
  config:
  ``['comment_types' => ['hash']]``
- `single_line_throw <./../rules/function_notation/single_line_throw.rst>`_
- `single_quote <./../rules/string_notation/single_quote.rst>`_
- `single_space_after_construct <./../rules/language_construct/single_space_after_construct.rst>`_
  config:
  ``['constructs' => ['abstract', 'as', 'attribute', 'break', 'case', 'catch', 'class', 'clone', 'comment', 'const', 'const_import', 'continue', 'do', 'echo', 'else', 'elseif', 'enum', 'extends', 'final', 'finally', 'for', 'foreach', 'function', 'function_import', 'global', 'goto', 'if', 'implements', 'include', 'include_once', 'instanceof', 'insteadof', 'interface', 'match', 'named_argument', 'namespace', 'new', 'open_tag_with_echo', 'php_doc', 'php_open', 'print', 'private', 'protected', 'public', 'readonly', 'require', 'require_once', 'return', 'static', 'switch', 'throw', 'trait', 'try', 'type_colon', 'use', 'use_lambda', 'use_trait', 'var', 'while', 'yield', 'yield_from']]``
- `space_after_semicolon <./../rules/semicolon/space_after_semicolon.rst>`_
  config:
  ``['remove_in_empty_for_expressions' => true]``
- `standardize_increment <./../rules/operator/standardize_increment.rst>`_
- `standardize_not_equals <./../rules/operator/standardize_not_equals.rst>`_
- `switch_continue_to_break <./../rules/control_structure/switch_continue_to_break.rst>`_
- `trailing_comma_in_multiline <./../rules/control_structure/trailing_comma_in_multiline.rst>`_
- `trim_array_spaces <./../rules/array_notation/trim_array_spaces.rst>`_
- `types_spaces <./../rules/whitespace/types_spaces.rst>`_
- `unary_operator_spaces <./../rules/operator/unary_operator_spaces.rst>`_
- `whitespace_after_comma_in_array <./../rules/array_notation/whitespace_after_comma_in_array.rst>`_
- `yoda_style <./../rules/control_structure/yoda_style.rst>`_
