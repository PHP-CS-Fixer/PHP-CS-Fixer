==========================
Rule ``phpdoc_separation``
==========================

Annotations in PHPDoc should be grouped together so that annotations of the same
type immediately follow each other, and annotations of a different type are
separated by a single blank line.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,11 +1,12 @@
    <?php
    /**
     * Description.
   + *
     * @param string $foo
   + * @param bool   $bar Bar
     *
   + * @throws Exception|RuntimeException
     *
   - * @param bool   $bar Bar
   - * @throws Exception|RuntimeException
     * @return bool
     */
    function fnc($foo, $bar) {}

Rule sets
---------

The rule is part of the following rule sets:

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``phpdoc_separation`` rule.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``phpdoc_separation`` rule.
