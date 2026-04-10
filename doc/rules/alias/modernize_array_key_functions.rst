======================================
Rule ``modernize_array_key_functions``
======================================

Replace ``$array[array_key_first($array)]`` with ``array_first($array)`` and
``$array[array_key_last($array)]`` with ``array_last($array)``.

Warning
-------

This rule is RISKY
~~~~~~~~~~~~~~~~~~

This changes the behaviour for empty arrays: ``$foo[array_key_first($foo)]``
crashes with an invalid array offset error (unless it is caught using the ``??``
operator), ``array_first($foo)`` returns null instead. Also risky if the
``array_first``, ``array_last``, ``array_key_first`` or ``array_key_last``
functions are overridden.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
    $foo = [1, 2, 3];
   -$first = $foo[array_key_first($foo)];
   -$last = $foo[array_key_last($foo)];
   -$first = $foo->bar[array_key_first($foo->bar)] ?? null;
   -$last = FooClass::CONSTANT[array_key_last(FooClass::CONSTANT)] ?? null;
   +$first = array_first($foo);
   +$last = array_last($foo);
   +$first = array_first($foo->bar) ?? null;
   +$last = array_last(FooClass::CONSTANT) ?? null;

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Alias\\ModernizeArrayKeyFunctionsFixer <./../../../src/Fixer/Alias/ModernizeArrayKeyFunctionsFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Alias\\ModernizeArrayKeyFunctionsFixerTest <./../../../tests/Fixer/Alias/ModernizeArrayKeyFunctionsFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
