======================
Rule ``static_lambda``
======================

Lambdas not (indirectly) referencing ``$this`` must be declared ``static``.

Warning
-------

This rule is RISKY
~~~~~~~~~~~~~~~~~~

Risky when using ``->bindTo`` on lambdas without referencing to ``$this``.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$a = function () {
   +$a = static function () {
        echo $b;
    };

    $b = (function () {
        \assert($this !== null); // approach you can use to instruct PHP CS Fixer to not convert this lambda to static, e.g. when you see "Cannot bind an instance to a static closure" error caused by lambda handling outside of your control
    })->bindTo(new stdClass());

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer:risky <./../../ruleSets/PhpCsFixerRisky.rst>`_
- `@Symfony:risky <./../../ruleSets/SymfonyRisky.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\FunctionNotation\\StaticLambdaFixer <./../../../src/Fixer/FunctionNotation/StaticLambdaFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\FunctionNotation\\StaticLambdaFixerTest <./../../../tests/Fixer/FunctionNotation/StaticLambdaFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
