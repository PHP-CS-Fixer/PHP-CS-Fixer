=====================
Rule ``phpdoc_order``
=====================

Annotations in PHPDoc should be ordered in defined sequence.

Warning
-------

This rule is CONFIGURABLE
~~~~~~~~~~~~~~~~~~~~~~~~~

You can configure this rule using the following option: ``order``.

Configuration
-------------

``order``
~~~~~~~~~

Sequence in which annotations in PHPDoc should be ordered.

Allowed types: ``list<string>``

Default value: ``['param', 'throws', 'return']``

Default value (future-mode): ``['param', 'return', 'throws']``

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

Example #3
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

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Phpdoc\\PhpdocOrderFixer <./../../../src/Fixer/Phpdoc/PhpdocOrderFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Phpdoc\\PhpdocOrderFixerTest <./../../../tests/Fixer/Phpdoc/PhpdocOrderFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
