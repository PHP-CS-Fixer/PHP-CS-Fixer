=======================================
Rule set ``@PHPUnit6x0Migration:risky``
=======================================

Rules to improve tests code for PHPUnit 6.0 compatibility.

Warning
-------

This rule set is RISKY
~~~~~~~~~~~~~~~~~~~~~~

This set contains rules that are risky. Using it may lead to changes in your
code's logic and behaviour. Use it with caution and review changes before
incorporating them into your code base.

Rules
-----

- `@PHPUnit5x7Migration:risky <./PHPUnit5x7MigrationRisky.rst>`_
- `php_unit_namespaced <./../rules/php_unit/php_unit_namespaced.rst>`_ with config:

  ``['target' => '6.0']``

