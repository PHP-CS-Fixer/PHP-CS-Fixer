=====================
Rule ``array_syntax``
=====================

PHP arrays should be declared using the configured syntax.

Configuration
-------------

``syntax``
~~~~~~~~~~

Whether to use the ``long`` or ``short`` array syntax.

Allowed values: ``'long'`` and ``'short'``

Default value: ``'short'``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -array(1,2);
   +[1,2];

Example #2
~~~~~~~~~~

With configuration: ``['syntax' => 'long']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -[1,2];
   +array(1,2);

Rule sets
---------

The rule is part of the following rule sets:

- `@PER <./../../ruleSets/PER.rst>`_ *(deprecated)*
- `@PER-CS <./../../ruleSets/PER-CS.rst>`_
- `@PER-CS2.0 <./../../ruleSets/PER-CS2.0.rst>`_ *(deprecated)*
- `@PER-CS2x0 <./../../ruleSets/PER-CS2x0.rst>`_
- `@PER-CS3.0 <./../../ruleSets/PER-CS3.0.rst>`_ *(deprecated)*
- `@PER-CS3x0 <./../../ruleSets/PER-CS3x0.rst>`_
- `@PHP5x4Migration <./../../ruleSets/PHP5x4Migration.rst>`_
- `@PHP7x0Migration <./../../ruleSets/PHP7x0Migration.rst>`_
- `@PHP7x1Migration <./../../ruleSets/PHP7x1Migration.rst>`_
- `@PHP7x3Migration <./../../ruleSets/PHP7x3Migration.rst>`_
- `@PHP7x4Migration <./../../ruleSets/PHP7x4Migration.rst>`_
- `@PHP8x0Migration <./../../ruleSets/PHP8x0Migration.rst>`_
- `@PHP8x1Migration <./../../ruleSets/PHP8x1Migration.rst>`_
- `@PHP8x2Migration <./../../ruleSets/PHP8x2Migration.rst>`_
- `@PHP8x3Migration <./../../ruleSets/PHP8x3Migration.rst>`_
- `@PHP8x4Migration <./../../ruleSets/PHP8x4Migration.rst>`_
- `@PHP8x5Migration <./../../ruleSets/PHP8x5Migration.rst>`_
- `@PHP54Migration <./../../ruleSets/PHP54Migration.rst>`_ *(deprecated)*
- `@PHP70Migration <./../../ruleSets/PHP70Migration.rst>`_ *(deprecated)*
- `@PHP71Migration <./../../ruleSets/PHP71Migration.rst>`_ *(deprecated)*
- `@PHP73Migration <./../../ruleSets/PHP73Migration.rst>`_ *(deprecated)*
- `@PHP74Migration <./../../ruleSets/PHP74Migration.rst>`_ *(deprecated)*
- `@PHP80Migration <./../../ruleSets/PHP80Migration.rst>`_ *(deprecated)*
- `@PHP81Migration <./../../ruleSets/PHP81Migration.rst>`_ *(deprecated)*
- `@PHP82Migration <./../../ruleSets/PHP82Migration.rst>`_ *(deprecated)*
- `@PHP83Migration <./../../ruleSets/PHP83Migration.rst>`_ *(deprecated)*
- `@PHP84Migration <./../../ruleSets/PHP84Migration.rst>`_ *(deprecated)*
- `@PHP85Migration <./../../ruleSets/PHP85Migration.rst>`_ *(deprecated)*
- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\ArrayNotation\\ArraySyntaxFixer <./../../../src/Fixer/ArrayNotation/ArraySyntaxFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\ArrayNotation\\ArraySyntaxFixerTest <./../../../tests/Fixer/ArrayNotation/ArraySyntaxFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
