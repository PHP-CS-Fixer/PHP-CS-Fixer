======================================
Rule set ``@PHPUnit84Migration:risky``
======================================

Rules to improve tests code for PHPUnit 8.4 compatibility.

Warning
-------

This set contains rules that are risky
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Using this rule set may lead to changes in your code's logic and behaviour. Use it with caution and review changes before incorporating them into your code base.

Rules
-----

- `@PHPUnit60Migration:risky <./PHPUnit60MigrationRisky.rst>`_
- `@PHPUnit75Migration:risky <./PHPUnit75MigrationRisky.rst>`_
- `php_unit_expectation <./../rules/php_unit/php_unit_expectation.rst>`_ with config:

  ``['target' => '8.4']``

