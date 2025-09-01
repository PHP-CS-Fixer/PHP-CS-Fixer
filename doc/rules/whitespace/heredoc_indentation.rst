============================
Rule ``heredoc_indentation``
============================

Heredoc/nowdoc content must be properly indented.

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

- `@PHP7.3Migration <./../../ruleSets/PHP7.3Migration.rst>`_
- `@PHP7.4Migration <./../../ruleSets/PHP7.4Migration.rst>`_
- `@PHP8.0Migration <./../../ruleSets/PHP8.0Migration.rst>`_
- `@PHP8.1Migration <./../../ruleSets/PHP8.1Migration.rst>`_
- `@PHP8.2Migration <./../../ruleSets/PHP8.2Migration.rst>`_
- `@PHP8.3Migration <./../../ruleSets/PHP8.3Migration.rst>`_
- `@PHP8.4Migration <./../../ruleSets/PHP8.4Migration.rst>`_
- `@PHP8.5Migration <./../../ruleSets/PHP8.5Migration.rst>`_
- `@PHP73Migration <./../../ruleSets/PHP73Migration.rst>`_
- `@PHP74Migration <./../../ruleSets/PHP74Migration.rst>`_
- `@PHP80Migration <./../../ruleSets/PHP80Migration.rst>`_
- `@PHP81Migration <./../../ruleSets/PHP81Migration.rst>`_
- `@PHP82Migration <./../../ruleSets/PHP82Migration.rst>`_
- `@PHP83Migration <./../../ruleSets/PHP83Migration.rst>`_
- `@PHP84Migration <./../../ruleSets/PHP84Migration.rst>`_
- `@PHP85Migration <./../../ruleSets/PHP85Migration.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Whitespace\\HeredocIndentationFixer <./../../../src/Fixer/Whitespace/HeredocIndentationFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Whitespace\\HeredocIndentationFixerTest <./../../../tests/Fixer/Whitespace/HeredocIndentationFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
