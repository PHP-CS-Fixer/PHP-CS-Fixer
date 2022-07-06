=============================
Rule ``laravel_phpdoc_order``
=============================

Annotations in PHPDoc should be ordered so that ``@param`` annotations come
first, then ``@return`` annotations, then ``@throws`` annotations.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
    /**
     * Hello there!
     *
   - * @throws Exception|RuntimeException foo
     * @custom Test!
   - * @return int  Return the number of changes.
     * @param string $foo
     * @param bool   $bar Bar
   + * @return int  Return the number of changes.
   + * @throws Exception|RuntimeException foo
     */

Rule sets
---------

The rule is part of the following rule set:

@Laravel
  Using the `@Laravel <./../../ruleSets/Laravel.rst>`_ rule set will enable the ``laravel_phpdoc_order`` rule.
