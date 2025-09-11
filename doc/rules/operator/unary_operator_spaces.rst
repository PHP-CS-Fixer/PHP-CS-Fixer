==============================
Rule ``unary_operator_spaces``
==============================

Unary operators should be placed adjacent to their operands.

Configuration
-------------

``only_dec_inc``
~~~~~~~~~~~~~~~~

Limit to increment and decrement operators.

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
    <?php
   -$sample ++;
   --- $sample;
   -$sample = ! ! $a;
   -$sample = ~  $c;
   -function & foo(){}
   +$sample++;
   +--$sample;
   +$sample = !!$a;
   +$sample = ~$c;
   +function &foo(){}

Example #2
~~~~~~~~~~

With configuration: ``['only_dec_inc' => false]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -function foo($a, ...   $b) { return (--   $a) * ($b   ++);}
   +function foo($a, ...$b) { return (--$a) * ($b++);}

Example #3
~~~~~~~~~~

With configuration: ``['only_dec_inc' => true]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -function foo($a, ...   $b) { return (--   $a) * ($b   ++);}
   +function foo($a, ...   $b) { return (--$a) * ($b++);}

Rule sets
---------

The rule is part of the following rule sets:

- `@PER <./../../ruleSets/PER.rst>`_ *(deprecated)* with config:

  ``['only_dec_inc' => true]``

- `@PER-CS <./../../ruleSets/PER-CS.rst>`_ with config:

  ``['only_dec_inc' => true]``

- `@PER-CS1.0 <./../../ruleSets/PER-CS1.0.rst>`_ with config:

  ``['only_dec_inc' => true]``

- `@PER-CS2.0 <./../../ruleSets/PER-CS2.0.rst>`_ with config:

  ``['only_dec_inc' => true]``

- `@PER-CS3.0 <./../../ruleSets/PER-CS3.0.rst>`_ with config:

  ``['only_dec_inc' => true]``

- `@PSR12 <./../../ruleSets/PSR12.rst>`_ with config:

  ``['only_dec_inc' => true]``

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Operator\\UnaryOperatorSpacesFixer <./../../../src/Fixer/Operator/UnaryOperatorSpacesFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Operator\\UnaryOperatorSpacesFixerTest <./../../../tests/Fixer/Operator/UnaryOperatorSpacesFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
