=====
Usage
=====

The ``fix`` command tries to fix as much coding standards
problems as possible on a given file or files in a given directory and its subdirectories:

.. code-block:: console

    $ php php-cs-fixer.phar fix /path/to/dir
    $ php php-cs-fixer.phar fix /path/to/file

By default ``--path-mode`` is set to ``override``, which means, that if you specify the path to a file or a directory via
command arguments, then the paths provided to a ``Finder`` in config file will be ignored. You can use ``--path-mode=intersection``
to merge paths from the config file and from the argument:

.. code-block:: console

    $ php php-cs-fixer.phar fix --path-mode=intersection /path/to/dir

The ``--format`` option for the output format. Supported formats are ``txt`` (default one), ``json``, ``xml``, ``checkstyle``, ``junit`` and ``gitlab``.

NOTE: the output for the following formats are generated in accordance with XML schemas

* ``checkstyle`` follows the common `"checkstyle" xml schema </doc/report-schema/checkstyle.xsd>`_
* ``junit`` follows the `JUnit xml schema from Jenkins </doc/report-schema/junit-10.xsd>`_

The ``--quiet`` Do not output any message.

The ``--verbose`` option will show the applied rules. When using the ``txt`` format it will also display progress notifications.

NOTE: if there is an error like "errors reported during linting after fixing", you can use this to be even more verbose for debugging purpose

* ``--verbose=0`` or no option: normal
* ``--verbose``, ``--verbose=1``, ``-v``: verbose
* ``--verbose=2``, ``-vv``: very verbose
* ``--verbose=3``, ``-vvv``: debug

The ``--rules`` option limits the rules to apply to the
project:

.. code-block:: console

    $ php php-cs-fixer.phar fix /path/to/project --rules=@PSR2

By default the ``PSR1`` and ``PSR2`` rules are used. If the ``--rules`` option is used rules from config files are ignored.

The ``--rules`` option lets you choose the exact rules to apply (the rule names must be separated by a comma):

.. code-block:: console

    $ php php-cs-fixer.phar fix /path/to/dir --rules=line_ending,full_opening_tag,indentation_type

You can also exclude the rules you don't want by placing a dash in front of the rule name, if this is more convenient,
using ``-name_of_fixer``:

.. code-block:: console

    $ php php-cs-fixer.phar fix /path/to/dir --rules=-full_opening_tag,-indentation_type

When using combinations of exact and exclude rules, applying exact rules along with above excluded results:

.. code-block:: console

    $ php php-cs-fixer.phar fix /path/to/project --rules=@Symfony,-@PSR1,-blank_line_before_statement,strict_comparison

Complete configuration for rules can be supplied using a ``json`` formatted string.

.. code-block:: console

    $ php php-cs-fixer.phar fix /path/to/project --rules='{"concat_space": {"spacing": "none"}}'

The ``--dry-run`` flag will run the fixer without making changes to your files.

The ``--diff`` flag can be used to let the fixer output all the changes it makes.

The ``--diff-format`` option allows to specify in which format the fixer should output the changes it makes:

* ``udiff``: unified diff format;
* ``sbd``: Sebastianbergmann/diff format (default when using `--diff` without specifying `diff-format`).

The ``--allow-risky`` option (pass ``yes`` or ``no``) allows you to set whether risky rules may run. Default value is taken from config file.
A rule is considered risky if it could change code behaviour. By default no risky rules are run.

The ``--stop-on-violation`` flag stops the execution upon first file that needs to be fixed.

The ``--show-progress`` option allows you to choose the way process progress is rendered:

* ``none``: disables progress output;
* ``run-in``: [deprecated] simple single-line progress output;
* ``estimating``: [deprecated] multiline progress output with number of files and percentage on each line. Note that with this option, the files list is evaluated before processing to get the total number of files and then kept in memory to avoid using the file iterator twice. This has an impact on memory usage so using this option is not recommended on very large projects;
* ``estimating-max``: [deprecated] same as ``dots``;
* ``dots``: same as ``estimating`` but using all terminal columns instead of default 80.

If the option is not provided, it defaults to ``run-in`` unless a config file that disables output is used, in which case it defaults to ``none``. This option has no effect if the verbosity of the command is less than ``verbose``.

.. code-block:: console

    $ php php-cs-fixer.phar fix --verbose --show-progress=estimating

The command can also read from standard input, in which case it won't
automatically fix anything:

.. code-block:: console

    $ cat foo.php | php php-cs-fixer.phar fix --diff -

Finally, if you don't need BC kept on CLI level, you might use `PHP_CS_FIXER_FUTURE_MODE` to start using options that
would be default in next MAJOR release (unified differ, estimating, full-width progress indicator):

.. code-block:: console

    $ PHP_CS_FIXER_FUTURE_MODE=1 php php-cs-fixer.phar fix -v --diff

The ``--dry-run`` option displays the files that need to be
fixed but without actually modifying them:

.. code-block:: console

    $ php php-cs-fixer.phar fix /path/to/code --dry-run

By using ``--using-cache`` option with ``yes`` or ``no`` you can set if the caching
mechanism should be used.

Rule descriptions
-----------------

Use the following command to quickly understand what a rule will do to your code:

.. code-block:: console

    $ php php-cs-fixer.phar describe align_multiline_comment

To visualize all the rules that belong to a ruleset:

.. code-block:: console

    $ php php-cs-fixer.phar describe @PSR2

Caching
-------

The caching mechanism is enabled by default. This will speed up further runs by
fixing only files that were modified since the last run. The tool will fix all
files if the tool version has changed or the list of rules has changed.
Cache is supported only for tool downloaded as phar file or installed via
composer.

Cache can be disabled via ``--using-cache`` option or config file:

.. code-block:: php

    <?php

    return PhpCsFixer\Config::create()
        ->setUsingCache(false)
    ;

Cache file can be specified via ``--cache-file`` option or config file:

.. code-block:: php

    <?php

    return PhpCsFixer\Config::create()
        ->setCacheFile(__DIR__.'/.php_cs.cache')
    ;

Using PHP CS Fixer on CI
------------------------

Require ``friendsofphp/php-cs-fixer`` as a ``dev`` dependency:

.. code-block:: console

    $ ./composer.phar require --dev friendsofphp/php-cs-fixer

Then, add the following command to your CI:

.. code-block:: console

    $ IFS='
    $ '
    $ CHANGED_FILES=$(git diff --name-only --diff-filter=ACMRTUXB "${COMMIT_RANGE}")
    $ if ! echo "${CHANGED_FILES}" | grep -qE "^(\\.php_cs(\\.dist)?|composer\\.lock)$"; then EXTRA_ARGS=$(printf -- '--path-mode=intersection\n--\n%s' "${CHANGED_FILES}"); else EXTRA_ARGS=''; fi
    $ vendor/bin/php-cs-fixer fix --config=.php_cs.dist -v --dry-run --stop-on-violation --using-cache=no ${EXTRA_ARGS}

Where ``$COMMIT_RANGE`` is your range of commits, e.g. ``$TRAVIS_COMMIT_RANGE`` or ``HEAD~..HEAD``.

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
