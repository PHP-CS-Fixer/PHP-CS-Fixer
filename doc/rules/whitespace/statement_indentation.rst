==============================
Rule ``statement_indentation``
==============================

Each statement must be indented.

Warning
-------

This rule is CONFIGURABLE
~~~~~~~~~~~~~~~~~~~~~~~~~

You can configure this rule using the following option:
``stick_comment_to_next_continuous_control_statement``.

Configuration
-------------

``stick_comment_to_next_continuous_control_statement``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Last comment of code block counts as comment for next block.

Allowed types: ``bool``

Default value: ``false``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    if ($baz == true) {
   -  echo "foo";
   +    echo "foo";
    }
    else {
   -      echo "bar";
   +    echo "bar";
    }

Example #2
~~~~~~~~~~

With configuration: ``['stick_comment_to_next_continuous_control_statement' => false]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -        // foo
   +// foo
    if ($foo) {
        echo "foo";
   -        // this is treated as comment of `if` block, as `stick_comment_to_next_continuous_control_statement` is disabled
   +    // this is treated as comment of `if` block, as `stick_comment_to_next_continuous_control_statement` is disabled
    } else {
        $aaa = 1;
    }

Example #3
~~~~~~~~~~

With configuration: ``['stick_comment_to_next_continuous_control_statement' => true]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -        // foo
   +// foo
    if ($foo) {
        echo "foo";
   -        // this is treated as comment of `elseif(1)` block, as `stick_comment_to_next_continuous_control_statement` is enabled
   +// this is treated as comment of `elseif(1)` block, as `stick_comment_to_next_continuous_control_statement` is enabled
    } elseif(1) {
        echo "bar";
    } elseif(2) {
   -        // this is treated as comment of `elseif(2)` block, as the only content of that block
   +    // this is treated as comment of `elseif(2)` block, as the only content of that block
    } elseif(3) {
        $aaa = 1;
   -        // this is treated as comment of `elseif(3)` block, as it is a comment in the final block
   +    // this is treated as comment of `elseif(3)` block, as it is a comment in the final block
    }

Rule sets
---------

The rule is part of the following rule sets:

- `@PER <./../../ruleSets/PER.rst>`_ *(deprecated)*
- `@PER-CS <./../../ruleSets/PER-CS.rst>`_
- `@PER-CS1.0 <./../../ruleSets/PER-CS1.0.rst>`_ *(deprecated)*
- `@PER-CS1x0 <./../../ruleSets/PER-CS1x0.rst>`_
- `@PER-CS2.0 <./../../ruleSets/PER-CS2.0.rst>`_ *(deprecated)*
- `@PER-CS2x0 <./../../ruleSets/PER-CS2x0.rst>`_
- `@PER-CS3.0 <./../../ruleSets/PER-CS3.0.rst>`_ *(deprecated)*
- `@PER-CS3x0 <./../../ruleSets/PER-CS3x0.rst>`_
- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ with config:

  ``['stick_comment_to_next_continuous_control_statement' => true]``

- `@PSR2 <./../../ruleSets/PSR2.rst>`_
- `@PSR12 <./../../ruleSets/PSR12.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_ with config:

  ``['stick_comment_to_next_continuous_control_statement' => true]``

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Whitespace\\StatementIndentationFixer <./../../../src/Fixer/Whitespace/StatementIndentationFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Whitespace\\StatementIndentationFixerTest <./../../../tests/Fixer/Whitespace/StatementIndentationFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
