=======================================
Rule ``negated_instanceof_parentheses``
=======================================

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

- Fixer class: `PhpCsFixer\\Fixer\\Operator\\NegatedInstanceofParenthesesFixer <./../../../src/Fixer/Operator/NegatedInstanceofParenthesesFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Operator\\NegatedInstanceofParenthesesFixerTest <./../../../tests/Fixer/Operator/NegatedInstanceofParenthesesFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
