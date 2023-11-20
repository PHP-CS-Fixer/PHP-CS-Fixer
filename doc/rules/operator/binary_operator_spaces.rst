===============================
Rule ``binary_operator_spaces``
===============================

Binary operators should be surrounded by space as configured.

Configuration
-------------

``default``
~~~~~~~~~~~

Default fix strategy.

Allowed values: ``'align'``, ``'align_by_scope'``, ``'align_single_space'``, ``'align_single_space_by_scope'``, ``'align_single_space_minimal'``, ``'align_single_space_minimal_by_scope'``, ``'at_least_single_space'``, ``'no_space'``, ``'single_space'`` and ``null``

Default value: ``'single_space'``

``operators``
~~~~~~~~~~~~~

Dictionary of ``binary operator`` => ``fix strategy`` values that differ from
the default strategy. Supported are: ``=``, ``*``, ``/``, ``%``, ``<``, ``>``,
``|``, ``^``, ``+``, ``-``, ``&``, ``&=``, ``&&``, ``||``, ``.=``, ``/=``,
``=>``, ``==``, ``>=``, ``===``, ``!=``, ``<>``, ``!==``, ``<=``, ``and``,
``or``, ``xor``, ``-=``, ``%=``, ``*=``, ``|=``, ``+=``, ``<<``, ``<<=``,
``>>``, ``>>=``, ``^=``, ``**``, ``**=``, ``<=>``, ``??`` and ``??=``.

Allowed types: ``array``

Default value: ``[]``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$a= 1  + $b^ $d !==  $e or   $f;
   +$a = 1 + $b ^ $d !== $e or $f;

Example #2
~~~~~~~~~~

With configuration: ``['operators' => ['=' => 'align', 'xor' => null]]``.

.. code-block:: diff

   --- Original
   +++ New
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
    <?php
   -$foo = \json_encode($bar, JSON_PRESERVE_ZERO_FRACTION | JSON_PRETTY_PRINT);
   +$foo = \json_encode($bar, JSON_PRESERVE_ZERO_FRACTION|JSON_PRETTY_PRINT);

Example #6
~~~~~~~~~~

With configuration: ``['operators' => ['=>' => 'single_space']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    $array = [
   -    "foo"            =>   1,
   -    "baaaaaaaaaaar"  =>  11,
   +    "foo" => 1,
   +    "baaaaaaaaaaar" => 11,
    ];

Example #7
~~~~~~~~~~

With configuration: ``['operators' => ['=>' => 'align']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    $array = [
   -    "foo" => 12,
   +    "foo"            => 12,
        "baaaaaaaaaaar"  => 13,

        "baz" => 1,
    ];

Example #8
~~~~~~~~~~

With configuration: ``['operators' => ['=>' => 'align_by_scope']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    $array = [
   -    "foo" => 12,
   +    "foo"            => 12,
        "baaaaaaaaaaar"  => 13,

   -    "baz" => 1,
   +    "baz"            => 1,
    ];

Example #9
~~~~~~~~~~

With configuration: ``['operators' => ['=>' => 'align_single_space']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    $array = [
   -    "foo" => 12,
   +    "foo"            => 12,
        "baaaaaaaaaaar"  => 13,

        "baz" => 1,
    ];

Example #10
~~~~~~~~~~~

With configuration: ``['operators' => ['=>' => 'align_single_space_by_scope']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    $array = [
   -    "foo" => 12,
   +    "foo"            => 12,
        "baaaaaaaaaaar"  => 13,

   -    "baz" => 1,
   +    "baz"            => 1,
    ];

Example #11
~~~~~~~~~~~

With configuration: ``['operators' => ['=>' => 'align_single_space_minimal']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    $array = [
   -    "foo" => 12,
   -    "baaaaaaaaaaar"  => 13,
   +    "foo"           => 12,
   +    "baaaaaaaaaaar" => 13,

        "baz" => 1,
    ];

Example #12
~~~~~~~~~~~

With configuration: ``['operators' => ['=>' => 'align_single_space_minimal_by_scope']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    $array = [
   -    "foo" => 12,
   -    "baaaaaaaaaaar"  => 13,
   +    "foo"           => 12,
   +    "baaaaaaaaaaar" => 13,

   -    "baz" => 1,
   +    "baz"           => 1,
    ];

Rule sets
---------

The rule is part of the following rule sets:

- `@PER <./../../ruleSets/PER.rst>`_ with config:

  ``['default' => 'at_least_single_space']``

- `@PER-CS <./../../ruleSets/PER-CS.rst>`_ with config:

  ``['default' => 'at_least_single_space']``

- `@PER-CS1.0 <./../../ruleSets/PER-CS1.0.rst>`_ with config:

  ``['default' => 'at_least_single_space']``

- `@PER-CS2.0 <./../../ruleSets/PER-CS2.0.rst>`_ with config:

  ``['default' => 'at_least_single_space']``

- `@PSR12 <./../../ruleSets/PSR12.rst>`_ with config:

  ``['default' => 'at_least_single_space']``

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

Source class
------------

`PhpCsFixer\\Fixer\\Operator\\BinaryOperatorSpacesFixer <./../src/Fixer/Operator/BinaryOperatorSpacesFixer.php>`_
