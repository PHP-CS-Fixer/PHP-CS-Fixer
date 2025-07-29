=====
Usage
=====

The ``fix`` command
-------------------

The ``fix`` command tries to fix as much coding standards
problems as possible.


With config file created, you can run command as easy as:

.. code-block:: console

    php php-cs-fixer.phar fix

If you do not have config file, you can run following command to fix non-hidden, non-vendor/ PHP files with default ruleset @PSR12:

.. code-block:: console

    php php-cs-fixer.phar fix .

You can also fix files in parallel, utilising more CPU cores. You can do this by using config class that implements
`PhpCsFixer\\ParallelAwareConfigInterface <./../src/ParallelAwareConfigInterface.php>`_, and use ``setParallelConfig()`` method.
Recommended way is to utilise auto-detecting parallel configuration:

.. code-block:: php

    <?php

    return (new PhpCsFixer\Config())
        ->setParallelConfig(PhpCsFixer\Runner\Parallel\ParallelConfigFactory::detect())
    ;

However, in some case you may want to fine-tune parallelisation with explicit values (e.g. in environments where auto-detection does not work properly and suggests more cores than it should):

.. code-block:: php

    <?php

    return (new PhpCsFixer\Config())
        ->setParallelConfig(new PhpCsFixer\Runner\Parallel\ParallelConfig(4, 20))
    ;

You can limit process to given file or files in a given directory and its subdirectories:

.. code-block:: console

    php php-cs-fixer.phar fix /path/to/dir
    php php-cs-fixer.phar fix /path/to/file

By default ``--path-mode`` is set to ``override``, which means, that if you specify the path to a file or a directory via
command arguments, then the paths provided to a ``Finder`` in config file will be ignored. You can also use ``--path-mode=intersection``,
which will use the intersection of the paths from the config file and from the argument:

.. code-block:: console

    php php-cs-fixer.phar fix --path-mode=intersection /path/to/dir

The ``--format`` option for the output format. Supported formats are ``@auto`` (default one on v4+), ``txt`` (default one on v3), ``checkstyle``, ``gitlab``, ``json``, ``junit`` and ``xml``.

* ``@auto`` aims to auto-select best reporter for given CI or local execution (resolution into best format is outside of BC promise and is future-ready)

  * ``gitlab`` for GitLab

* ``@auto,{format}`` takes ``@auto`` under CI, and {format} otherwise

NOTE: the output for the following formats are generated in accordance with schemas

* ``checkstyle`` follows the common `"checkstyle" XML schema </doc/schemas/fix/checkstyle.xsd>`_
* ``gitlab`` follows the `codeclimate JSON schema </doc/schemas/fix/codeclimate.json>`_
* ``json`` follows the `own JSON schema </doc/schemas/fix/schema.json>`_
* ``junit`` follows the `JUnit XML schema from Jenkins </doc/schemas/fix/junit-10.xsd>`_
* ``xml`` follows the `own XML schema </doc/schemas/fix/xml.xsd>`_

The ``--quiet`` Do not output any message.

The ``--verbose`` option will show the applied rules. When using the ``txt`` format it will also display progress output (progress bar by default, but can be changed using ``--show-progress`` option).

NOTE: if there is an error like "errors reported during linting after fixing", you can use this to be even more verbose for debugging purpose

* ``-v``: verbose
* ``-vv``: very verbose
* ``-vvv``: debug

The ``--rules`` option limits the rules to apply to the
project:

.. code-block:: console

    php php-cs-fixer.phar fix /path/to/project --rules=@PSR12

By default the ``PSR12`` rules are used. If the ``--rules`` option is used rules from config files are ignored.

The ``--rules`` option lets you choose the exact rules to apply (the rule names must be separated by a comma):

.. code-block:: console

    php php-cs-fixer.phar fix /path/to/dir --rules=line_ending,full_opening_tag,indentation_type

You can also exclude the rules you don't want by placing a dash in front of the rule name, if this is more convenient,
using ``-name_of_fixer``:

.. code-block:: console

    php php-cs-fixer.phar fix /path/to/dir --rules=-full_opening_tag,-indentation_type

When using combinations of exact and exclude rules, applying exact rules along with above excluded results:

.. code-block:: console

    php php-cs-fixer.phar fix /path/to/project --rules=@Symfony,-@PSR1,-blank_line_before_statement,strict_comparison

Complete configuration for rules can be supplied using a ``json`` formatted string.

.. code-block:: console

    php php-cs-fixer.phar fix /path/to/project --rules='{"concat_space": {"spacing": "none"}}'

The ``--dry-run`` flag will run the fixer without making changes to your files (implicitly set when you use ``check`` command).

The ``--sequential`` flag will enforce sequential analysis even if parallel config is provided.

The ``--diff`` flag can be used to let the fixer output all the changes it makes in ``udiff`` format.

The ``--allow-risky`` option (pass ``yes`` or ``no``) allows you to set whether risky rules may run. Default value is taken from config file.
A rule is considered risky if it could change code behaviour. By default no risky rules are run.

The ``--stop-on-violation`` flag stops the execution upon first file that needs to be fixed.

The ``--show-progress`` option allows you to choose the way process progress is rendered:

* ``none``: disables progress output;
* ``dots``: multiline progress output with number of files and percentage on each line. Note that with this option, the files list is evaluated before processing to get the total number of files and then kept in memory to avoid using the file iterator twice. This has an impact on memory usage so using this option is not recommended on very large projects;
* ``bar``: single line progress output with number of files and calculated percentage. Similar to ``dots`` output, it has to evaluate files list twice;

If the option is not provided, it defaults to ``bar`` unless a config file that disables output, or non-txt reporter is used, then it defaults to ``none``.

