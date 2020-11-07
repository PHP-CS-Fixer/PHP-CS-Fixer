==================================
Rule set ``@PHP70Migration:risky``
==================================

Rules to improve code for PHP 7.0 compatibility. This set contains rules that are risky.

Rules
-----

- `@PHP56Migration:risky <./PHP56MigrationRisky.rst>`_
- `combine_nested_dirname <./../rules/function_notation/combine_nested_dirname.rst>`_
- `declare_strict_types <./../rules/strict/declare_strict_types.rst>`_
- `non_printable_character <./../rules/basic/non_printable_character.rst>`_
  config:
  ``['use_escape_sequences_in_strings' => true]``
- `random_api_migration <./../rules/alias/random_api_migration.rst>`_
  config:
  ``['replacements' => ['mt_rand' => 'random_int', 'rand' => 'random_int']]``
