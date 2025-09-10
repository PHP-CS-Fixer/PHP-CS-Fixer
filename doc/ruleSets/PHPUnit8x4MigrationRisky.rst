=======================================
Rule set ``@PHPUnit8x4Migration:risky``
=======================================

Rules to improve tests code for PHPUnit 8.4 compatibility.

Warning
-------

This set contains rules that are risky
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Using this rule set may lead to changes in your code's logic and behaviour. Use it with caution and review changes before incorporating them into your code base.

Rules
-----

- `@PHPUnit7x5Migration:risky <./PHPUnit7x5MigrationRisky.rst>`_
- `php_unit_expectation <./../rules/php_unit/php_unit_expectation.rst>`_ with config:

  ``['target' => '8.4']``

