===================================
Rule ``new_expression_parentheses``
===================================

All ``new`` expressions must (not) be wrapped in parentheses upon access.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php

   -(new Foo())->bar;
   +new Foo()->bar;

Example #2
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php

   -(new class {})->bar;
   +new class {}->bar;
References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Operator\\NewExpressionParenthesesFixer <./../../../src/Fixer/Operator/NewExpressionParenthesesFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Operator\\NewExpressionParenthesesFixerTest <./../../../tests/Fixer/Operator/NewExpressionParenthesesFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
