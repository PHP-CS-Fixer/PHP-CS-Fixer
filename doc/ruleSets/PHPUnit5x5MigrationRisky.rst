=======================================
Rule set ``@PHPUnit5x5Migration:risky``
=======================================

Rules to improve tests code for PHPUnit 5.5 compatibility.

Warning
-------

This rule set is RISKY
~~~~~~~~~~~~~~~~~~~~~~

This set contains rules that are risky. Using it may lead to changes in your
code's logic and behaviour. Use it with caution and review changes before
incorporating them into your code base.

Rules
-----

- `@PHPUnit5x4Migration:risky <./PHPUnit5x4MigrationRisky.rst>`_
- `php_unit_mock <./../rules/php_unit/php_unit_mock.rst>`_ with config:

  ``['target' => '5.5']``

