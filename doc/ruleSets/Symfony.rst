=====================
Rule set ``@Symfony``
=====================

Rules that follow the official `Symfony Coding Standards <https://symfony.com/doc/current/contributing/code/standards.html>`_. Extends ``@PER-CS``.

Rules
-----

- `@PER-CS3x0 <./PER-CS3x0.rst>`_
- `align_multiline_comment <./../rules/phpdoc/align_multiline_comment.rst>`_
- `backtick_to_shell_exec <./../rules/alias/backtick_to_shell_exec.rst>`_
- `binary_operator_spaces <./../rules/operator/binary_operator_spaces.rst>`_
- `blank_line_before_statement <./../rules/whitespace/blank_line_before_statement.rst>`_ with config:

  ``['statements' => ['return']]``

- `braces_position <./../rules/basic/braces_position.rst>`_ with config:

  ``['allow_single_line_anonymous_functions' => true, 'allow_single_line_empty_anonymous_classes' => true]``

- `class_attributes_separation <./../rules/class_notation/class_attributes_separation.rst>`_ with config:

  ``['elements' => ['method' => 'one']]``

- `class_definition <./../rules/class_notation/class_definition.rst>`_ with config:

  ``['single_line' => true]``

- `class_reference_name_casing <./../rules/casing/class_reference_name_casing.rst>`_
- `clean_namespace <./../rules/namespace_notation/clean_namespace.rst>`_
- `concat_space <./../rules/operator/concat_space.rst>`_
- `declare_parentheses <./../rules/language_construct/declare_parentheses.rst>`_
- `echo_tag_syntax <./../rules/php_tag/echo_tag_syntax.rst>`_
- `empty_loop_body <./../rules/control_structure/empty_loop_body.rst>`_ with config:

  ``['style' => 'braces']``

- `empty_loop_condition <./../rules/control_structure/empty_loop_condition.rst>`_
- `fully_qualified_strict_types <./../rules/import/fully_qualified_strict_types.rst>`_
- `function_declaration <./../rules/function_notation/function_declaration.rst>`_ with config:

  ``['closure_fn_spacing' => 'one']``

- `general_phpdoc_tag_rename <./../rules/phpdoc/general_phpdoc_tag_rename.rst>`_ with config:

  ``['replacements' => ['inheritDocs' => 'inheritDoc']]``

- `global_namespace_import <./../rules/import/global_namespace_import.rst>`_ with config:

  ``['import_classes' => false, 'import_constants' => false, 'import_functions' => false]``

- `include <./../rules/control_structure/include.rst>`_
- `increment_style <./../rules/operator/increment_style.rst>`_
- `integer_literal_case <./../rules/casing/integer_literal_case.rst>`_
- `lambda_not_used_import <./../rules/function_notation/lambda_not_used_import.rst>`_
- `linebreak_after_opening_tag <./../rules/php_tag/linebreak_after_opening_tag.rst>`_
- `magic_constant_casing <./../rules/casing/magic_constant_casing.rst>`_
- `magic_method_casing <./../rules/casing/magic_method_casing.rst>`_
- `method_argument_space <./../rules/function_notation/method_argument_space.rst>`_ with config:

  ``['after_heredoc' => true, 'on_multiline' => 'ignore']``

- `native_function_casing <./../rules/casing/native_function_casing.rst>`_
- `native_type_declaration_casing <./../rules/casing/native_type_declaration_casing.rst>`_
- `no_alias_language_construct_call <./../rules/alias/no_alias_language_construct_call.rst>`_
- `no_alternative_syntax <./../rules/control_structure/no_alternative_syntax.rst>`_
- `no_binary_string <./../rules/string_notation/no_binary_string.rst>`_
- `no_blank_lines_after_phpdoc <./../rules/phpdoc/no_blank_lines_after_phpdoc.rst>`_
- `no_empty_comment <./../rules/comment/no_empty_comment.rst>`_
- `no_empty_phpdoc <./../rules/phpdoc/no_empty_phpdoc.rst>`_
- `no_empty_statement <./../rules/semicolon/no_empty_statement.rst>`_
- `no_extra_blank_lines <./../rules/whitespace/no_extra_blank_lines.rst>`_ with config:

  ``['tokens' => ['attribute', 'case', 'continue', 'curly_brace_block', 'default', 'extra', 'parenthesis_brace_block', 'square_brace_block', 'switch', 'throw', 'use']]``

