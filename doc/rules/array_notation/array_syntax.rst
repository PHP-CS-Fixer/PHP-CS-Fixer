=====================
Rule ``array_syntax``
=====================

PHP arrays should be declared using the configured syntax.

Configuration
-------------

``syntax``
~~~~~~~~~~

Whether to use the ``long`` or ``short`` array syntax.

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
   -array(1,2);
   +[1,2];

Example #2
~~~~~~~~~~

With configuration: ``['syntax' => 'long']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -[1,2];
   +array(1,2);

Rule sets
---------

The rule is part of the following rule sets:

- `@PHP54Migration <./../../ruleSets/PHP54Migration.rst>`_
- `@PHP70Migration <./../../ruleSets/PHP70Migration.rst>`_
- `@PHP71Migration <./../../ruleSets/PHP71Migration.rst>`_
- `@PHP73Migration <./../../ruleSets/PHP73Migration.rst>`_
- `@PHP74Migration <./../../ruleSets/PHP74Migration.rst>`_
- `@PHP80Migration <./../../ruleSets/PHP80Migration.rst>`_
- `@PHP81Migration <./../../ruleSets/PHP81Migration.rst>`_
- `@PHP82Migration <./../../ruleSets/PHP82Migration.rst>`_
- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

Source class
------------

`PhpCsFixer\\Fixer\\ArrayNotation\\ArraySyntaxFixer <./../src/Fixer/ArrayNotation/ArraySyntaxFixer.php>`_
