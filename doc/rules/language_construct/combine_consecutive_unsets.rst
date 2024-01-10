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

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\LanguageConstruct\\CombineConsecutiveUnsetsFixer <./../../../src/Fixer/LanguageConstruct/CombineConsecutiveUnsetsFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\LanguageConstruct\\CombineConsecutiveUnsetsFixerTest <./../../../tests/Fixer/LanguageConstruct/CombineConsecutiveUnsetsFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
