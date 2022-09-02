==========================
Rule ``phpdoc_separation``
==========================

Annotations in PHPDoc should be grouped together so that annotations of the same
type immediately follow each other. Annotations of a different type are
separated by a single blank line.

Configuration
-------------

``groups``
~~~~~~~~~~

Sets of annotation types to be grouped together.

Allowed types: ``string[][]``

Default value: ``[['deprecated', 'link', 'see', 'since'], ['author', 'copyright', 'license'], ['category', 'package', 'subpackage'], ['property', 'property-read', 'property-write']]``

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
     * Hello there!
   + *
     * @throws Exception|RuntimeException foo
   + *
     * @param string $foo
   + * @param bool   $bar Bar
     *
   - * @param bool   $bar Bar
     * @return int  Return the number of changes.
     */

Example #2
~~~~~~~~~~

With configuration: ``['groups' => [['requires', 'dataProvider'], ['*param', '*return']]]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    /**
     * @requires PHP 8.1
   + * @dataProvider provideFix81Cases
     *
   - * @dataProvider provideFix81Cases
     * @param string $expected
   - *
     * @psalm-param non-empty-string $expected
   - *
     * @return void
     */

Rule sets
---------

The rule is part of the following rule sets:

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``phpdoc_separation`` rule with the default config.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``phpdoc_separation`` rule with the default config.
