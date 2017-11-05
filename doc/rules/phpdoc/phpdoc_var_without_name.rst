================================
Rule ``phpdoc_var_without_name``
================================

``@var`` and ``@type`` annotations of classy properties should not contain the
name.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -2,12 +2,12 @@
    final class Foo
    {
        /**
   -     * @var int $bar
   +     * @var int
         */
        public $bar;

        /**
   -     * @type $baz float
   +     * @type float
         */
        public $baz;
    }

Rule sets
---------

The rule is part of the following rule sets:

@Symfony
  Using the ``@Symfony`` rule set will enable the ``phpdoc_var_without_name`` rule.

@PhpCsFixer
  Using the ``@PhpCsFixer`` rule set will enable the ``phpdoc_var_without_name`` rule.
