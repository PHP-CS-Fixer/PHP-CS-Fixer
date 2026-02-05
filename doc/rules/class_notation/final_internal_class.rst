=============================
Rule ``final_internal_class``
=============================

Internal classes should be ``final``.

Warnings
--------

This rule is RISKY
~~~~~~~~~~~~~~~~~~

Changing classes to ``final`` might cause code execution to break.

This rule is CONFIGURABLE
~~~~~~~~~~~~~~~~~~~~~~~~~

You can configure this rule using the following options: ``annotation_exclude``,
``annotation_include``, ``consider_absent_docblock_as_internal_class``,
``exclude``, ``include``.

Configuration
-------------

``annotation_exclude``
~~~~~~~~~~~~~~~~~~~~~~

.. warning:: This option is deprecated and will be removed in the next major version. Use ``exclude`` to configure PHPDoc annotations tags and attributes.

Class level attribute or annotation tags that must be omitted to fix the class,
even if all of the white list ones are used as well (case insensitive).

Allowed types: ``list<string>``

Default value: ``['@final', '@Entity', '@ORM\\Entity', '@ORM\\Mapping\\Entity', '@Mapping\\Entity', '@Document', '@ODM\\Document']``

``annotation_include``
~~~~~~~~~~~~~~~~~~~~~~

.. warning:: This option is deprecated and will be removed in the next major version. Use ``include`` to configure PHPDoc annotations tags and attributes.

Class level attribute or annotation tags that must be set in order to fix the
class (case insensitive).

Allowed types: ``list<string>``

Default value: ``['@internal']``

``consider_absent_docblock_as_internal_class``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Whether classes without any DocBlock should be fixed to final.

Allowed types: ``bool``

Default value: ``false``

``exclude``
~~~~~~~~~~~

Class level attribute or annotation tags that must be omitted to fix the class,
even if all of the white list ones are used as well (case insensitive).

Allowed types: ``list<string>``

Default value: ``['final', 'Entity', 'ORM\\Entity', 'ORM\\Mapping\\Entity', 'Mapping\\Entity', 'Document', 'ODM\\Document']``

``include``
~~~~~~~~~~~

Class level attribute or annotation tags that must be set in order to fix the
class (case insensitive).

Allowed types: ``list<string>``

Default value: ``['internal']``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    /**
     * @internal
     */
   -class Sample
   +final class Sample
    {
    }

Example #2
~~~~~~~~~~

With configuration: ``['include' => ['@Custom'], 'exclude' => ['@not-fix']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    /**
     * @CUSTOM
     */
   -class A{}
   +final class A{}

    /**
     * @CUSTOM
     * @not-fix
     */
    class B{}

Rule sets
---------

The rule is part of the following rule set:

- `@PhpCsFixer:risky <./../../ruleSets/PhpCsFixerRisky.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\ClassNotation\\FinalInternalClassFixer <./../../../src/Fixer/ClassNotation/FinalInternalClassFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\ClassNotation\\FinalInternalClassFixerTest <./../../../tests/Fixer/ClassNotation/FinalInternalClassFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
