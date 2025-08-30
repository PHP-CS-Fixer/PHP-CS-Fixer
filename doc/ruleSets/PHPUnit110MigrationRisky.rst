=======================================
Rule set ``@PHPUnit110Migration:risky``
=======================================

Rules to improve tests code for PHPUnit 11.0 compatibility.

Warning
-------

This set contains rules that are risky
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Using this rule set may lead to changes in your code's logic and behaviour. Use it with caution and review changes before incorporating them into your code base.

Rules
-----

- `@PHPUnit100Migration:risky <./PHPUnit100MigrationRisky.rst>`_
- `php_unit_test_case_static_method_calls <./../rules/php_unit/php_unit_test_case_static_method_calls.rst>`_ with config:

  ``['target' => '11.0']``

