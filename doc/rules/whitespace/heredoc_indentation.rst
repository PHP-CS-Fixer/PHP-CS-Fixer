============================
Rule ``heredoc_indentation``
============================

Heredoc/nowdoc content must be properly indented. Requires PHP >= 7.3.

Configuration
-------------

``indentation``
~~~~~~~~~~~~~~~

Whether the indentation should be the same as in the start token line or one
level more.

Allowed values: ``'same_as_start'``, ``'start_plus_one'``

Default value: ``'start_plus_one'``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php
        $a = <<<EOD
   -abc
   -    def
   -EOD;
   +        abc
   +            def
   +        EOD;

Example #2
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php
        $a = <<<'EOD'
   -abc
   -    def
   -EOD;
   +        abc
   +            def
   +        EOD;

Example #3
~~~~~~~~~~

With configuration: ``['indentation' => 'same_as_start']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
        $a = <<<'EOD'
   -abc
   -    def
   -EOD;
   +    abc
   +        def
   +    EOD;

Rule sets
---------

The rule is part of the following rule sets:

@PHP73Migration
  Using the `@PHP73Migration <./../../ruleSets/PHP73Migration.rst>`_ rule set will enable the ``heredoc_indentation`` rule with the default config.

@PHP74Migration
  Using the `@PHP74Migration <./../../ruleSets/PHP74Migration.rst>`_ rule set will enable the ``heredoc_indentation`` rule with the default config.

@PHP80Migration
  Using the `@PHP80Migration <./../../ruleSets/PHP80Migration.rst>`_ rule set will enable the ``heredoc_indentation`` rule with the default config.