- `no_leading_namespace_whitespace <./../rules/namespace_notation/no_leading_namespace_whitespace.rst>`_
- `no_mixed_echo_print <./../rules/alias/no_mixed_echo_print.rst>`_
- `no_multiline_whitespace_around_double_arrow <./../rules/array_notation/no_multiline_whitespace_around_double_arrow.rst>`_
- `no_null_property_initialization <./../rules/class_notation/no_null_property_initialization.rst>`_
- `no_short_bool_cast <./../rules/cast_notation/no_short_bool_cast.rst>`_
- `no_singleline_whitespace_before_semicolons <./../rules/semicolon/no_singleline_whitespace_before_semicolons.rst>`_
- `no_spaces_around_offset <./../rules/whitespace/no_spaces_around_offset.rst>`_
- `no_superfluous_phpdoc_tags <./../rules/phpdoc/no_superfluous_phpdoc_tags.rst>`_ with config:

  ``['allow_hidden_params' => true, 'remove_inheritdoc' => true]``

- `no_trailing_comma_in_singleline <./../rules/basic/no_trailing_comma_in_singleline.rst>`_
- `no_unneeded_braces <./../rules/control_structure/no_unneeded_braces.rst>`_ with config:

  ``['namespaces' => true]``

- `no_unneeded_control_parentheses <./../rules/control_structure/no_unneeded_control_parentheses.rst>`_ with config:

  ``['statements' => ['break', 'clone', 'continue', 'echo_print', 'others', 'return', 'switch_case', 'yield', 'yield_from']]``

- `no_unneeded_import_alias <./../rules/import/no_unneeded_import_alias.rst>`_
- `no_unset_cast <./../rules/cast_notation/no_unset_cast.rst>`_
- `no_unused_imports <./../rules/import/no_unused_imports.rst>`_
- `no_useless_concat_operator <./../rules/operator/no_useless_concat_operator.rst>`_
- `no_useless_nullsafe_operator <./../rules/operator/no_useless_nullsafe_operator.rst>`_
- `no_whitespace_before_comma_in_array <./../rules/array_notation/no_whitespace_before_comma_in_array.rst>`_ with config:

  ``['after_heredoc' => true]``

- `normalize_index_brace <./../rules/array_notation/normalize_index_brace.rst>`_
- `nullable_type_declaration_for_default_null_value <./../rules/function_notation/nullable_type_declaration_for_default_null_value.rst>`_
- `object_operator_without_whitespace <./../rules/operator/object_operator_without_whitespace.rst>`_
- `operator_linebreak <./../rules/operator/operator_linebreak.rst>`_ with config:

  ``['only_booleans' => true]``

- `ordered_imports <./../rules/import/ordered_imports.rst>`_ with config:

  ``['imports_order' => ['class', 'function', 'const'], 'sort_algorithm' => 'alpha']``

- `php_unit_fqcn_annotation <./../rules/php_unit/php_unit_fqcn_annotation.rst>`_
- `php_unit_method_casing <./../rules/php_unit/php_unit_method_casing.rst>`_
- `phpdoc_align <./../rules/phpdoc/phpdoc_align.rst>`_
- `phpdoc_annotation_without_dot <./../rules/phpdoc/phpdoc_annotation_without_dot.rst>`_
- `phpdoc_indent <./../rules/phpdoc/phpdoc_indent.rst>`_
- `phpdoc_inline_tag_normalizer <./../rules/phpdoc/phpdoc_inline_tag_normalizer.rst>`_
- `phpdoc_no_access <./../rules/phpdoc/phpdoc_no_access.rst>`_
- `phpdoc_no_alias_tag <./../rules/phpdoc/phpdoc_no_alias_tag.rst>`_ with config:

  ``['replacements' => ['const' => 'var', 'link' => 'see', 'property-read' => 'property', 'property-write' => 'property', 'type' => 'var']]``

- `phpdoc_no_package <./../rules/phpdoc/phpdoc_no_package.rst>`_
- `phpdoc_no_useless_inheritdoc <./../rules/phpdoc/phpdoc_no_useless_inheritdoc.rst>`_
- `phpdoc_order <./../rules/phpdoc/phpdoc_order.rst>`_ with config:

  ``['order' => ['param', 'return', 'throws']]``

