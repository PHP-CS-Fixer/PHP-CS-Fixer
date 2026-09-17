=====================
Rule ``phpdoc_types``
=====================

The correct case must be used for standard PHP types in PHPDoc.

Warning
-------

This rule is CONFIGURABLE
~~~~~~~~~~~~~~~~~~~~~~~~~

You can configure this rule using the following options: ``exclude``,
``groups``.

Configuration
-------------

``exclude``
~~~~~~~~~~~

List of types to exclude from fixing, regardless of groups.

Allowed values: a subset of ``['$this', 'array', 'bool', 'boolean', 'callable', 'double', 'false', 'float', 'int', 'integer', 'iterable', 'mixed', 'null', 'object', 'parent', 'resource', 'scalar', 'self', 'static', 'string', 'true', 'void']``

Default value: ``[]``

``groups``
~~~~~~~~~~

Type groups to fix.

Allowed values: a subset of ``['alias', 'meta', 'simple']``

Default value: ``['alias', 'meta', 'simple']``

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
   - * @param STRING|String[] $bar
   + * @param string|string[] $bar
     *
   - * @return inT[]
   + * @return int[]
     */

Example #2
~~~~~~~~~~

With configuration: ``['groups' => ['simple', 'alias']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    /**
   - * @param BOOL $foo
   + * @param bool $foo
     *
     * @return MIXED
     */

Example #3
~~~~~~~~~~

With configuration: ``['exclude' => ['resource']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    /**
     * @param Resource $foo
     *
   - * @return VOID
   + * @return void
     */

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Phpdoc\\PhpdocTypesFixer <./../../../src/Fixer/Phpdoc/PhpdocTypesFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Phpdoc\\PhpdocTypesFixerTest <./../../../tests/Fixer/Phpdoc/PhpdocTypesFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
