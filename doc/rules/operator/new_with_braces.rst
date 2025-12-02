========================
Rule ``new_with_braces``
========================

All instances created with ``new`` keyword must (not) be followed by braces.

Warnings
--------

This rule is DEPRECATED and will be removed in the next major version 4.0
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

You should use ``new_with_parentheses`` instead.

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

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Operator\\NewWithBracesFixer <./../../../src/Fixer/Operator/NewWithBracesFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Operator\\NewWithBracesFixerTest <./../../../tests/Fixer/Operator/NewWithBracesFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