- `phpdoc_return_self_reference <./../rules/phpdoc/phpdoc_return_self_reference.rst>`_
- `phpdoc_scalar <./../rules/phpdoc/phpdoc_scalar.rst>`_ with config:

  ``['types' => ['boolean', 'callback', 'double', 'integer', 'never-return', 'never-returns', 'no-return', 'real', 'str']]``

- `phpdoc_separation <./../rules/phpdoc/phpdoc_separation.rst>`_ with config:

  ``['groups' => [['Annotation', 'NamedArgumentConstructor', 'Target'], ['author', 'copyright', 'license'], ['category', 'package', 'subpackage'], ['property', 'property-read', 'property-write'], ['deprecated', 'link', 'see', 'since']], 'skip_unlisted_annotations' => false]``

- `phpdoc_single_line_var_spacing <./../rules/phpdoc/phpdoc_single_line_var_spacing.rst>`_
- `phpdoc_summary <./../rules/phpdoc/phpdoc_summary.rst>`_
- `phpdoc_tag_type <./../rules/phpdoc/phpdoc_tag_type.rst>`_ with config:

  ``['tags' => ['inheritDoc' => 'inline']]``

- `phpdoc_to_comment <./../rules/phpdoc/phpdoc_to_comment.rst>`_ with config:

  ``['allow_before_return_statement' => false]``

- `phpdoc_trim <./../rules/phpdoc/phpdoc_trim.rst>`_
- `phpdoc_trim_consecutive_blank_line_separation <./../rules/phpdoc/phpdoc_trim_consecutive_blank_line_separation.rst>`_
- `phpdoc_types <./../rules/phpdoc/phpdoc_types.rst>`_
- `phpdoc_types_order <./../rules/phpdoc/phpdoc_types_order.rst>`_ with config:

  ``['null_adjustment' => 'always_last', 'sort_algorithm' => 'none']``

- `phpdoc_var_annotation_correct_order <./../rules/phpdoc/phpdoc_var_annotation_correct_order.rst>`_
- `phpdoc_var_without_name <./../rules/phpdoc/phpdoc_var_without_name.rst>`_
- `protected_to_private <./../rules/class_notation/protected_to_private.rst>`_
- `semicolon_after_instruction <./../rules/semicolon/semicolon_after_instruction.rst>`_
- `simple_to_complex_string_variable <./../rules/string_notation/simple_to_complex_string_variable.rst>`_
- `single_import_per_statement <./../rules/import/single_import_per_statement.rst>`_
- `single_line_comment_spacing <./../rules/comment/single_line_comment_spacing.rst>`_
- `single_line_comment_style <./../rules/comment/single_line_comment_style.rst>`_ with config:

  ``['comment_types' => ['hash']]``

- `single_line_throw <./../rules/function_notation/single_line_throw.rst>`_
- `single_quote <./../rules/string_notation/single_quote.rst>`_
- `single_space_around_construct <./../rules/language_construct/single_space_around_construct.rst>`_
- `space_after_semicolon <./../rules/semicolon/space_after_semicolon.rst>`_ with config:

  ``['remove_in_empty_for_expressions' => true]``

- `standardize_increment <./../rules/operator/standardize_increment.rst>`_
- `standardize_not_equals <./../rules/operator/standardize_not_equals.rst>`_
- `statement_indentation <./../rules/whitespace/statement_indentation.rst>`_ with config:

  ``['stick_comment_to_next_continuous_control_statement' => true]``

- `switch_continue_to_break <./../rules/control_structure/switch_continue_to_break.rst>`_
- `trailing_comma_in_multiline <./../rules/control_structure/trailing_comma_in_multiline.rst>`_ with config:

  ``['after_heredoc' => true, 'elements' => ['array_destructuring', 'arrays', 'match', 'parameters']]``

- `trim_array_spaces <./../rules/array_notation/trim_array_spaces.rst>`_
- `type_declaration_spaces <./../rules/whitespace/type_declaration_spaces.rst>`_ with config:

  ``['elements' => ['function', 'property']]``

- `unary_operator_spaces <./../rules/operator/unary_operator_spaces.rst>`_
- `whitespace_after_comma_in_array <./../rules/array_notation/whitespace_after_comma_in_array.rst>`_
- `yoda_style <./../rules/control_structure/yoda_style.rst>`_

Disabled rules
--------------

- `single_line_empty_body <./../rules/basic/single_line_empty_body.rst>`_
