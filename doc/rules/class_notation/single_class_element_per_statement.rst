===========================================
Rule ``single_class_element_per_statement``
===========================================

There MUST NOT be more than one property or constant declared per statement.

Configuration
-------------

``elements``
~~~~~~~~~~~~

List of strings which element should be modified.

Allowed values: a subset of ``['const', 'property']``

Default value: ``['const', 'property']``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    final class Example
    {
   -    const FOO_1 = 1, FOO_2 = 2;
   -    private static $bar1 = array(1,2,3), $bar2 = [1,2,3];
   +    const FOO_1 = 1;
   +    const FOO_2 = 2;
   +    private static $bar1 = array(1,2,3);
   +    private static $bar2 = [1,2,3];
    }

Example #2
~~~~~~~~~~

With configuration: ``['elements' => ['property']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    final class Example
    {
        const FOO_1 = 1, FOO_2 = 2;
   -    private static $bar1 = array(1,2,3), $bar2 = [1,2,3];
   +    private static $bar1 = array(1,2,3);
   +    private static $bar2 = [1,2,3];
    }

Rule sets
---------

The rule is part of the following rule sets:

- `@PER <./../../ruleSets/PER.rst>`_ *(deprecated)*
- `@PER-CS <./../../ruleSets/PER-CS.rst>`_
- `@PER-CS1.0 <./../../ruleSets/PER-CS1.0.rst>`_ with config:

  ``['elements' => ['property']]``

- `@PER-CS1x0 <./../../ruleSets/PER-CS1x0.rst>`_ with config:

  ``['elements' => ['property']]``

- `@PER-CS2.0 <./../../ruleSets/PER-CS2.0.rst>`_ with config:

  ``['elements' => ['property']]``

- `@PER-CS2x0 <./../../ruleSets/PER-CS2x0.rst>`_ with config:

  ``['elements' => ['property']]``

- `@PER-CS3.0 <./../../ruleSets/PER-CS3.0.rst>`_
- `@PER-CS3x0 <./../../ruleSets/PER-CS3x0.rst>`_
- `@PSR2 <./../../ruleSets/PSR2.rst>`_ with config:

  ``['elements' => ['property']]``

- `@PSR12 <./../../ruleSets/PSR12.rst>`_ with config:

  ``['elements' => ['property']]``

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\ClassNotation\\SingleClassElementPerStatementFixer <./../../../src/Fixer/ClassNotation/SingleClassElementPerStatementFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\ClassNotation\\SingleClassElementPerStatementFixerTest <./../../../tests/Fixer/ClassNotation/SingleClassElementPerStatementFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
