=================================
Rule ``laravel_phpdoc_alignment``
=================================

All items of the given phpdoc tags must be either left-aligned separated with
one space except ``@param`` tag which has to be separated with two.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
    /**
   - * @param  EngineInterface $templating
   - * @param string      $format
   - * @param  int  $code       an HTTP response status code
   - * @param    bool         $debug
   - * @param  mixed    &$reference     a parameter passed by reference
   + * @param  EngineInterface  $templating
   + * @param  string  $format
   + * @param  int  $code  an HTTP response status code
   + * @param  bool  $debug
   + * @param  mixed  &$reference  a parameter passed by reference
     */

Rule sets
---------

The rule is part of the following rule set:

@Laravel
  Using the `@Laravel <./../../ruleSets/Laravel.rst>`_ rule set will enable the ``laravel_phpdoc_alignment`` rule.
