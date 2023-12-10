=====================
Rule ``concat_space``
=====================

Concatenation should be spaced according to configuration.

Configuration
-------------

``spacing``
~~~~~~~~~~~

Spacing to apply around concatenation operator.

Allowed values: ``'none'`` and ``'one'``

Default value: ``'none'``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$foo = 'bar' . 3 . 'baz'.'qux';
   +$foo = 'bar'. 3 .'baz'.'qux';

Example #2
~~~~~~~~~~

With configuration: ``['spacing' => 'none']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$foo = 'bar' . 3 . 'baz'.'qux';
   +$foo = 'bar'. 3 .'baz'.'qux';

Example #3
~~~~~~~~~~

With configuration: ``['spacing' => 'one']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$foo = 'bar' . 3 . 'baz'.'qux';
   +$foo = 'bar' . 3 . 'baz' . 'qux';

Rule sets
---------

The rule is part of the following rule sets:

- `@PER <./../../ruleSets/PER.rst>`_ with config:

  ``['spacing' => 'one']``

- `@PER-CS <./../../ruleSets/PER-CS.rst>`_ with config:

  ``['spacing' => 'one']``

- `@PER-CS2.0 <./../../ruleSets/PER-CS2.0.rst>`_ with config:

  ``['spacing' => 'one']``

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

Source class
------------

`PhpCsFixer\\Fixer\\Operator\\ConcatSpaceFixer <./../../../src/Fixer/Operator/ConcatSpaceFixer.php>`_
