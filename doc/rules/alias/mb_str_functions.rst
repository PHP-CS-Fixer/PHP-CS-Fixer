=========================
Rule ``mb_str_functions``
=========================

Replace non multibyte-safe functions with corresponding mb function.

Warning
-------

Using this rule is risky
~~~~~~~~~~~~~~~~~~~~~~~~

Risky when any of the functions are overridden, or when relying on the string
byte size rather than its length in characters.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$a = strlen($a);
   -$a = strpos($a, $b);
   -$a = strrpos($a, $b);
   -$a = substr($a, $b);
   -$a = strtolower($a);
   -$a = strtoupper($a);
   -$a = stripos($a, $b);
   -$a = strripos($a, $b);
   -$a = strstr($a, $b);
   -$a = stristr($a, $b);
   -$a = strrchr($a, $b);
   -$a = substr_count($a, $b);
   +$a = mb_strlen($a);
   +$a = mb_strpos($a, $b);
   +$a = mb_strrpos($a, $b);
   +$a = mb_substr($a, $b);
   +$a = mb_strtolower($a);
   +$a = mb_strtoupper($a);
   +$a = mb_stripos($a, $b);
   +$a = mb_strripos($a, $b);
   +$a = mb_strstr($a, $b);
   +$a = mb_stristr($a, $b);
   +$a = mb_strrchr($a, $b);
   +$a = mb_substr_count($a, $b);

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Alias\\MbStrFunctionsFixer <./../../../src/Fixer/Alias/MbStrFunctionsFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Alias\\MbStrFunctionsFixerTest <./../../../tests/Fixer/Alias/MbStrFunctionsFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
