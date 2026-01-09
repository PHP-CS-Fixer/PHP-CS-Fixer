===================
Rule ``yoda_style``
===================

Write conditions in Yoda style (``true``), non-Yoda style (``['equal' => false,
'identical' => false, 'less_and_greater' => false]``) or ignore those conditions
(``null``) based on configuration.

Warning
-------

This rule is CONFIGURABLE
~~~~~~~~~~~~~~~~~~~~~~~~~

You can configure this rule using the following options:
``always_move_variable``, ``equal``, ``identical``, ``less_and_greater``.

Configuration
-------------

``always_move_variable``
~~~~~~~~~~~~~~~~~~~~~~~~

Whether variables should always be on non assignable side when applying Yoda
style.

Allowed types: ``bool``

Default value: ``false``

``equal``
~~~~~~~~~

Style for equal (``==``, ``!=``) statements.

Allowed types: ``bool`` and ``null``

Default value: ``true``

``identical``
~~~~~~~~~~~~~

Style for identical (``===``, ``!==``) statements.

Allowed types: ``bool`` and ``null``

Default value: ``true``

``less_and_greater``
~~~~~~~~~~~~~~~~~~~~

Style for less and greater than (``<``, ``<=``, ``>``, ``>=``) statements.

Allowed types: ``bool`` and ``null``

Default value: ``null``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -    if ($a === null) {
   +    if (null === $a) {
            echo "null";
        }

Example #2
~~~~~~~~~~

With configuration: ``['equal' => true, 'identical' => false, 'less_and_greater' => null]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -    $b = $c != 1;  // equal
   -    $a = 1 === $b; // identical
   +    $b = 1 != $c;  // equal
   +    $a = $b === 1; // identical
        $c = $c > 3;   // less than

Example #3
~~~~~~~~~~

With configuration: ``['always_move_variable' => true]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -return $foo === count($bar);
   +return count($bar) === $foo;

Example #4
~~~~~~~~~~

With configuration: ``['equal' => false, 'identical' => false, 'less_and_greater' => false]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
        // Enforce non-Yoda style.
   -    if (null === $a) {
   +    if ($a === null) {
            echo "null";
        }

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\ControlStructure\\YodaStyleFixer <./../../../src/Fixer/ControlStructure/YodaStyleFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\ControlStructure\\YodaStyleFixerTest <./../../../tests/Fixer/ControlStructure/YodaStyleFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
