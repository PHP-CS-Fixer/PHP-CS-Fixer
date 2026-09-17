============================
Rule ``heredoc_indentation``
============================

Heredoc/nowdoc content must be properly indented.

Warning
-------

This rule is CONFIGURABLE
~~~~~~~~~~~~~~~~~~~~~~~~~

You can configure this rule using the following option: ``indentation``.

Configuration
-------------

``indentation``
~~~~~~~~~~~~~~~

Whether the indentation should be the same as in the start token line or one
level more.

Allowed values: ``'same_as_start'`` and ``'start_plus_one'``

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
        $heredoc = <<<EOD
   -abc
   -    def
   -EOD;
   +        abc
   +            def
   +        EOD;

        $nowdoc = <<<'EOD'
   -abc
   -    def
   -EOD;
   +        abc
   +            def
   +        EOD;

Example #2
~~~~~~~~~~

With configuration: ``['indentation' => 'same_as_start']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
        $nowdoc = <<<'EOD'
   -abc
   -    def
   -EOD;
   +    abc
   +        def
   +    EOD;

Rule sets
---------

The rule is part of the following rule sets:

- `@PHP7x3Migration <./../../ruleSets/PHP7x3Migration.rst>`_
- `@PHP7x4Migration <./../../ruleSets/PHP7x4Migration.rst>`_
- `@PHP8x0Migration <./../../ruleSets/PHP8x0Migration.rst>`_
- `@PHP8x1Migration <./../../ruleSets/PHP8x1Migration.rst>`_
- `@PHP8x2Migration <./../../ruleSets/PHP8x2Migration.rst>`_
- `@PHP8x3Migration <./../../ruleSets/PHP8x3Migration.rst>`_
- `@PHP8x4Migration <./../../ruleSets/PHP8x4Migration.rst>`_
- `@PHP8x5Migration <./../../ruleSets/PHP8x5Migration.rst>`_
- `@PHP73Migration <./../../ruleSets/PHP73Migration.rst>`_ *(deprecated)*
- `@PHP74Migration <./../../ruleSets/PHP74Migration.rst>`_ *(deprecated)*
- `@PHP80Migration <./../../ruleSets/PHP80Migration.rst>`_ *(deprecated)*
- `@PHP81Migration <./../../ruleSets/PHP81Migration.rst>`_ *(deprecated)*
- `@PHP82Migration <./../../ruleSets/PHP82Migration.rst>`_ *(deprecated)*
- `@PHP83Migration <./../../ruleSets/PHP83Migration.rst>`_ *(deprecated)*
- `@PHP84Migration <./../../ruleSets/PHP84Migration.rst>`_ *(deprecated)*
- `@PHP85Migration <./../../ruleSets/PHP85Migration.rst>`_ *(deprecated)*

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Whitespace\\HeredocIndentationFixer <./../../../src/Fixer/Whitespace/HeredocIndentationFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Whitespace\\HeredocIndentationFixerTest <./../../../tests/Fixer/Whitespace/HeredocIndentationFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
