===================================
Rule set ``@PHP7x0Migration:risky``
===================================

Rules to improve code for PHP 7.0 compatibility.

Warning
-------

This set contains rules that are risky
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Using this rule set may lead to changes in your code's logic and behaviour. Use it with caution and review changes before incorporating them into your code base.

Rules
-----

- `@PHP5x6Migration:risky <./PHP5x6MigrationRisky.rst>`_
- `combine_nested_dirname <./../rules/function_notation/combine_nested_dirname.rst>`_
- `declare_strict_types <./../rules/strict/declare_strict_types.rst>`_
- `non_printable_character <./../rules/basic/non_printable_character.rst>`_
- `random_api_migration <./../rules/alias/random_api_migration.rst>`_ with config:

  ``['replacements' => ['mt_rand' => 'random_int', 'rand' => 'random_int']]``

