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

Sets of annotation types to be grouped together. Use ``*`` to match any tag
character.

Allowed types: ``string[][]``

Default value: ``[['deprecated', 'link', 'see', 'since'], ['author', 'copyright', 'license'], ['category', 'package', 'subpackage'], ['property', 'property-read', 'property-write']]``

``skip_unlisted_annotations``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Whether to skip annotations that are not listed in any group.

Allowed types: ``bool``

Default value: ``false``

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
   + *
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

With configuration: ``['groups' => [['deprecated', 'link', 'see', 'since'], ['author', 'copyright', 'license'], ['category', 'package', 'subpackage'], ['property', 'property-read', 'property-write'], ['param', 'return']]]``.

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

Example #3
~~~~~~~~~~

With configuration: ``['groups' => [['author', 'throws', 'custom'], ['return', 'param']]]``.

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

Example #4
~~~~~~~~~~

With configuration: ``['groups' => [['ORM\\*'], ['Assert\\*']]]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    /**
     * @ORM\Id
   + * @ORM\GeneratedValue
     *
   - * @ORM\GeneratedValue
     * @Assert\NotNull
   - *
     * @Assert\Type("string")
     */

Example #5
~~~~~~~~~~

With configuration: ``['skip_unlisted_annotations' => true]``.

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
