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
    <?php
   -function foo(int $a):string {};
   +function foo(int $a): string {};

Example #2
~~~~~~~~~~

With configuration: ``['space_before' => 'none']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -function foo(int $a):string {};
   +function foo(int $a): string {};

Example #3
~~~~~~~~~~

With configuration: ``['space_before' => 'one']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -function foo(int $a):string {};
   +function foo(int $a) : string {};

Rule sets
---------

The rule is part of the following rule sets:

@PSR12
  Using the `@PSR12 <./../../ruleSets/PSR12.rst>`_ rule set will enable the ``return_type_declaration`` rule with the default config.

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``return_type_declaration`` rule with the default config.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``return_type_declaration`` rule with the default config.
