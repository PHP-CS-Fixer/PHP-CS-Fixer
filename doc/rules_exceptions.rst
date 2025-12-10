================
Rules exceptions
================

Sometimes you may want to ignore/modify certain rule for specific files or directories.

.. warning::

    **⚠️ EXPERIMENTAL ⚠️**

    This feature is experimental.

    That means the API may change in minor releases of PHP CS Fixer.

    We are open to feedback about this feature to make it better.

.. warning::

    **⚠️ WARNING ⚠️**

    Sets provided by PHP CS Fixer are a living standards, and as such their definition is NOT covered with Backward Compatibility promise.
    That means any upgrade of PHP CS Fixer may add or remove fixers from the sets (or change their configuration).
    This already means that after upgrade of PHP CS Fixer, your project will start applying different rules, simply due to fact of upgrade.
    This may come from adding a new rules to the set, but also removed the rule or replace the deprecated rule by it's successor.

    Now, when you use exceptions for the rules, this may lead to situation where, after PHP CS Fixer upgrade,
    your exception refers to a rule that is no longer part of the set you use.

    For such cases, PHP CS Fixer will check that all the rules configured as exceptions are actually configured in set and raise error if some of them are not used.
    This will prevent accidental breaking of rules exceptions due to upgrade of PHP CS Fixer.

Configuring exceptions via ``@php-cs-fixer-ignore`` annotation
--------------------------------------------------------------

This is the simplest way to **ignore** specific rule for specific file.

Just put this annotation in comment anywhere on top or bottom of the file, and the rule will be ignored for the whole file:

.. code-block:: php

    <?php

    declare(strict_types=1);

    /*
     * File header..
     * LICENSE...
     */

    // @php-cs-fixer-ignore no_binary_string
    // @php-cs-fixer-ignore no_trailing_whitespace Optional comment - Rule ignored because of ...

    /*
     * @php-cs-fixer-ignore no_unset_on_property,no_useless_else Multiple rules ignored at once
     */

    class MyClass {
        /* ... */
    }

    // @php-cs-fixer-ignore no_empty_statement    Works Also
    // @php-cs-fixer-ignore no_extra_blank_lines  on bottom of file

Configuring exceptions via ``Rule Customisation Policy``
--------------------------------------------------------

Sometimes, simple annotation usage for ignoring the rule in-file is not enough.

If you need to **ignore** or **reconfigure** a rule for specific files, you can inject ``RuleCustomisationPolicyInterface`` via ``Config::setRuleCustomisationPolicy()`` method:

.. code-block:: php

    <?php

    use PhpCsFixer\Config;
    use PhpCsFixer\Config\RuleCustomisationPolicyInterface;
    use PhpCsFixer\Finder;
    use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
    use PhpCsFixer\Fixer\FixerInterface;

    class MyPolicy implements RuleCustomisationPolicyInterface
    {
        public function getPolicyVersionForCache(): string
        {
            // to provide version of the policy, you may use the following snippet if your policy does not depend on any code outside of the file
            return hash_file(\PHP_VERSION_ID >= 8_01_00 ? 'xxh128' : 'md5', __FILE__);
        }

        public function getRuleCustomisers(): array
        {
            return [
                'array_syntax' => static function (\SplFileInfo $file) {
                    if (str_ends_with($file->getPathname(), '/tests/foo.php')) {
                        // Disable the fixer for the file tests/foo.php
                        return false;
                    }

                    if (str_ends_with($file->getPathname(), '/bin/entrypoint')) {
                        // For the file bin/entrypoint let's create a new fixer instance with a different configuration
                        $fixer = new ArraySyntaxFixer();
                        $fixer->configure(['syntax' => 'long']);
                        return $fixer;
                    }

                    // Keep the default configuration for other files
                    return true;
                },
            ];
        }
    }

    return (new Config())
        ->setRules([
            'array_syntax' => ['syntax' => 'short'],
        ])
        ->setRuleCustomisationPolicy(new MyPolicy())
        ->setFinder(
            (new Finder())
                ->in(__DIR__)
        )
    ;
