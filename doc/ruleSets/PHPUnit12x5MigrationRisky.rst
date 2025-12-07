========================================
Rule set ``@PHPUnit12x5Migration:risky``
========================================

Rules to improve tests code for PHPUnit 12.5 compatibility.

Warning
-------

This rule set is RISKY
~~~~~~~~~~~~~~~~~~~~~~

This set contains rules that are risky. Using it may lead to changes in your
code's logic and behaviour. Use it with caution and review changes before
incorporating them into your code base.

Rules
-----

- `@PHPUnit11x0Migration:risky <./PHPUnit11x0MigrationRisky.rst>`_
- `php_unit_test_case_static_method_calls <./../rules/php_unit/php_unit_test_case_static_method_calls.rst>`_ with config:

  ``['target' => '12.5']``

