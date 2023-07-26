============================
Rule ``phpdoc_no_alias_tag``
============================

No alias PHPDoc tags should be used.

Configuration
-------------

``replacements``
~~~~~~~~~~~~~~~~

Mapping between replaced annotations with new ones.

Allowed types: ``array``

Default value: ``['property-read' => 'property', 'property-write' => 'property', 'type' => 'var', 'link' => 'see']``

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
     * @property string $foo
   - * @property-read string $bar
   + * @property string $bar
     *
   - * @link baz
   + * @see baz
     */
    final class Example
    {
    }

Example #2
~~~~~~~~~~

With configuration: ``['replacements' => ['link' => 'website']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    /**
     * @property string $foo
     * @property-read string $bar
     *
   - * @link baz
   + * @website baz
     */
    final class Example
    {
    }

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