.. code-block:: console

    php php-cs-fixer.phar fix --verbose --show-progress=dots

The command can also read from standard input, in which case it won't
automatically fix anything:

.. code-block:: console

    cat foo.php | php php-cs-fixer.phar fix --diff -

Finally, if you don't need BC kept on CLI level, you might use ``PHP_CS_FIXER_FUTURE_MODE`` to start using options that
would be default in next MAJOR release and to forbid using deprecated configuration:

.. code-block:: console

    PHP_CS_FIXER_FUTURE_MODE=1 php php-cs-fixer.phar fix -v --diff

The ``--dry-run`` option displays the files that need to be
fixed but without actually modifying them:

.. code-block:: console

    php php-cs-fixer.phar fix /path/to/code --dry-run

By using ``--using-cache`` option with ``yes`` or ``no`` you can set if the caching
mechanism should be used.

The ``check`` command
---------------------

This command is a shorthand for ``fix --dry-run`` and offers all the options and arguments as ``fix`` command.
The only difference is that ``check`` command won't apply any changes, but will only print analysis result.

The ``list-files`` command
--------------------------

The ``list-files`` command will list all files which need fixing.

.. code-block:: console

    php php-cs-fixer.phar list-files

The ``--config`` option can be used, like in the ``fix`` command, to tell from which path a config file should be loaded.

.. code-block:: console

    php php-cs-fixer.phar list-files --config=.php-cs-fixer.dist.php

The output is built in a form that its easy to use in combination with ``xargs`` command in a linux pipe.
This can be useful e.g. in situations where the caching mechanism might not be available (CI, Docker) and distribute
fixing across several processes might speedup the process.

Note: You need to pass the config to the ``fix`` command, in order to make it work with several files being passed by ``list-files``.

.. code-block:: console

    php php-cs-fixer.phar list-files --config=.php-cs-fixer.dist.php | xargs -n 50 -P 8 php php-cs-fixer.phar fix --config=.php-cs-fixer.dist.php --path-mode intersection -v

* ``-n`` defines how many files a single subprocess process
* ``-P`` defines how many subprocesses the shell is allowed to spawn for parallel processing (usually similar to the number of CPUs your system has)


Rule descriptions
-----------------

Use the following command to quickly understand what a rule will do to your code:

.. code-block:: console

    php php-cs-fixer.phar describe align_multiline_comment

To visualize all the rules that belong to a ruleset:

.. code-block:: console

    php php-cs-fixer.phar describe @PSR2

Command-line completion
-----------------------

Command-line completion can be enabled by running this command and following the instructions:

.. code-block:: console

    php php-cs-fixer.phar completion --help

Caching
-------

The caching mechanism is enabled by default. This will speed up further runs by fixing only files that were modified
since the last run. The tool will fix all files if the tool version has changed or the list of rules has changed.
The cache is supported only when the tool was downloaded as a PHAR file, executed within pre-built Docker image
or installed via Composer. The cache is written to the drive progressively, so do not be afraid of interruption -
rerun the command and start where you left. The cache mechanism also supports executing the command in parallel.

Cache can be disabled via ``--using-cache`` option or config file:

.. code-block:: php

    <?php

    $config = new PhpCsFixer\Config();
    return $config->setUsingCache(false);

Cache file can be specified via ``--cache-file`` option or config file:

.. code-block:: php

    <?php

    $config = new PhpCsFixer\Config();
    return $config->setCacheFile(__DIR__.'/.php-cs-fixer.cache');

Using PHP CS Fixer on CI
------------------------

Require ``friendsofphp/php-cs-fixer`` as a ``dev`` dependency:

.. code-block:: console

    ./composer.phar require --dev friendsofphp/php-cs-fixer

Then, add the following command to your CI:

.. code-block:: console

    IFS='
    '
    CHANGED_FILES=$(git diff --name-only --diff-filter=ACMRTUXB "${COMMIT_RANGE}")
    if ! echo "${CHANGED_FILES}" | grep -qE "^(\\.php-cs-fixer(\\.dist)?\\.php|composer\\.lock)$"; then EXTRA_ARGS=$(printf -- '--path-mode=intersection\n--\n%s' "${CHANGED_FILES}"); else EXTRA_ARGS=''; fi
    vendor/bin/php-cs-fixer check --config=.php-cs-fixer.dist.php -v --stop-on-violation --using-cache=no ${EXTRA_ARGS}

Where ``$COMMIT_RANGE`` is your range of commits, e.g. ``${{github.event.before}}...${{github.event.after}}`` or ``HEAD~..HEAD``.

GitLab Code Quality Integration
###############################

If you want to integrate with GitLab's Code Quality feature, in order for report to contain correct line numbers, you
will need to use both ``--format=gitlab`` and ``--diff`` arguments.

Environment
-----------

The ``--allow-unsupported-php-version=yes`` can be used to ignore any environment requirements.

Also possible via ``PHP_CS_FIXER_IGNORE_ENV`` environment variable (deprecated),
which also allows the Fixer to run with required PHP extensions missing.

NOTE: Execution may be unstable when used.

.. code-block:: console

    PHP_CS_FIXER_IGNORE_ENV=1 php php-cs-fixer.phar fix /path/to/dir

Exit code
---------

Exit code of the ``fix`` command is built using following bit flags:

*  0 - OK.
*  1 - General error (or PHP minimal requirement not matched).
*  4 - Some files have invalid syntax (only in dry-run mode).
*  8 - Some files need fixing (only in dry-run mode).
* 16 - Configuration error of the application.
* 32 - Configuration error of a Fixer.
* 64 - Exception raised within the application.
