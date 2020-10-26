==========================
Rule ``no_unused_imports``
==========================

Unused ``use`` statements must be removed.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,5 +1,4 @@
    <?php
    use \DateTime;
   -use \Exception;

    new DateTime();

Rule sets
---------

The rule is part of the following rule sets:

@Symfony
  Using the ``@Symfony`` rule set will enable the ``no_unused_imports`` rule.

@PhpCsFixer
  Using the ``@PhpCsFixer`` rule set will enable the ``no_unused_imports`` rule.
