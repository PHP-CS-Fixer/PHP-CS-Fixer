============================================
Rule ``negated_instanceof_with_parentheses``
============================================

Negated ``instanceof`` expressions must (not) be wrapped in parentheses.

Warning
-------

This rule is CONFIGURABLE
~~~~~~~~~~~~~~~~~~~~~~~~~

You can configure this rule using the following option: ``use_parentheses``.

Configuration
-------------

``use_parentheses``
~~~~~~~~~~~~~~~~~~~

Whether negated ``instanceof`` expressions should be wrapped in parentheses or
not.

Allowed types: ``bool``

Default value: ``true``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -!$foo instanceof Foo;
   +!($foo instanceof Foo);

Example #2
~~~~~~~~~~

With configuration: ``['use_parentheses' => false]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -!($foo instanceof Foo);
   +!$foo instanceof Foo;

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Operator\\NegatedInstanceofWithParenthesesFixer <./../../../src/Fixer/Operator/NegatedInstanceofWithParenthesesFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Operator\\NegatedInstanceofWithParenthesesFixerTest <./../../../tests/Fixer/Operator/NegatedInstanceofWithParenthesesFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
