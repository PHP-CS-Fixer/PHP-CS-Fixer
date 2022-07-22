==========================
Rule ``phpdoc_separation``
==========================

Annotations in PHPDoc should be grouped together so that annotations of the same
type immediately follow each other. Annotations of a different type are
separated by a single blank line, except those specified in
``additional_groups`` option.

Configuration
-------------

``groups``
~~~~~~~~~~

Sets of annotation types to be grouped together.

Allowed types: ``string[][]``

Default value: ``[['deprecated', 'link', 'see', 'since'], ['author', 'copyright', 'license'], ['category', 'package', 'subpackage'], ['property', 'property-read', 'property-write']]``

``additional_groups``
~~~~~~~~~~~~~~~~~~~~~

Sets of additional annotation types to be grouped together.

Allowed types: ``string[][]``

Default value: ``[]``

``psr_standard_tags_only``
~~~~~~~~~~~~~~~~~~~~~~~~~~

Whether to only process annotations defined by PSR-5 draft, which are: ``api``,
``author``, ``category``, ``copyright``, ``deprecated``, ``example``,
``global``, ``internal``, ``license``, ``link``, ``method``, ``package``,
``param``, ``property``, ``property-read``, ``property-write``, ``return``,
``see``, ``since``, ``subpackage``, ``throws``, ``todo``, ``uses``, ``var``,
``version``.

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
    /**
     * Hello there!
     *
     * @author John Doe
     * @custom Test!
     *
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

With configuration: ``['additional_groups' => [['param', 'return']]]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    /**
     * Hello there!
     *
     * @author John Doe
     * @custom Test!
     *
     * @throws Exception|RuntimeException foo
   + *
     * @param string $foo
   - *
     * @param bool   $bar Bar
     * @return int  Return the number of changes.
     */

Example #3
~~~~~~~~~~

With configuration: ``['additional_groups' => [['param', 'return']], 'psr_standard_tags_only' => false]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    /**
     * Hello there!
     *
     * @author John Doe
   + *
     * @custom Test!
     *
     * @throws Exception|RuntimeException foo
   + *
     * @param string $foo
   - *
     * @param bool   $bar Bar
     * @return int  Return the number of changes.
     */

Example #4
~~~~~~~~~~

With configuration: ``['groups' => [['author', 'throws', 'custom'], ['return', 'param']], 'psr_standard_tags_only' => false]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    /**
     * Hello there!
     *
     * @author John Doe
     * @custom Test!
   + * @throws Exception|RuntimeException foo
     *
   - * @throws Exception|RuntimeException foo
     * @param string $foo
   - *
     * @param bool   $bar Bar
     * @return int  Return the number of changes.
     */

Rule sets
---------

The rule is part of the following rule sets:

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``phpdoc_separation`` rule with the default config.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``phpdoc_separation`` rule with the default config.
