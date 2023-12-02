==============================
Rule ``regular_callable_call``
==============================

Callables must be called without using ``call_user_func*`` when possible.

Warning
-------

Using this rule is risky
~~~~~~~~~~~~~~~~~~~~~~~~

Risky when the ``call_user_func`` or ``call_user_func_array`` function is
overridden or when are used in constructions that should be avoided, like
``call_user_func_array('foo', ['bar' => 'baz'])`` or ``call_user_func($foo, $foo
= 'bar')``.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -    call_user_func("var_dump", 1, 2);
   +    var_dump(1, 2);

   -    call_user_func("Bar\Baz::d", 1, 2);
   +    Bar\Baz::d(1, 2);

   -    call_user_func_array($callback, [1, 2]);
   +    $callback(...[1, 2]);

Example #2
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -call_user_func(function ($a, $b) { var_dump($a, $b); }, 1, 2);
   +(function ($a, $b) { var_dump($a, $b); })(1, 2);

   -call_user_func(static function ($a, $b) { var_dump($a, $b); }, 1, 2);
   +(static function ($a, $b) { var_dump($a, $b); })(1, 2);
Source class
------------

`PhpCsFixer\\Fixer\\FunctionNotation\\RegularCallableCallFixer <./../../../src/Fixer/FunctionNotation/RegularCallableCallFixer.php>`_
