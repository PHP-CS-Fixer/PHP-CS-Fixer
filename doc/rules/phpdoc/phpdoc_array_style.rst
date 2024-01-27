===========================
Rule ``phpdoc_array_style``
===========================

PHPDoc list types must be written in configured style.

Configuration
-------------

``strategy``
~~~~~~~~~~~~

Which part of the conversion - brackets (``[]``) to ``array`` to ``list`` - to
perform.

Allowed values: ``'array_to_list'``, ``'brackets_to_array'`` and ``'brackets_to_array_to_list'``

Default value: ``'brackets_to_array'``

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
   - * @param bool[] $a
   + * @param array<bool> $a
     * @param array<int> $b
     * @param list<int> $c
     * @param array<string, int> $d
     */

Example #2
~~~~~~~~~~

With configuration: ``['strategy' => 'array_to_list']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    /**
     * @param bool[] $a
   - * @param array<int> $b
   + * @param list<int> $b
     * @param list<int> $c
     * @param array<string, int> $d
     */

Example #3
~~~~~~~~~~

With configuration: ``['strategy' => 'brackets_to_array_to_list']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    /**
   - * @param bool[] $a
   - * @param array<int> $b
   + * @param list<bool> $a
   + * @param list<int> $b
     * @param list<int> $c
     * @param array<string, int> $d
     */
References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Phpdoc\\PhpdocArrayStyleFixer <./../../../src/Fixer/Phpdoc/PhpdocArrayStyleFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Phpdoc\\PhpdocArrayStyleFixerTest <./../../../tests/Fixer/Phpdoc/PhpdocArrayStyleFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
