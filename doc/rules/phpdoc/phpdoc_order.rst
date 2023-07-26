=====================
Rule ``phpdoc_order``
=====================

Annotations in PHPDoc should be ordered in defined sequence.

Configuration
-------------

``order``
~~~~~~~~~

Sequence in which annotations in PHPDoc should be ordered.

Allowed types: ``string[]``

Default value: ``['param', 'throws', 'return']``

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

With configuration: ``['order' => ['param', 'throws', 'return']]``.

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

Example #3
~~~~~~~~~~

With configuration: ``['order' => ['param', 'return', 'throws']]``.

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

Example #4
~~~~~~~~~~

With configuration: ``['order' => ['param', 'custom', 'throws', 'return']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    /**
     * Hello there!
     *
   + * @param string $foo
   + * @param bool   $bar Bar
   + * @custom Test!
     * @throws Exception|RuntimeException foo
   - * @custom Test!
     * @return int  Return the number of changes.
   - * @param string $foo
   - * @param bool   $bar Bar
     */

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ with config:

  ``['order' => ['param', 'return', 'throws']]``

- `@Symfony <./../../ruleSets/Symfony.rst>`_ with config:

  ``['order' => ['param', 'return', 'throws']]``


