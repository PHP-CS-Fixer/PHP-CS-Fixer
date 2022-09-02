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
   - * @throws InvalidArgumentException
     * @dataProvider provideFixCases
   - * @return void
     * @param string $expected The expected value.
     * @param int    $input
   + * @throws InvalidArgumentException
   + * @return void
     */

Example #2
~~~~~~~~~~

With configuration: ``['order' => ['*param', '*return']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    /**
   + * @param string $foo
   + * @param bool   $bar Bar
     * @return int
   - * @param string $foo
     * @psalm-return positive-int
   - * @param bool   $bar Bar
     */

Rule sets
---------

The rule is part of the following rule sets:

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``phpdoc_order`` rule with the config below:

  ``['order' => ['param', 'return', 'throws']]``

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``phpdoc_order`` rule with the config below:

  ``['order' => ['param', 'return', 'throws']]``
