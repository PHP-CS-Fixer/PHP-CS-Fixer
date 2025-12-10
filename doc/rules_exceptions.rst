================
Rules exceptions
================

Sometimes you may want to ignore/modify certain rule for specific files or directories.

If you need to ignore or reconfigure a rule for specific files, you can use the ``setRuleCustomisationPolicy`` method:

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


.. warning::

    **⚠️ WARNING ⚠️**

    When you write an implementation of ``RuleCustomisationPolicyInterface``, PHP CS Fixer may provide some fixers that, in future versions, may be either removed from the fixer sets you use, or deprecated and replaced by other fixers.

    In such cases, your implementation will stop receiving the fixers it expects to receive.

    To detect this case, PHP CS Fixer will check that all the fixer names returned by your ``getRuleCustomisers()`` method are actually used.

    If some of them are not used, PHP CS Fixer will throw an exception with the list of unused fixer names, and you will need to update your implementation.

.. warning::

    **⚠️ EXPERIMENTAL ⚠️**

    Since PHP CS Fixer may remove rules from rulesets, and replace rules with other ones (even in patch releases), your implementation of ``RuleCustomisationPolicyInterface`` may cause the exception described above even in patch releases.

    So, we can't guarantee semver compatibility for Rule Customisation Policies.
