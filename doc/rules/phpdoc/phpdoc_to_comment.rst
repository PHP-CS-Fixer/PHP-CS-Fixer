==========================
Rule ``phpdoc_to_comment``
==========================

Docblocks should only be used on structural elements.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,7 +1,7 @@
    <?php
    $first = true;// needed because by default first docblock is never fixed.

   -/** This should not be a docblock */
   +/* This should not be a docblock */
    foreach($connections as $key => $sqlite) {
        $sqlite->open($path);
    }

Rule sets
---------

The rule is part of the following rule sets:

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``phpdoc_to_comment`` rule.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``phpdoc_to_comment`` rule.
