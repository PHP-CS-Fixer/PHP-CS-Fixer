===================================
Rule set ``@PHP8x0Migration:risky``
===================================

Rules to improve code for PHP 8.0 compatibility.

Warning
-------

This rule set is RISKY
~~~~~~~~~~~~~~~~~~~~~~

This set contains rules that are risky. Using it may lead to changes in your
code's logic and behaviour. Use it with caution and review changes before
incorporating them into your code base.

Rules
-----

- `@PHP7x4Migration:risky <./PHP7x4MigrationRisky.rst>`_
- `get_class_to_class_keyword <./../rules/language_construct/get_class_to_class_keyword.rst>`_
- `modernize_strpos <./../rules/alias/modernize_strpos.rst>`_
- `no_alias_functions <./../rules/alias/no_alias_functions.rst>`_ with config:

  ``['sets' => ['@all']]``

- `no_php4_constructor <./../rules/class_notation/no_php4_constructor.rst>`_
- `no_unneeded_final_method <./../rules/class_notation/no_unneeded_final_method.rst>`_
- `no_unreachable_default_argument_value <./../rules/function_notation/no_unreachable_default_argument_value.rst>`_
