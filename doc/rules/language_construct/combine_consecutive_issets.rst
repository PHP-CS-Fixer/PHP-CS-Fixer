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

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\LanguageConstruct\\CombineConsecutiveIssetsFixer <./../../../src/Fixer/LanguageConstruct/CombineConsecutiveIssetsFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\LanguageConstruct\\CombineConsecutiveIssetsFixerTest <./../../../tests/Fixer/LanguageConstruct/CombineConsecutiveIssetsFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
