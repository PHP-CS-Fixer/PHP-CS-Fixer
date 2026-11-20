=========================
Rule ``mb_str_functions``
=========================

Replace non multibyte-safe functions with corresponding mb function.

Warnings
--------

This rule is RISKY
~~~~~~~~~~~~~~~~~~

Risky when any of the functions are overridden, or when relying on the string
byte size rather than its length in characters.

This rule is CONFIGURABLE
~~~~~~~~~~~~~~~~~~~~~~~~~

You can configure this rule using the following option: ``functions``.

Configuration
-------------

``functions``
~~~~~~~~~~~~~

List of function names to fix; a function is only fixed when its ``mb_*``
alternative is available in the PHP runtime.

Allowed values: a subset of ``['ltrim', 'rtrim', 'str_pad', 'str_split', 'stripos', 'stristr', 'strlen', 'strpos', 'strrchr', 'strripos', 'strrpos', 'strstr', 'strtolower', 'strtoupper', 'substr', 'substr_count', 'trim']``

Default value: ``['ltrim', 'rtrim', 'str_pad', 'str_split', 'stripos', 'stristr', 'strlen', 'strpos', 'strrchr', 'strripos', 'strrpos', 'strstr', 'strtolower', 'strtoupper', 'substr', 'substr_count', 'trim']``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

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

Example #2
~~~~~~~~~~

With configuration: ``['functions' => ['strlen', 'strpos']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$a = strlen($a);
   -$a = strpos($a, $b);
   +$a = mb_strlen($a);
   +$a = mb_strpos($a, $b);
    $a = str_split($a);

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Alias\\MbStrFunctionsFixer <./../../../src/Fixer/Alias/MbStrFunctionsFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Alias\\MbStrFunctionsFixerTest <./../../../tests/Fixer/Alias/MbStrFunctionsFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
