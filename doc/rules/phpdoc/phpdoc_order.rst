=====================
Rule ``phpdoc_order``
=====================

Annotations in PHPDoc should be ordered in specific style.

Description
-----------

Annotations in PHPDoc should be ordered in one of the styles below:

- ``'phpcs'`` style annotations order is ``@param``, ``@throws``, ``@return``,
- ``'symfony'`` style annotations order is ``@param``, ``@return``, ``@throws``.

Configuration
-------------

``style``
~~~~~~~~~

Style in which annotations in PHPDoc should be ordered.

Allowed values: ``'phpcs'``, ``'symfony'``

Default value: ``'phpcs'``

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
   - * @throws Exception|RuntimeException foo
     * @custom Test!
   - * @return int  Return the number of changes.
     * @param string $foo
     * @param bool   $bar Bar
   + * @throws Exception|RuntimeException foo
   + * @return int  Return the number of changes.
     */

Example #2
~~~~~~~~~~

With configuration: ``['style' => 'symfony']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    /**
     * Hello there!
     *
   - * @throws Exception|RuntimeException foo
     * @custom Test!
   - * @return int  Return the number of changes.
     * @param string $foo
     * @param bool   $bar Bar
   + * @return int  Return the number of changes.
   + * @throws Exception|RuntimeException foo
     */

Example #3
~~~~~~~~~~

With configuration: ``['style' => 'phpcs']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    /**
     * Hello there!
     *
   - * @throws Exception|RuntimeException foo
     * @custom Test!
   - * @return int  Return the number of changes.
     * @param string $foo
     * @param bool   $bar Bar
   + * @throws Exception|RuntimeException foo
   + * @return int  Return the number of changes.
     */

Rule sets
---------

The rule is part of the following rule set:

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``phpdoc_order`` rule with the default config.
