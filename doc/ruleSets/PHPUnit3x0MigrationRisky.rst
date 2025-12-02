=======================================
Rule set ``@PHPUnit3x0Migration:risky``
=======================================

Rules to improve tests code for PHPUnit 3.0 compatibility.

Warning
-------

This rule set is RISKY
~~~~~~~~~~~~~~~~~~~~~~

This set contains rules that are risky. Using it may lead to changes in your
code's logic and behaviour. Use it with caution and review changes before
incorporating them into your code base.

Rules
-----

- `php_unit_dedicate_assert <./../rules/php_unit/php_unit_dedicate_assert.rst>`_ with config:

  ``['target' => '3.0']``

