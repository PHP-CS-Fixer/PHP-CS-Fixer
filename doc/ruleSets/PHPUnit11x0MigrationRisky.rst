========================================
Rule set ``@PHPUnit11x0Migration:risky``
========================================

Rules to improve tests code for PHPUnit 11.0 compatibility.

Warnings
--------

This rule set is deprecated and will be removed in the next major version
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

You should use ``@PHPUnit100Migration:risky`` and
``php_unit_test_case_static_method_calls`` instead.

This set contains rules that are risky
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Using this rule set may lead to changes in your code's logic and behaviour. Use it with caution and review changes before incorporating them into your code base.

Rules
-----

- `@PHPUnit100Migration:risky <./PHPUnit100MigrationRisky.rst>`_
- `php_unit_test_case_static_method_calls <./../rules/php_unit/php_unit_test_case_static_method_calls.rst>`_ with config:

  ``['target' => '11.0']``

