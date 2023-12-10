=====================
Creating custom rules
=====================

If you need to enforce some specific code style rules, you can implement your
own fixers.

For each rule you want to add, create a class that implements
`PhpCsFixer\\Fixer\\FixerInterface <./../src/Fixer/FixerInterface.php>`_.
Note that there is a specific constraint
regarding custom rules names: they must match the pattern
``/^[A-Z][a-zA-Z0-9]*\/[a-z][a-z0-9_]*$/``.

Then register your custom fixers and enable them in the config file:

.. code-block:: php

    <?php
    // ...
    return (new PhpCsFixer\Config())
        // ...
        ->registerCustomFixers([
            new CustomerFixer1(),
            new CustomerFixer2(),
        ])
        ->setRules([
            // ...
            'YourVendorName/custome_rule' => true,
            'YourVendorName/custome_rule_2' => true,
        ])
    ;

There are several interfaces that your fixers can also implement if needed:

* `PhpCsFixer\\Fixer\\WhitespacesAwareFixerInterface <./../src/Fixer/WhitespacesAwareFixerInterface.php>`_: for fixers that need to know the configured indentation and line endings;
* `PhpCsFixer\\Fixer\\ConfigurableFixerInterface <./../src/Fixer/ConfigurableFixerInterface.php>`_: to create a configurable fixer;
* `PhpCsFixer\\Fixer\\DeprecatedFixerInterface <./../src/Fixer/DeprecatedFixerInterface.php>`_: to deprecate a fixer.
