======================================
Rule set ``@PHPUnit43Migration:risky``
======================================

Rules to improve tests code for PHPUnit 4.3 compatibility.

Warning
-------

This set contains rules that are risky
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Using this rule set may lead to changes in your code's logic and behaviour. Use it with caution and review changes before incorporating them into your code base.

Rules
-----

- `@PHPUnit35Migration:risky <./PHPUnit35MigrationRisky.rst>`_
- `php_unit_no_expectation_annotation <./../rules/php_unit/php_unit_no_expectation_annotation.rst>`_ with config:

  ``['target' => '4.3']``

