=========================
Rule ``phpdoc_list_type``
=========================

PHPDoc list types must be written in configured style.

Configuration
-------------

``style``
~~~~~~~~~

Whether to use ``array`` or ``list`` as type.

Allowed values: ``'array'`` and ``'list'``

Default value: ``'list'``

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
   - * @param array<int> $b
   + * @param list<bool> $a
   + * @param list<int> $b
     * @param array<string, int> $c
     * @param list<int> $d
     */

Example #2
~~~~~~~~~~

With configuration: ``['style' => 'array']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    /**
   - * @param bool[] $a
   + * @param array<bool> $a
     * @param array<int> $b
     * @param array<string, int> $c
   - * @param list<int> $d
   + * @param array<int> $d
     */
References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Phpdoc\\PhpdocListTypeFixer <./../../../src/Fixer/Phpdoc/PhpdocListTypeFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Phpdoc\\PhpdocListTypeFixerTest <./../../../tests/Fixer/Phpdoc/PhpdocListTypeFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
