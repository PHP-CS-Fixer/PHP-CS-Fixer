=======================================
Rule set ``@PHPUnit4x3Migration:risky``
=======================================

Rules to improve tests code for PHPUnit 4.3 compatibility.

Warning
-------

This rule set is RISKY
~~~~~~~~~~~~~~~~~~~~~~

This set contains rules that are risky. Using it may lead to changes in your
code's logic and behaviour. Use it with caution and review changes before
incorporating them into your code base.

Rules
-----

- `@PHPUnit3x5Migration:risky <./PHPUnit3x5MigrationRisky.rst>`_
- `php_unit_no_expectation_annotation <./../rules/php_unit/php_unit_no_expectation_annotation.rst>`_ with config:

  ``['target' => '4.3']``

