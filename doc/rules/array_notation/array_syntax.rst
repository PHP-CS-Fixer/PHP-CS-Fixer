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

- `@PER <./../../ruleSets/PER.rst>`_
- `@PER-CS <./../../ruleSets/PER-CS.rst>`_
- `@PER-CS2.0 <./../../ruleSets/PER-CS2.0.rst>`_
- `@PER-CS3.0 <./../../ruleSets/PER-CS3.0.rst>`_
- `@PHP5.4Migration <./../../ruleSets/PHP5.4Migration.rst>`_
- `@PHP7.0Migration <./../../ruleSets/PHP7.0Migration.rst>`_
- `@PHP7.1Migration <./../../ruleSets/PHP7.1Migration.rst>`_
- `@PHP7.3Migration <./../../ruleSets/PHP7.3Migration.rst>`_
- `@PHP7.4Migration <./../../ruleSets/PHP7.4Migration.rst>`_
- `@PHP8.0Migration <./../../ruleSets/PHP8.0Migration.rst>`_
- `@PHP8.1Migration <./../../ruleSets/PHP8.1Migration.rst>`_
- `@PHP8.2Migration <./../../ruleSets/PHP8.2Migration.rst>`_
- `@PHP8.3Migration <./../../ruleSets/PHP8.3Migration.rst>`_
- `@PHP8.4Migration <./../../ruleSets/PHP8.4Migration.rst>`_
- `@PHP8.5Migration <./../../ruleSets/PHP8.5Migration.rst>`_
- `@PHP54Migration <./../../ruleSets/PHP54Migration.rst>`_
- `@PHP70Migration <./../../ruleSets/PHP70Migration.rst>`_
- `@PHP71Migration <./../../ruleSets/PHP71Migration.rst>`_
- `@PHP73Migration <./../../ruleSets/PHP73Migration.rst>`_
- `@PHP74Migration <./../../ruleSets/PHP74Migration.rst>`_
- `@PHP80Migration <./../../ruleSets/PHP80Migration.rst>`_
- `@PHP81Migration <./../../ruleSets/PHP81Migration.rst>`_
- `@PHP82Migration <./../../ruleSets/PHP82Migration.rst>`_
- `@PHP83Migration <./../../ruleSets/PHP83Migration.rst>`_
- `@PHP84Migration <./../../ruleSets/PHP84Migration.rst>`_
- `@PHP85Migration <./../../ruleSets/PHP85Migration.rst>`_
- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\ArrayNotation\\ArraySyntaxFixer <./../../../src/Fixer/ArrayNotation/ArraySyntaxFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\ArrayNotation\\ArraySyntaxFixerTest <./../../../tests/Fixer/ArrayNotation/ArraySyntaxFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
