=====================================
Rule ``phpdoc_no_useless_inheritdoc``
=====================================

Classy that does not inherit must not have ``@inheritdoc`` tags.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,5 +1,5 @@
    <?php
   -/** {@inheritdoc} */
   +/** */
    class Sample
    {
    }

Example #2
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -2,9 +2,9 @@
    class Sample
    {
        /**
   -     * @inheritdoc
   +     * 
         */
        public function Test()
        {
        }
    }

Rule sets
---------

The rule is part of the following rule sets:

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``phpdoc_no_useless_inheritdoc`` rule.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``phpdoc_no_useless_inheritdoc`` rule.
