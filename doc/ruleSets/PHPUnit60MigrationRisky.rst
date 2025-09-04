======================================
Rule set ``@PHPUnit60Migration:risky``
======================================

Rules to improve tests code for PHPUnit 6.0 compatibility.

Warning
-------

This set contains rules that are risky
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Using this rule set may lead to changes in your code's logic and behaviour. Use it with caution and review changes before incorporating them into your code base.

Rules
-----

- `@PHPUnit57Migration:risky <./PHPUnit57MigrationRisky.rst>`_
- `php_unit_namespaced <./../rules/php_unit/php_unit_namespaced.rst>`_ with config:

  ``['target' => '6.0']``

