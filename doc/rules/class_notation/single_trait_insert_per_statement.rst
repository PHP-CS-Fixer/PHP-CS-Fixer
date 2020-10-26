==========================================
Rule ``single_trait_insert_per_statement``
==========================================

Each trait ``use`` must be done as single statement.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,5 +1,5 @@
    <?php
    final class Example
    {
   -    use Foo, Bar;
   +    use Foo;use Bar;
    }

Rule sets
---------

The rule is part of the following rule sets:

@Symfony
  Using the ``@Symfony`` rule set will enable the ``single_trait_insert_per_statement`` rule.

@PhpCsFixer
  Using the ``@PhpCsFixer`` rule set will enable the ``single_trait_insert_per_statement`` rule.
