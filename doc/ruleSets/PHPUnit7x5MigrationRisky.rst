=======================================
Rule set ``@PHPUnit7x5Migration:risky``
=======================================

Rules to improve tests code for PHPUnit 7.5 compatibility.

Warning
-------

This set contains rules that are risky
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Using this rule set may lead to changes in your code's logic and behaviour. Use it with caution and review changes before incorporating them into your code base.

Rules
-----

- `@PHPUnit6x0Migration:risky <./PHPUnit6x0MigrationRisky.rst>`_
- `php_unit_dedicate_assert_internal_type <./../rules/php_unit/php_unit_dedicate_assert_internal_type.rst>`_ with config:

  ``['target' => '7.5']``

