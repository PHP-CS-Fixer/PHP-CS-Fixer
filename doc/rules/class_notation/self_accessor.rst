======================
Rule ``self_accessor``
======================

Inside class or interface element ``self`` should be preferred to the class name
itself.

Warning
-------

Using this rule is risky
~~~~~~~~~~~~~~~~~~~~~~~~

Risky when using dynamic calls like get_called_class() or late static binding.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
    class Sample
    {
        const BAZ = 1;
   -    const BAR = Sample::BAZ;
   +    const BAR = self::BAZ;

        public function getBar()
        {
   -        return Sample::BAR;
   +        return self::BAR;
        }
    }

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer:risky <./../../ruleSets/PhpCsFixerRisky.rst>`_
- `@Symfony:risky <./../../ruleSets/SymfonyRisky.rst>`_

Source class
------------

`PhpCsFixer\\Fixer\\ClassNotation\\SelfAccessorFixer <./../../../src/Fixer/ClassNotation/SelfAccessorFixer.php>`_
