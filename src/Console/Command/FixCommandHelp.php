<?php

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Console\Command;

use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\DefinedFixerInterface;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\FixerFactory;
use PhpCsFixer\RuleSet;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author SpacePossum
 *
 * @internal
 */
final class FixCommandHelp
{
    /**
     * @return string
     */
    public static function getHelpCopy()
    {
        $template =
            <<<EOF
The <info>%command.name%</info> command tries to fix as much coding standards
problems as possible on a given file or files in a given directory and its subdirectories:

    <info>$ php %command.full_name% /path/to/dir</info>
    <info>$ php %command.full_name% /path/to/file</info>

By default <comment>--path-mode</comment> is set to ``override``, which means, that if you specify the path to a file or a directory via
command arguments, then the paths provided to a ``Finder`` in config file will be ignored. You can use <comment>--path-mode=intersection</comment>
to merge paths from the config file and from the argument:

    <info>$ php %command.full_name% --path-mode=intersection /path/to/dir</info>

The <comment>--format</comment> option for the output format. Supported formats are ``txt`` (default one), ``json``, ``xml`` and ``junit``.

NOTE: When using ``junit`` format report generates in accordance with JUnit xml schema from Jenkins (see docs/junit-10.xsd).

The <comment>--verbose</comment> option will show the applied rules. When using the ``txt`` format it will also displays progress notifications.

The <comment>--rules</comment> option limits the rules to apply on the
project:

    <info>$ php %command.full_name% /path/to/project --rules=@PSR2</info>

By default, all PSR rules are run.

The <comment>--rules</comment> option lets you choose the exact rules to
apply (the rule names must be separated by a comma):

    <info>$ php %command.full_name% /path/to/dir --rules=line_ending,full_opening_tag,indentation_type</info>

You can also blacklist the rules you don't want by placing a dash in front of the rule name, if this is more convenient,
using <comment>-name_of_fixer</comment>:

    <info>$ php %command.full_name% /path/to/dir --rules=-full_opening_tag,-indentation_type</info>

When using combinations of exact and blacklist rules, applying exact rules along with above blacklisted results:

    <info>$ php %command.full_name% /path/to/project --rules=@Symfony,-@PSR1,-blank_line_before_return,strict_comparison</info>

A combination of <comment>--dry-run</comment> and <comment>--diff</comment> will
display a summary of proposed fixes, leaving your files unchanged.

The <comment>--allow-risky</comment> option allows you to set whether risky rules may run. Default value is taken from config file.
Risky rule is a rule, which could change code behaviour. By default no risky rules are run.

The command can also read from standard input, in which case it won't
automatically fix anything:

    <info>$ cat foo.php | php %command.full_name% --diff -</info>

Choose from the list of available rules:

%%%FIXERS_DETAILS%%%

The <comment>--dry-run</comment> option displays the files that need to be
fixed but without actually modifying them:

    <info>$ php %command.full_name% /path/to/code --dry-run</info>

Instead of using command line options to customize the rule, you can save the
project configuration in a <comment>.php_cs.dist</comment> file in the root directory
of your project. The file must return an instance of ``PhpCsFixer\ConfigInterface``,
which lets you configure the rules, the files and directories that
need to be analyzed. You may also create <comment>.php_cs</comment> file, which is
the local configuration that will be used instead of the project configuration. It
is a good practice to add that file into your <comment>.gitignore</comment> file.
With the <comment>--config</comment> option you can specify the path to the
<comment>.php_cs</comment> file.

The example below will add two rules to the default list of PSR2 set rules:

    <?php

    \$finder = PhpCsFixer\Finder::create()
        ->exclude('somedir')
        ->notPath('src/Symfony/Component/Translation/Tests/fixtures/resources.php')
        ->in(__DIR__)
    ;

    return PhpCsFixer\Config::create()
        ->setRules(array(
            '@PSR2' => true,
            'strict_param' => true,
            'array_syntax' => array('syntax' => 'short'),
        ))
        ->setFinder(\$finder)
    ;

    ?>

**NOTE**: ``exclude`` will work only for directories, so if you need to exclude file, try ``notPath``.

See `Symfony\\\\Finder <http://symfony.com/doc/current/components/finder.html>`_
online documentation for other `Finder` methods.

You may also use a blacklist for the rules instead of the above shown whitelist approach.
The following example shows how to use all ``Symfony`` rules but the ``full_opening_tag`` rule.

    <?php

    \$finder = PhpCsFixer\Finder::create()
        ->exclude('somedir')
        ->in(__DIR__)
    ;

    return PhpCsFixer\Config::create()
        ->setRules(array(
            '@Symfony' => true,
            'full_opening_tag' => false,
        ))
        ->setFinder(\$finder)
    ;

    ?>

You may want to use non-linux whitespaces in your project. Then you need to
configure them in your config file. Please be aware that this feature is
experimental.

    <?php

    return PhpCsFixer\Config::create()
        ->setIndent("\\t")
        ->setLineEnding("\\r\\n")
    ;

    ?>

By using ``--using-cache`` option with yes or no you can set if the caching
mechanism should be used.

Caching
-------

The caching mechanism is enabled by default. This will speed up further runs by
fixing only files that were modified since the last run. The tool will fix all
files if the tool version has changed or the list of rules has changed.
Cache is supported only for tool downloaded as phar file or installed via
composer.

Cache can be disabled via ``--using-cache`` option or config file:

    <?php

    return PhpCsFixer\Config::create()
        ->setUsingCache(false)
    ;

    ?>

Cache file can be specified via ``--cache-file`` option or config file:

    <?php

    return PhpCsFixer\Config::create()
        ->setCacheFile(__DIR__.'/.php_cs.cache')
    ;

    ?>

Using PHP CS Fixer on CI
------------------------

Require ``friendsofphp/php-cs-fixer`` as a ``dev`` dependency:

    $ ./composer.phar require --dev friendsofphp/php-cs-fixer

Then, add the following command to your CI:

    $ vendor/bin/php-cs-fixer fix --config=.php_cs.dist -v --dry-run --using-cache=no --path-mode=intersection `git diff --name-only --diff-filter=ACMRTUXB \$COMMIT_RANGE`

Where ``\$COMMIT_RANGE`` is your range of commits, eg ``\$TRAVIS_COMMIT_RANGE`` or ``HEAD~..HEAD``.

Exit codes
----------

Exit code is build using following bit flags:

*  0 OK.
*  1 General error (or PHP/HHVM minimal requirement not matched).
*  4 Some files have invalid syntax (only in dry-run mode).
*  8 Some files need fixing (only in dry-run mode).
* 16 Configuration error of the application.
* 32 Configuration error of a Fixer.
* 64 Exception raised within the application.

(applies to exit codes of the `fix` command only)
EOF
        ;

        return str_replace(
           '%%%FIXERS_DETAILS%%%',
            self::getFixersHelp(),
            $template
        );
    }

