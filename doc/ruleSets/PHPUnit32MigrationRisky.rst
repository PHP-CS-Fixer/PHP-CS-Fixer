======================================
Rule set ``@PHPUnit32Migration:risky``
======================================

Rules to improve tests code for PHPUnit 3.2 compatibility.

Warning
-------

This set contains rules that are risky
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Using this rule set may lead to changes in your code's logic and behaviour. Use it with caution and review changes before incorporating them into your code base.

Rules
-----

- `@PHPUnit30Migration:risky <./PHPUnit30MigrationRisky.rst>`_
- `php_unit_no_expectation_annotation <./../rules/php_unit/php_unit_no_expectation_annotation.rst>`_ with config:

  ``['target' => '3.2']``

