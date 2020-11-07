==================================
Rule ``single_line_after_imports``
==================================

Each namespace use MUST go on its own line and there MUST be one blank line
after the use statements block.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -3,6 +3,7 @@

    use Bar;
    use Baz;
   +
    final class Example
    {
    }

Example #2
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -4,7 +4,6 @@
    use Bar;
    use Baz;

   -
    final class Example
    {
    }

Rule sets
---------

The rule is part of the following rule sets:

@PSR2
  Using the `@PSR2 <./../../ruleSets/PSR2.rst>`_ rule set will enable the ``single_line_after_imports`` rule.

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``single_line_after_imports`` rule.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``single_line_after_imports`` rule.
