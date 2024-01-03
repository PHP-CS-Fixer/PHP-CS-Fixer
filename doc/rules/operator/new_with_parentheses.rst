=============================
Rule ``new_with_parentheses``
=============================

All instances created with ``new`` keyword must (not) be followed by
parentheses.

Configuration
-------------

``anonymous_class``
~~~~~~~~~~~~~~~~~~~

Whether anonymous classes should be followed by parentheses.

Allowed types: ``bool``

Default value: ``true``

``named_class``
~~~~~~~~~~~~~~~

Whether named classes should be followed by parentheses.

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

   -$x = new X;
   -$y = new class {};
   +$x = new X();
   +$y = new class() {};

Example #2
~~~~~~~~~~

With configuration: ``['anonymous_class' => false]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php

   -$y = new class() {};
   +$y = new class {};

Example #3
~~~~~~~~~~

With configuration: ``['named_class' => false]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php

   -$x = new X();
   +$x = new X;

Rule sets
---------

The rule is part of the following rule sets:

- `@PER <./../../ruleSets/PER.rst>`_
- `@PER-CS <./../../ruleSets/PER-CS.rst>`_
- `@PER-CS1.0 <./../../ruleSets/PER-CS1.0.rst>`_
- `@PER-CS2.0 <./../../ruleSets/PER-CS2.0.rst>`_
- `@PSR12 <./../../ruleSets/PSR12.rst>`_
- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Operator\\NewWithParenthesesFixer <./../../../src/Fixer/Operator/NewWithParenthesesFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Operator\\NewWithParenthesesFixerTest <./../../../tests/Fixer/Operator/NewWithParenthesesFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
