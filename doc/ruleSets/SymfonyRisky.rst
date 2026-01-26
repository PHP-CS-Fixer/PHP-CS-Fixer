===========================
Rule set ``@Symfony:risky``
===========================

Rules that follow the official `Symfony Coding Standards <https://symfony.com/doc/current/contributing/code/standards.html>`_. Extends ``@PER-CS:risky``.

Warning
-------

This rule set is RISKY
~~~~~~~~~~~~~~~~~~~~~~

This set contains rules that are risky. Using it may lead to changes in your
code's logic and behaviour. Use it with caution and review changes before
incorporating them into your code base.

Rules
-----

- `@PHP5x6Migration:risky <./PHP5x6MigrationRisky.rst>`_
- `@PSR12:risky <./PSR12Risky.rst>`_
- `array_push <./../rules/alias/array_push.rst>`_
- `combine_nested_dirname <./../rules/function_notation/combine_nested_dirname.rst>`_
- `declare_strict_types <./../rules/strict/declare_strict_types.rst>`_ with config:

  ``['remove_existing_declaration' => true]``

- `dir_constant <./../rules/language_construct/dir_constant.rst>`_
- `ereg_to_preg <./../rules/alias/ereg_to_preg.rst>`_
- `error_suppression <./../rules/language_construct/error_suppression.rst>`_
- `fopen_flag_order <./../rules/function_notation/fopen_flag_order.rst>`_
- `fopen_flags <./../rules/function_notation/fopen_flags.rst>`_ with config:

  ``['b_mode' => false]``

- `function_to_constant <./../rules/language_construct/function_to_constant.rst>`_
- `get_class_to_class_keyword <./../rules/language_construct/get_class_to_class_keyword.rst>`_
- `implode_call <./../rules/function_notation/implode_call.rst>`_
- `is_null <./../rules/language_construct/is_null.rst>`_
- `logical_operators <./../rules/operator/logical_operators.rst>`_
- `long_to_shorthand_operator <./../rules/operator/long_to_shorthand_operator.rst>`_
- `modern_serialization_methods <./../rules/class_notation/modern_serialization_methods.rst>`_
- `modernize_strpos <./../rules/alias/modernize_strpos.rst>`_
- `modernize_types_casting <./../rules/cast_notation/modernize_types_casting.rst>`_
- `native_constant_invocation <./../rules/constant_notation/native_constant_invocation.rst>`_ with config:

  ``['strict' => false]``

- `native_function_invocation <./../rules/function_notation/native_function_invocation.rst>`_ with config:

  ``['include' => ['@compiler_optimized'], 'scope' => 'namespaced', 'strict' => true]``

- `no_alias_functions <./../rules/alias/no_alias_functions.rst>`_
- `no_homoglyph_names <./../rules/naming/no_homoglyph_names.rst>`_
- `no_php4_constructor <./../rules/class_notation/no_php4_constructor.rst>`_
- `no_unneeded_final_method <./../rules/class_notation/no_unneeded_final_method.rst>`_
- `no_useless_sprintf <./../rules/function_notation/no_useless_sprintf.rst>`_
- `non_printable_character <./../rules/basic/non_printable_character.rst>`_
- `ordered_traits <./../rules/class_notation/ordered_traits.rst>`_
- `php_unit_construct <./../rules/php_unit/php_unit_construct.rst>`_
- `php_unit_mock_short_will_return <./../rules/php_unit/php_unit_mock_short_will_return.rst>`_
- `php_unit_set_up_tear_down_visibility <./../rules/php_unit/php_unit_set_up_tear_down_visibility.rst>`_
- `php_unit_test_annotation <./../rules/php_unit/php_unit_test_annotation.rst>`_
- `psr_autoloading <./../rules/basic/psr_autoloading.rst>`_
- `self_accessor <./../rules/class_notation/self_accessor.rst>`_
- `set_type_to_cast <./../rules/alias/set_type_to_cast.rst>`_
- `static_lambda <./../rules/function_notation/static_lambda.rst>`_
- `string_length_to_empty <./../rules/string_notation/string_length_to_empty.rst>`_
- `string_line_ending <./../rules/string_notation/string_line_ending.rst>`_
- `ternary_to_elvis_operator <./../rules/operator/ternary_to_elvis_operator.rst>`_

Disabled rules
--------------

- `no_trailing_whitespace_in_string <./../rules/string_notation/no_trailing_whitespace_in_string.rst>`_
