================================
Rule ``declare_equal_normalize``
================================

Equal sign in declare statement should be surrounded by spaces or not following
configuration.

Configuration
-------------

``space``
~~~~~~~~~

Spacing to apply around the equal sign.

Allowed values: ``'none'`` and ``'single'``

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
   -declare(ticks =  1);
   +declare(ticks=1);

Example #2
~~~~~~~~~~

With configuration: ``['space' => 'single']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -declare(ticks=1);
   +declare(ticks = 1);

Rule sets
---------

The rule is part of the following rule sets:

- `@PER <./../../ruleSets/PER.rst>`_
- `@PER-CS1.0 <./../../ruleSets/PER-CS1.0.rst>`_
- `@PER-CS2.0 <./../../ruleSets/PER-CS2.0.rst>`_
- `@PSR12 <./../../ruleSets/PSR12.rst>`_
- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

