======================================
Rule set ``@PHPUnit50Migration:risky``
======================================

Rules to improve tests code for PHPUnit 5.0 compatibility.

Warning
-------

This set contains rules that are risky
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Using this rule set may lead to changes in your code's logic and behaviour. Use it with caution and review changes before incorporating them into your code base.

Rules
-----

- `@PHPUnit48Migration:risky <./PHPUnit48MigrationRisky.rst>`_
- `php_unit_dedicate_assert <./../rules/php_unit/php_unit_dedicate_assert.rst>`_ with config:

  ``['target' => '5.0']``

