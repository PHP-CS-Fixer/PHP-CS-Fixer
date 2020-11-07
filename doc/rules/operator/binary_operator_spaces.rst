===============================
Rule ``binary_operator_spaces``
===============================

Binary operators should be surrounded by space as configured.

Configuration
-------------

``default``
~~~~~~~~~~~

Default fix strategy.

Allowed values: ``'align'``, ``'align_single_space'``, ``'align_single_space_minimal'``, ``'no_space'``, ``'single_space'``, ``null``

Default value: ``'single_space'``

``operators``
~~~~~~~~~~~~~

Dictionary of ``binary operator`` => ``fix strategy`` values that differ from
the default strategy.

Allowed types: ``array``

Default value: ``[]``

``align_double_arrow``
~~~~~~~~~~~~~~~~~~~~~~

.. warning:: This option is deprecated and will be removed on next major version. Use options ``operators`` and ``default`` instead.

Whether to apply, remove or ignore double arrows alignment.

Allowed values: ``false``, ``null``, ``true``

Default value: ``false``

``align_equals``
~~~~~~~~~~~~~~~~

.. warning:: This option is deprecated and will be removed on next major version. Use options ``operators`` and ``default`` instead.

Whether to apply, remove or ignore equals alignment.

Allowed values: ``false``, ``null``, ``true``

Default value: ``false``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,2 +1,2 @@
    <?php
   -$a= 1  + $b^ $d !==  $e or   $f;
   +$a = 1 + $b ^ $d !== $e or $f;

Example #2
~~~~~~~~~~

With configuration: ``['operators' => ['=' => 'align', 'xor' => null]]``.

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,6 +1,6 @@
    <?php
    $aa=  1;
   -$b=2;
   +$b =2;

    $c = $d    xor    $e;
   -$f    -=  1;
   +$f -= 1;

Example #3
~~~~~~~~~~

With configuration: ``['operators' => ['+=' => 'align_single_space']]``.

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,6 +1,6 @@
    <?php
   -$a = $b +=$c;
   -$d = $ee+=$f;
   +$a = $b  += $c;
   +$d = $ee += $f;

   -$g = $b     +=$c;
   -$h = $ee+=$f;
   +$g = $b     += $c;
   +$h = $ee    += $f;

Example #4
~~~~~~~~~~

With configuration: ``['operators' => ['===' => 'align_single_space_minimal']]``.

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,4 +1,4 @@
    <?php
   -$a = $b===$c;
   -$d = $f   ===  $g;
   -$h = $i===  $j;
   +$a = $b === $c;
   +$d = $f === $g;
   +$h = $i === $j;

Example #5
~~~~~~~~~~

With configuration: ``['operators' => ['|' => 'no_space']]``.

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,2 +1,2 @@
    <?php
   -$foo = \json_encode($bar, JSON_PRESERVE_ZERO_FRACTION | JSON_PRETTY_PRINT);
   +$foo = \json_encode($bar, JSON_PRESERVE_ZERO_FRACTION|JSON_PRETTY_PRINT);

Rule sets
---------

The rule is part of the following rule sets:

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``binary_operator_spaces`` rule with the default config.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``binary_operator_spaces`` rule with the default config.
