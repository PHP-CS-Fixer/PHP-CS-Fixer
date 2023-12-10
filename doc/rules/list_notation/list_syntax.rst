====================
Rule ``list_syntax``
====================

List (``array`` destructuring) assignment should be declared using the
configured syntax. Requires PHP >= 7.1.

Configuration
-------------

``syntax``
~~~~~~~~~~

Whether to use the ``long`` or ``short`` syntax for array destructuring.

Allowed values: ``'long'`` and ``'short'``

Default value: ``'short'``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -list($sample) = $array;
   +[$sample] = $array;

Example #2
~~~~~~~~~~

With configuration: ``['syntax' => 'long']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -[$sample] = $array;
   +list($sample) = $array;

Rule sets
---------

The rule is part of the following rule sets:

- `@PHP71Migration <./../../ruleSets/PHP71Migration.rst>`_
- `@PHP73Migration <./../../ruleSets/PHP73Migration.rst>`_
- `@PHP74Migration <./../../ruleSets/PHP74Migration.rst>`_
- `@PHP80Migration <./../../ruleSets/PHP80Migration.rst>`_
- `@PHP81Migration <./../../ruleSets/PHP81Migration.rst>`_
- `@PHP82Migration <./../../ruleSets/PHP82Migration.rst>`_
- `@PHP83Migration <./../../ruleSets/PHP83Migration.rst>`_

Source class
------------

`PhpCsFixer\\Fixer\\ListNotation\\ListSyntaxFixer <./../../../src/Fixer/ListNotation/ListSyntaxFixer.php>`_
