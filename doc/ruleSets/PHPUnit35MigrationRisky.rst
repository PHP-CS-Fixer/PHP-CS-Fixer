======================================
Rule set ``@PHPUnit35Migration:risky``
======================================

Rules to improve tests code for PHPUnit 3.5 compatibility.

Warning
-------

This set contains rules that are risky
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Using this rule set may lead to changes in your code's logic and behaviour. Use it with caution and review changes before incorporating them into your code base.

Rules
-----

- `@PHPUnit32Migration:risky <./PHPUnit32MigrationRisky.rst>`_
- `php_unit_dedicate_assert <./../rules/php_unit/php_unit_dedicate_assert.rst>`_ with config:

  ``['target' => '3.5']``

