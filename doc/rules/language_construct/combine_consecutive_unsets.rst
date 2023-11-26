===================================
Rule ``combine_consecutive_unsets``
===================================

Calling ``unset`` on multiple items should be done in one call.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -unset($a); unset($b);
   +unset($a, $b); 

Rule sets
---------

The rule is part of the following rule set:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_

Source class
------------

`PhpCsFixer\\Fixer\\LanguageConstruct\\CombineConsecutiveUnsetsFixer <./../src/Fixer/LanguageConstruct/CombineConsecutiveUnsetsFixer.php>`_
