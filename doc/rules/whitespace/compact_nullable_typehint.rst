==================================
Rule ``compact_nullable_typehint``
==================================

Remove extra spaces in a nullable typehint.

Description
-----------

Rule is applied only in a PHP 7.1+ environment.

Warning
-------

This rule is deprecated and will be removed in the next major version
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

You should use ``compact_nullable_type_declaration`` instead.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -function sample(? string $str): ? string
   +function sample(?string $str): ?string
    {}
Source class
------------

`PhpCsFixer\\Fixer\\Whitespace\\CompactNullableTypehintFixer <./../../../src/Fixer/Whitespace/CompactNullableTypehintFixer.php>`_
