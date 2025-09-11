============================
Rule ``phpdoc_no_alias_tag``
============================

No alias PHPDoc tags should be used.

Configuration
-------------

``replacements``
~~~~~~~~~~~~~~~~

Mapping between replaced annotations with new ones.

Allowed types: ``array<string, string>``

Default value: ``['link' => 'see', 'property-read' => 'property', 'property-write' => 'property', 'type' => 'var']``

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

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ with config:

  ``['replacements' => ['const' => 'var', 'link' => 'see', 'property-read' => 'property', 'property-write' => 'property', 'type' => 'var']]``

- `@Symfony <./../../ruleSets/Symfony.rst>`_ with config:

  ``['replacements' => ['const' => 'var', 'link' => 'see', 'property-read' => 'property', 'property-write' => 'property', 'type' => 'var']]``

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Phpdoc\\PhpdocNoAliasTagFixer <./../../../src/Fixer/Phpdoc/PhpdocNoAliasTagFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Phpdoc\\PhpdocNoAliasTagFixerTest <./../../../tests/Fixer/Phpdoc/PhpdocNoAliasTagFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
