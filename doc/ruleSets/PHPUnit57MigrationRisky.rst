======================================
Rule set ``@PHPUnit57Migration:risky``
======================================

Rules to improve tests code for PHPUnit 5.7 compatibility.

Warning
-------

This set contains rules that are risky
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Using this rule set may lead to changes in your code's logic and behaviour. Use it with caution and review changes before incorporating them into your code base.

Rules
-----

- `@PHPUnit56Migration:risky <./PHPUnit56MigrationRisky.rst>`_
- `php_unit_namespaced <./../rules/php_unit/php_unit_namespaced.rst>`_ with config:

  ``['target' => '5.7']``

