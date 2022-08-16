==============================
Rule set ``@PhpCsFixer:risky``
==============================

Rule set as used by the PHP-CS-Fixer development team, highly opinionated. This set contains rules that are risky.

Rules
-----

- `@PER:risky <./PERRisky.rst>`_
- `@Symfony:risky <./SymfonyRisky.rst>`_
- `comment_to_phpdoc <./../rules/comment/comment_to_phpdoc.rst>`_
- `final_internal_class <./../rules/class_notation/final_internal_class.rst>`_
- `native_constant_invocation <./../rules/constant_notation/native_constant_invocation.rst>`_
  config:
  ``['fix_built_in' => false, 'include' => ['DIRECTORY_SEPARATOR', 'PHP_INT_SIZE', 'PHP_SAPI', 'PHP_VERSION_ID'], 'scope' => 'namespaced', 'strict' => true]``
- `no_alias_functions <./../rules/alias/no_alias_functions.rst>`_
  config:
  ``['sets' => ['@all']]``
- `no_unreachable_default_argument_value <./../rules/function_notation/no_unreachable_default_argument_value.rst>`_
- `no_unset_on_property <./../rules/language_construct/no_unset_on_property.rst>`_
- `php_unit_strict <./../rules/php_unit/php_unit_strict.rst>`_
- `php_unit_test_case_static_method_calls <./../rules/php_unit/php_unit_test_case_static_method_calls.rst>`_
- `strict_comparison <./../rules/strict/strict_comparison.rst>`_
- `strict_param <./../rules/strict/strict_param.rst>`_
