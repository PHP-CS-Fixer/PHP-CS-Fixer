================================
Rule ``return_type_declaration``
================================

There should be one or no space before colon, and one space after it in return
type declarations, according to configuration.

Description
-----------

Rule is applied only in a PHP 7+ environment.

Configuration
-------------

``space_before``
~~~~~~~~~~~~~~~~

Spacing to apply before colon.

Allowed values: ``'none'``, ``'one'``

Default value: ``'none'``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,2 +1,2 @@
    <?php
   -function foo(int $a):string {};
   +function foo(int $a): string {};

Example #2
~~~~~~~~~~

With configuration: ``['space_before' => 'none']``.

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,2 +1,2 @@
    <?php
   -function foo(int $a):string {};
   +function foo(int $a): string {};

Example #3
~~~~~~~~~~

With configuration: ``['space_before' => 'one']``.

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,2 +1,2 @@
    <?php
   -function foo(int $a):string {};
   +function foo(int $a) : string {};

Rule sets
---------

The rule is part of the following rule sets:

@Symfony
  Using the ``@Symfony`` rule set will enable the ``return_type_declaration`` rule with the default config.

@PhpCsFixer
  Using the ``@PhpCsFixer`` rule set will enable the ``return_type_declaration`` rule with the default config.
