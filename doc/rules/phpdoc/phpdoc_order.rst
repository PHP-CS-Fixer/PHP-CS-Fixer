=====================
Rule ``phpdoc_order``
=====================

Annotations in PHPDoc should be ordered so that ``@param`` annotations come
first, then ``@throws`` annotations, then ``@return`` annotations.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -2,9 +2,9 @@
    /**
     * Hello there!
     *
   - * @throws Exception|RuntimeException foo
     * @custom Test!
   - * @return int  Return the number of changes.
     * @param string $foo
     * @param bool   $bar Bar
   + * @throws Exception|RuntimeException foo
   + * @return int  Return the number of changes.
     */

Rule sets
---------

The rule is part of the following rule set:

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``phpdoc_order`` rule.
