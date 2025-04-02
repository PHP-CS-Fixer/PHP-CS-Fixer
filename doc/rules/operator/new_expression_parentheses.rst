===================================
Rule ``new_expression_parentheses``
===================================

All ``new`` expressions with a further call must (not) be wrapped in
parentheses.

Configuration
-------------

``use_parentheses``
~~~~~~~~~~~~~~~~~~~

Whether ``new`` expressions with a further call should be wrapped in parentheses
or not.

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

   -(new Foo())->bar();
   +new Foo()->bar();

Example #2
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php

   -(new class {})->bar();
   +new class {}->bar();

Example #3
~~~~~~~~~~

With configuration: ``['use_parentheses' => true]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php

   -new Foo()->bar();
   +(new Foo())->bar();

Example #4
~~~~~~~~~~

With configuration: ``['use_parentheses' => true]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php

   -new class {}->bar();
   +(new class {})->bar();
References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Operator\\NewExpressionParenthesesFixer <./../../../src/Fixer/Operator/NewExpressionParenthesesFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Operator\\NewExpressionParenthesesFixerTest <./../../../tests/Fixer/Operator/NewExpressionParenthesesFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
