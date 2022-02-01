=======================
Rule ``header_comment``
=======================

Add, replace or remove header comment.

Configuration
-------------

``header``
~~~~~~~~~~

Proper header content.

Allowed types: ``string``

This option is required.

``comment_type``
~~~~~~~~~~~~~~~~

Comment syntax type.

Allowed values: ``'comment'``, ``'PHPDoc'``

Default value: ``'comment'``

``location``
~~~~~~~~~~~~

The location of the inserted header.

Allowed values: ``'after_declare_strict'``, ``'after_open'``

Default value: ``'after_declare_strict'``

``separate``
~~~~~~~~~~~~

Whether the header should be separated from the file content with a new line.

Allowed values: ``'both'``, ``'bottom'``, ``'none'``, ``'top'``

Default value: ``'both'``

Examples
--------

Example #1
~~~~~~~~~~

With configuration: ``['header' => 'Made with love.']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    declare(strict_types=1);

   +/*
   + * Made with love.
   + */
   +
    namespace A\B;

    echo 1;

Example #2
~~~~~~~~~~

With configuration: ``['header' => 'Made with love.', 'comment_type' => 'PHPDoc', 'location' => 'after_open', 'separate' => 'bottom']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   +/**
   + * Made with love.
   + */
   +
    declare(strict_types=1);

    namespace A\B;

    echo 1;

Example #3
~~~~~~~~~~

With configuration: ``['header' => 'Made with love.', 'comment_type' => 'comment', 'location' => 'after_declare_strict']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    declare(strict_types=1);

   +/*
   + * Made with love.
   + */
   +
    namespace A\B;

    echo 1;

Example #4
~~~~~~~~~~

With configuration: ``['header' => '']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    declare(strict_types=1);

   -/*
   - * Comment is not wanted here.
   - */
   -
    namespace A\B;

    echo 1;
