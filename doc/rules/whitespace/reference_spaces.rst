=========================
Rule ``reference_spaces``
=========================

Reference operator should be surrounded by space as defined.

Configuration
-------------

``anonymous_function_use_block``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Default fix strategy for reference operator in anonymous function's use blocks.

Allowed values: ``'by_reference'``, ``'single_space'`` and ``false``

Default value: ``'by_reference'``

``assignment``
~~~~~~~~~~~~~~

Default fix strategy for reference operator in assignments.

Allowed values: ``'by_assign'``, ``'by_reference'``, ``'no_space'``, ``'single_space'`` and ``false``

Default value: ``'by_reference'``

``function_signature``
~~~~~~~~~~~~~~~~~~~~~~

Default fix strategy for reference operator in function signatures.

Allowed values: ``'by_reference'``, ``'single_space'`` and ``false``

Default value: ``'by_reference'``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
   -<?php $foo = & $bar;
   +<?php $foo = &$bar;

Example #2
~~~~~~~~~~

With configuration: ``['assignment' => 'by_assign']``.

.. code-block:: diff

   --- Original
   +++ New
   -<?php $foo = & $bar;
   +<?php $foo =& $bar;

Example #3
~~~~~~~~~~

With configuration: ``['assignment' => 'by_reference']``.

.. code-block:: diff

   --- Original
   +++ New
   -<?php $foo = & $bar;
   +<?php $foo = &$bar;

Example #4
~~~~~~~~~~

With configuration: ``['assignment' => 'single_space']``.

.. code-block:: diff

   --- Original
   +++ New
   -<?php $foo =&$bar;
   +<?php $foo = & $bar;

Example #5
~~~~~~~~~~

With configuration: ``['assignment' => 'no_space']``.

.. code-block:: diff

   --- Original
   +++ New
   -<?php $foo =& $bar;
   +<?php $foo =&$bar;

Example #6
~~~~~~~~~~

With configuration: ``['function_signature' => 'by_reference']``.

.. code-block:: diff

   --- Original
   +++ New
   -<?php function foo(& $bar) {}
   +<?php function foo(&$bar) {}

Example #7
~~~~~~~~~~

With configuration: ``['function_signature' => 'single_space']``.

.. code-block:: diff

   --- Original
   +++ New
   -<?php function foo(&$bar) {}
   +<?php function foo(& $bar) {}

Example #8
~~~~~~~~~~

With configuration: ``['anonymous_function_use_block' => 'by_reference']``.

.. code-block:: diff

   --- Original
   +++ New
   -<?php $foo = function () use (& $bar) {};
   +<?php $foo = function () use (&$bar) {};

Example #9
~~~~~~~~~~

With configuration: ``['anonymous_function_use_block' => 'single_space']``.

.. code-block:: diff

   --- Original
   +++ New
   -<?php $foo = function () use (&$bar) {};
   +<?php $foo = function () use (& $bar) {};
