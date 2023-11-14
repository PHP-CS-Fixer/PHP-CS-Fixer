======================
Rule ``static_lambda``
======================

Lambdas not (indirectly) referencing ``$this`` must be declared ``static``.

Warning
-------

Using this rule is risky
~~~~~~~~~~~~~~~~~~~~~~~~

Risky when using ``->bindTo`` on lambdas without referencing to ``$this``.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$a = function () use ($b)
   +$a = static function () use ($b)
    {   echo $b;
    };

Rule sets
---------

The rule is part of the following rule set:

- `@PhpCsFixer:risky <./../../ruleSets/PhpCsFixerRisky.rst>`_

Source class
------------

`PhpCsFixer\\Fixer\\FunctionNotation\\StaticLambdaFixer <./../src/Fixer/FunctionNotation/StaticLambdaFixer.php>`_
