=======================================
Rule set ``@PHPUnit3x5Migration:risky``
=======================================

Rules to improve tests code for PHPUnit 3.5 compatibility.

Warning
-------

This rule set is RISKY
~~~~~~~~~~~~~~~~~~~~~~

This set contains rules that are risky. Using it may lead to changes in your
code's logic and behaviour. Use it with caution and review changes before
incorporating them into your code base.

Rules
-----

- `@PHPUnit3x2Migration:risky <./PHPUnit3x2MigrationRisky.rst>`_
- `php_unit_dedicate_assert <./../rules/php_unit/php_unit_dedicate_assert.rst>`_ with config:

  ``['target' => '3.5']``

