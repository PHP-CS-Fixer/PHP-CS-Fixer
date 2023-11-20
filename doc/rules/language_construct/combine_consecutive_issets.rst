===================================
Rule ``combine_consecutive_issets``
===================================

Using ``isset($var) &&`` multiple times should be done in one call.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$a = isset($a) && isset($b);
   +$a = isset($a, $b)  ;

Rule sets
---------

The rule is part of the following rule set:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_

Source class
------------

`PhpCsFixer\\Fixer\\LanguageConstruct\\CombineConsecutiveIssetsFixer <./../src/Fixer/LanguageConstruct/CombineConsecutiveIssetsFixer.php>`_
