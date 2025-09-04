======================================
Rule set ``@PHPUnit56Migration:risky``
======================================

Rules to improve tests code for PHPUnit 5.6 compatibility.

Warning
-------

This set contains rules that are risky
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Using this rule set may lead to changes in your code's logic and behaviour. Use it with caution and review changes before incorporating them into your code base.

Rules
-----

- `@PHPUnit55Migration:risky <./PHPUnit55MigrationRisky.rst>`_
- `php_unit_dedicate_assert <./../rules/php_unit/php_unit_dedicate_assert.rst>`_ with config:

  ``['target' => '5.6']``

- `php_unit_expectation <./../rules/php_unit/php_unit_expectation.rst>`_ with config:

  ``['target' => '5.6']``

