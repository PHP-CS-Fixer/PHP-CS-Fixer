=====================
Rule ``phpdoc_types``
=====================

The correct case must be used for standard PHP types in PHPDoc.

Configuration
-------------

``groups``
~~~~~~~~~~

Type groups to fix.

Allowed values: a subset of ``['alias', 'meta', 'simple']``

Default value: ``['simple', 'alias', 'meta']``

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

Rule sets
---------

The rule is part of the following rule sets:

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``phpdoc_types`` rule with the default config.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``phpdoc_types`` rule with the default config.
