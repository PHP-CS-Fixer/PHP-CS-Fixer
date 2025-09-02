===================
Rule set ``@PSR12``
===================

Rules that follow `PSR-12 <https://www.php-fig.org/psr/psr-12/>`_ standard.

Rules
-----

- `@PSR2 <./PSR2.rst>`_
- `binary_operator_spaces <./../rules/operator/binary_operator_spaces.rst>`_ with config:

  ``['default' => 'at_least_single_space']``

- `blank_line_after_opening_tag <./../rules/php_tag/blank_line_after_opening_tag.rst>`_
- `blank_line_between_import_groups <./../rules/whitespace/blank_line_between_import_groups.rst>`_
- `blank_lines_before_namespace <./../rules/namespace_notation/blank_lines_before_namespace.rst>`_
- `braces_position <./../rules/basic/braces_position.rst>`_ with config:

  ``['allow_single_line_empty_anonymous_classes' => true]``

- `class_definition <./../rules/class_notation/class_definition.rst>`_ with config:

  ``['inline_constructor_arguments' => false, 'space_before_parenthesis' => true]``

- `compact_nullable_type_declaration <./../rules/whitespace/compact_nullable_type_declaration.rst>`_
- `declare_equal_normalize <./../rules/language_construct/declare_equal_normalize.rst>`_
- `lowercase_cast <./../rules/cast_notation/lowercase_cast.rst>`_
- `lowercase_static_reference <./../rules/casing/lowercase_static_reference.rst>`_
- `new_with_parentheses <./../rules/operator/new_with_parentheses.rst>`_ with config:

  ``['anonymous_class' => true]``

- `no_blank_lines_after_class_opening <./../rules/class_notation/no_blank_lines_after_class_opening.rst>`_
- `no_extra_blank_lines <./../rules/whitespace/no_extra_blank_lines.rst>`_ with config:

  ``['tokens' => ['use']]``

- `no_leading_import_slash <./../rules/import/no_leading_import_slash.rst>`_
- `no_whitespace_in_blank_line <./../rules/whitespace/no_whitespace_in_blank_line.rst>`_
- `ordered_class_elements <./../rules/class_notation/ordered_class_elements.rst>`_ with config:

  ``['order' => ['use_trait']]``

- `ordered_imports <./../rules/import/ordered_imports.rst>`_ with config:

  ``['imports_order' => ['class', 'function', 'const'], 'sort_algorithm' => 'none']``

- `return_type_declaration <./../rules/function_notation/return_type_declaration.rst>`_
- `short_scalar_cast <./../rules/cast_notation/short_scalar_cast.rst>`_
- `single_import_per_statement <./../rules/import/single_import_per_statement.rst>`_ with config:

  ``['group_to_single_imports' => false]``

- `single_space_around_construct <./../rules/language_construct/single_space_around_construct.rst>`_ with config:

  ``['constructs_followed_by_a_single_space' => ['abstract', 'as', 'case', 'catch', 'class', 'const_import', 'do', 'else', 'elseif', 'final', 'finally', 'for', 'foreach', 'function', 'function_import', 'if', 'insteadof', 'interface', 'namespace', 'new', 'private', 'protected', 'public', 'static', 'switch', 'trait', 'try', 'use', 'use_lambda', 'while'], 'constructs_preceded_by_a_single_space' => ['as', 'else', 'elseif', 'use_lambda']]``

- `single_trait_insert_per_statement <./../rules/class_notation/single_trait_insert_per_statement.rst>`_
- `ternary_operator_spaces <./../rules/operator/ternary_operator_spaces.rst>`_
- `unary_operator_spaces <./../rules/operator/unary_operator_spaces.rst>`_ with config:

  ``['only_dec_inc' => true]``

- `visibility_required <./../rules/class_notation/visibility_required.rst>`_
