===================================
Rule ``no_useless_concat_operator``
===================================

There should not be useless concat operations.

Warning
-------

This rule is CONFIGURABLE
~~~~~~~~~~~~~~~~~~~~~~~~~

You can configure this rule using the following option:
``juggle_simple_strings``.

Configuration
-------------

``juggle_simple_strings``
~~~~~~~~~~~~~~~~~~~~~~~~~

Allow for simple string quote juggling if it results in more concat-operations
merges.

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
   -$a = 'a'.'b';
   +$a = 'ab';

Example #2
~~~~~~~~~~

With configuration: ``['juggle_simple_strings' => true]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$a = 'a'."b";
   +$a = "ab";

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Operator\\NoUselessConcatOperatorFixer <./../../../src/Fixer/Operator/NoUselessConcatOperatorFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Operator\\NoUselessConcatOperatorFixerTest <./../../../tests/Fixer/Operator/NoUselessConcatOperatorFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
