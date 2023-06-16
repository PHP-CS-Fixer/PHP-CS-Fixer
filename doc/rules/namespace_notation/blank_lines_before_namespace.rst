=====================================
Rule ``blank_lines_before_namespace``
=====================================

Controls blank lines before a namespace declaration.

Configuration
-------------

``min_line_breaks``
~~~~~~~~~~~~~~~~~~~

Minimum line breaks that should exist before namespace declaration.

Allowed types: ``int``

Default value: ``2``

``max_line_breaks``
~~~~~~~~~~~~~~~~~~~

Maximum line breaks that should exist before namespace declaration.

Allowed types: ``int``

Default value: ``2``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
   -<?php  namespace A {}
   +<?php
   +
   +namespace A {}

Example #2
~~~~~~~~~~

With configuration: ``['min_line_breaks' => 1]``.

.. code-block:: diff

   --- Original
   +++ New
   -<?php  namespace A {}
   +<?php
   +namespace A {}

Example #3
~~~~~~~~~~

With configuration: ``['max_line_breaks' => 2]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php

    declare(strict_types=1);

   -
   -
    namespace A{}

Example #4
~~~~~~~~~~

With configuration: ``['min_line_breaks' => 2]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php

    /** Some comment */
   +
    namespace A{}

Example #5
~~~~~~~~~~

With configuration: ``['min_line_breaks' => 0, 'max_line_breaks' => 0]``.

.. code-block:: diff

   --- Original
   +++ New
   -<?php
   -
   -namespace A{}
   +<?php namespace A{}

Rule sets
---------

The rule is part of the following rule sets:

@PER
  Using the `@PER <./../../ruleSets/PER.rst>`_ rule set will enable the ``blank_lines_before_namespace`` rule with the default config.

@PER-CS1.0
  Using the `@PER-CS1.0 <./../../ruleSets/PER-CS1.0.rst>`_ rule set will enable the ``blank_lines_before_namespace`` rule with the default config.

@PSR12
  Using the `@PSR12 <./../../ruleSets/PSR12.rst>`_ rule set will enable the ``blank_lines_before_namespace`` rule with the default config.

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``blank_lines_before_namespace`` rule with the default config.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``blank_lines_before_namespace`` rule with the default config.
