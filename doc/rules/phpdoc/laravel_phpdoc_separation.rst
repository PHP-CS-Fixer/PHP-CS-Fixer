==================================
Rule ``laravel_phpdoc_separation``
==================================

Annotations in PHPDoc should be grouped together so that annotations of the same
type immediately follow each other, and annotations of a different type are
separated by a single blank line. Except @param and ``@return`` that stay
grouped.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
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

The rule is part of the following rule set:

@Laravel
  Using the `@Laravel <./../../ruleSets/Laravel.rst>`_ rule set will enable the ``laravel_phpdoc_separation`` rule.
