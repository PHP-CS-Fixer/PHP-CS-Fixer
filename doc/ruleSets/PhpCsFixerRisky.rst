==============================
Rule set ``@PhpCsFixer:risky``
==============================

Rules recommended by ``PHP CS Fixer`` team, highly opinionated. Extends ``@PER-CS:risky`` and ``@Symfony:risky``.

Warning
-------

This rule set is RISKY
~~~~~~~~~~~~~~~~~~~~~~

This set contains rules that are risky. Using it may lead to changes in your
code's logic and behaviour. Use it with caution and review changes before
incorporating them into your code base.

Rules
-----

- `@PER-CS:risky <./PER-CSRisky.rst>`_
- `@Symfony:risky <./SymfonyRisky.rst>`_
- `comment_to_phpdoc <./../rules/comment/comment_to_phpdoc.rst>`_
- `final_internal_class <./../rules/class_notation/final_internal_class.rst>`_
- `native_constant_invocation <./../rules/constant_notation/native_constant_invocation.rst>`_ with config:

  ``['fix_built_in' => false, 'include' => ['DIRECTORY_SEPARATOR', 'PHP_INT_SIZE', 'PHP_SAPI', 'PHP_VERSION_ID'], 'scope' => 'namespaced', 'strict' => true]``

- `no_alias_functions <./../rules/alias/no_alias_functions.rst>`_ with config:

  ``['sets' => ['@all']]``

- `no_trailing_whitespace_in_string <./../rules/string_notation/no_trailing_whitespace_in_string.rst>`_
- `no_unset_on_property <./../rules/language_construct/no_unset_on_property.rst>`_
- `php_unit_data_provider_name <./../rules/php_unit/php_unit_data_provider_name.rst>`_
- `php_unit_data_provider_return_type <./../rules/php_unit/php_unit_data_provider_return_type.rst>`_
- `php_unit_data_provider_static <./../rules/php_unit/php_unit_data_provider_static.rst>`_ with config:

  ``['force' => true]``

- `php_unit_strict <./../rules/php_unit/php_unit_strict.rst>`_
- `php_unit_test_case_static_method_calls <./../rules/php_unit/php_unit_test_case_static_method_calls.rst>`_ with config:

  ``['call_type' => 'self']``

- `static_lambda <./../rules/function_notation/static_lambda.rst>`_
- `strict_comparison <./../rules/strict/strict_comparison.rst>`_
- `strict_param <./../rules/strict/strict_param.rst>`_
- `yield_from_array_to_yields <./../rules/array_notation/yield_from_array_to_yields.rst>`_

Disabled rules
--------------

- `get_class_to_class_keyword <./../rules/language_construct/get_class_to_class_keyword.rst>`_
- `modernize_strpos <./../rules/alias/modernize_strpos.rst>`_