    private static function getFixersHelp()
    {
        $help = '';
        $fixerFactory = new FixerFactory();
        $fixers = $fixerFactory->registerBuiltInFixers()->getFixers();

        // sort fixers by name
        usort(
            $fixers,
            function (FixerInterface $a, FixerInterface $b) {
                return strcmp($a->getName(), $b->getName());
            }
        );

        $ruleSets = array();
        foreach (RuleSet::create()->getSetDefinitionNames() as $setName) {
            $ruleSets[$setName] = new RuleSet(array($setName => true));
        }

        $getSetsWithRule = function ($rule) use ($ruleSets) {
            $sets = array();

            foreach ($ruleSets as $setName => $ruleSet) {
                if ($ruleSet->hasRule($rule)) {
                    $sets[] = $setName;
                }
            }

            return $sets;
        };

        $count = count($fixers) - 1;
        foreach ($fixers as $i => $fixer) {
            $sets = $getSetsWithRule($fixer->getName());

            if ($fixer instanceof DefinedFixerInterface) {
                $description = $fixer->getDefinition()->getSummary();
            } else {
                $description = '[n/a]';
            }

            $attributes = array();

            if ($fixer->isRisky()) {
                $attributes[] = 'risky';
            }

            if ($fixer instanceof ConfigurableFixerInterface) {
                $attributes[] = 'configurable';
            }

            $description = wordwrap($description, 72, "\n   | ");
            $description = str_replace('`', '``', $description);

            if (!empty($sets)) {
                $help .= sprintf(" * <comment>%s</comment> [%s]\n   | %s\n", $fixer->getName(), implode(', ', $sets), $description);
            } else {
                $help .= sprintf(" * <comment>%s</comment>\n   | %s\n", $fixer->getName(), $description);
            }

            if (count($attributes)) {
                sort($attributes);
                $help .= sprintf("   | *Rule is: %s.*\n", implode(', ', $attributes));
            }

            if ($count !== $i) {
                $help .= "\n";
            }
        }

        return $help;
    }
}
