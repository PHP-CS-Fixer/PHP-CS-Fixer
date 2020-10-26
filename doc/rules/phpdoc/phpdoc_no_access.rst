=========================
Rule ``phpdoc_no_access``
=========================

``@access`` annotations should be omitted from PHPDoc.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -3,7 +3,6 @@
    {
        /**
         * @internal
   -     * @access private
         */
        private $bar;
    }

Rule sets
---------

The rule is part of the following rule sets:

@Symfony
  Using the ``@Symfony`` rule set will enable the ``phpdoc_no_access`` rule.

@PhpCsFixer
  Using the ``@PhpCsFixer`` rule set will enable the ``phpdoc_no_access`` rule.
