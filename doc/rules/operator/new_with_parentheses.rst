=============================
Rule ``new_with_parentheses``
=============================

All instances created with ``new`` keyword must (not) be followed by
parentheses.

Warning
-------

This rule is CONFIGURABLE
~~~~~~~~~~~~~~~~~~~~~~~~~

You can configure this rule using the following options: ``anonymous_class``,
``named_class``.

Configuration
-------------

``anonymous_class``
~~~~~~~~~~~~~~~~~~~

Whether anonymous classes should be followed by parentheses.

Allowed types: ``bool``

Default value: ``true``

Default value (future-mode): ``false``

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

- `@PER <./../../ruleSets/PER.rst>`_ *(deprecated)* with config:

  ``['anonymous_class' => false]``

- `@PER-CS <./../../ruleSets/PER-CS.rst>`_ with config:

  ``['anonymous_class' => false]``

- `@PER-CS1.0 <./../../ruleSets/PER-CS1.0.rst>`_ *(deprecated)* with config:

  ``['anonymous_class' => true]``

- `@PER-CS1x0 <./../../ruleSets/PER-CS1x0.rst>`_ with config:

  ``['anonymous_class' => true]``

- `@PER-CS2.0 <./../../ruleSets/PER-CS2.0.rst>`_ *(deprecated)* with config:

  ``['anonymous_class' => false]``

- `@PER-CS2x0 <./../../ruleSets/PER-CS2x0.rst>`_ with config:

  ``['anonymous_class' => false]``

- `@PER-CS3.0 <./../../ruleSets/PER-CS3.0.rst>`_ *(deprecated)* with config:

  ``['anonymous_class' => false]``

- `@PER-CS3x0 <./../../ruleSets/PER-CS3x0.rst>`_ with config:

  ``['anonymous_class' => false]``

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ with config:

  ``['anonymous_class' => false]``

- `@PSR12 <./../../ruleSets/PSR12.rst>`_ with config:

  ``['anonymous_class' => true]``

- `@Symfony <./../../ruleSets/Symfony.rst>`_ with config:

  ``['anonymous_class' => false]``

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Operator\\NewWithParenthesesFixer <./../../../src/Fixer/Operator/NewWithParenthesesFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Operator\\NewWithParenthesesFixerTest <./../../../tests/Fixer/Operator/NewWithParenthesesFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
