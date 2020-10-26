===================
Rule ``yoda_style``
===================

Write conditions in Yoda style (``true``), non-Yoda style (``false``) or ignore
those conditions (``null``) based on configuration.

Configuration
-------------

``equal``
~~~~~~~~~

Style for equal (``==``, ``!=``) statements.

Allowed types: ``bool``, ``null``

Default value: ``true``

``identical``
~~~~~~~~~~~~~

Style for identical (``===``, ``!==``) statements.

Allowed types: ``bool``, ``null``

Default value: ``true``

``less_and_greater``
~~~~~~~~~~~~~~~~~~~~

Style for less and greater than (``<``, ``<=``, ``>``, ``>=``) statements.

Allowed types: ``bool``, ``null``

Default value: ``null``

``always_move_variable``
~~~~~~~~~~~~~~~~~~~~~~~~

Whether variables should always be on non assignable side when applying Yoda
style.

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
   @@ -1,4 +1,4 @@
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
   @@ -1,4 +1,4 @@
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
   @@ -1,2 +1,2 @@
    <?php
   -return $foo === count($bar);
   +return count($bar) === $foo;

Rule sets
---------

The rule is part of the following rule sets:

@Symfony
  Using the ``@Symfony`` rule set will enable the ``yoda_style`` rule with the default config.

@PhpCsFixer
  Using the ``@PhpCsFixer`` rule set will enable the ``yoda_style`` rule with the default config.
