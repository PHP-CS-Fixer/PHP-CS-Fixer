===========
Config file
===========

Instead of using command line options to customize rules and rule sets, you can save the
project configuration in a ``.php_cs.dist`` file in the root directory of your project.
The file must return an instance of `PhpCsFixer\\ConfigInterface <../src/ConfigInterface.php>`_
which lets you configure the rules, the files and directories that
need to be analyzed. You may also create ``.php_cs`` file, which is
the local configuration that will be used instead of the project configuration. It
is a good practice to add that file into your ``.gitignore`` file.
With the ``--config`` option you can specify the path to the
``.php_cs`` file.

The example below will add two rules to the default list of PSR2 set rules:

.. code-block:: php

    <?php

    $finder = PhpCsFixer\Finder::create()
        ->exclude('somedir')
        ->notPath('src/Symfony/Component/Translation/Tests/fixtures/resources.php')
        ->in(__DIR__)
    ;

    $config = new PhpCsFixer\Config();
    return $config->setRules([
            '@PSR2' => true,
            'strict_param' => true,
            'array_syntax' => ['syntax' => 'short'],
        ])
        ->setFinder($finder)
    ;

**NOTE**: ``exclude`` will work only for directories, so if you need to exclude file, try ``notPath``.
Both ``exclude`` and ``notPath`` methods accept only relative paths to the ones defined with the ``in`` method.

See `Symfony\\Finder <https://symfony.com/doc/current/components/finder.html>`_
online documentation for other `Finder` methods.

You may also use an exclude list for the rules instead of the above shown include approach.
The following example shows how to use all ``Symfony`` rules but the ``full_opening_tag`` rule.

.. code-block:: php

    <?php

    $finder = PhpCsFixer\Finder::create()
        ->in(__DIR__)
        ->exclude('somedir')
    ;

    $config = new PhpCsFixer\Config();
    return $config->setRules([
            '@Symfony' => true,
            'full_opening_tag' => false,
        ])
        ->setFinder($finder)
    ;

You may want to use non-linux whitespaces in your project. Then you need to
configure them in your config file.

.. code-block:: php

    <?php

    $config = new PhpCsFixer\Config();
    return $config
        ->setIndent("\t")
        ->setLineEnding("\r\n")
    ;
