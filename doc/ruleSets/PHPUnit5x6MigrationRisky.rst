=======================================
Rule set ``@PHPUnit5x6Migration:risky``
=======================================

Rules to improve tests code for PHPUnit 5.6 compatibility.

Warning
-------

This set contains rules that are risky
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Using this rule set may lead to changes in your code's logic and behaviour. Use it with caution and review changes before incorporating them into your code base.

Rules
-----

- `@PHPUnit5x5Migration:risky <./PHPUnit5x5MigrationRisky.rst>`_
- `php_unit_dedicate_assert <./../rules/php_unit/php_unit_dedicate_assert.rst>`_ with config:

  ``['target' => '5.6']``

- `php_unit_expectation <./../rules/php_unit/php_unit_expectation.rst>`_ with config:

  ``['target' => '5.6']``

