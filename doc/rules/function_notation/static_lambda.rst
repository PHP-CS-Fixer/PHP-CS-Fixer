======================
Rule ``static_lambda``
======================

Lambdas not (indirect) referencing ``$this`` must be declared ``static``.

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
