=====================
Creating custom rule sets
=====================

If you need to enforce a set pf specific code style rules, you can implement your
own rule set.

For each rule set you want to add, create a class that extends
`PhpCsFixer\\RuleSet\\AbstractRuleSetDescription <../src/RuleSet/AbstractRuleSetDescription.php>`_.
Note that there is a specific constraint
regarding custom rule sets names: they must match the pattern
``/^\@[A-Z][a-zA-Z0-9]*\/[A-Z][a-zA-Z0-9]*(:risky)?$/``.

Then register your custom rule set and enable them in the config file:

.. code-block:: php

    <?php
    // ...
    return (new PhpCsFixer\Config())
        // ...
        ->registerCustomRuleSets([
            new Custom1Set(),
            new CustomName2Set(),
        ])
        ->setRules([
            // ...
            '@YourVendorName/Custom1' => true,
            '@YourVendorName/CustomName2' => true,
        ])
    ;
