========================
Rule ``empty_loop_body``
========================

Empty loop-body must be in configured style.

Configuration
-------------

``style``
~~~~~~~~~

Style of empty loop-bodies.

Allowed values: ``'braces'``, ``'semicolon'``

Default value: ``'semicolon'``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
   -<?php while(foo()){}
   +<?php while(foo());

Example #2
~~~~~~~~~~

With configuration: ``['style' => 'braces']``.

.. code-block:: diff

   --- Original
   +++ New
   -<?php while(foo());
   +<?php while(foo()){}

Rule sets
---------

The rule is part of the following rule sets:

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``empty_loop_body`` rule with the default config.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``empty_loop_body`` rule with the config below:

  ``['style' => 'braces']``
