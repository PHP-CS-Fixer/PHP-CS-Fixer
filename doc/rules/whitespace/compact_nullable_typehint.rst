==================================
Rule ``compact_nullable_typehint``
==================================

Remove extra spaces in a nullable typehint.

Description
-----------

Rule is applied only in a PHP 7.1+ environment.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,3 +1,3 @@
    <?php
   -function sample(? string $str): ? string
   +function sample(?string $str): ?string
    {}

Rule sets
---------

The rule is part of the following rule set:

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``compact_nullable_typehint`` rule.
