====================================
Rule ``blank_line_before_statement``
====================================

An empty line feed must precede any configured statement.

Configuration
-------------

``statements``
~~~~~~~~~~~~~~

List of statements which must be preceded by an empty line.

Allowed values: a subset of ``['break', 'case', 'continue', 'declare', 'default', 'do', 'exit', 'for', 'foreach', 'goto', 'if', 'include', 'include_once', 'phpdoc', 'require', 'require_once', 'return', 'switch', 'throw', 'try', 'while', 'yield', 'yield_from']``

Default value: ``['break', 'continue', 'declare', 'return', 'throw', 'try']``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    function A() {
        echo 1;
   +
        return 1;
    }

Example #2
~~~~~~~~~~

With configuration: ``['statements' => ['break']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    switch ($foo) {
        case 42:
            $bar->process();
   +
            break;
        case 44:
            break;
    }

Example #3
~~~~~~~~~~

With configuration: ``['statements' => ['continue']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    foreach ($foo as $bar) {
        if ($bar->isTired()) {
            $bar->sleep();
   +
            continue;
        }
    }

Example #4
~~~~~~~~~~

With configuration: ``['statements' => ['do']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    $i = 0;
   +
    do {
        echo $i;
    } while ($i > 0);

Example #5
~~~~~~~~~~

With configuration: ``['statements' => ['exit']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    if ($foo === false) {
        exit(0);
    } else {
        $bar = 9000;
   +
        exit(1);
    }

Example #6
~~~~~~~~~~

With configuration: ``['statements' => ['goto']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    a:

    if ($foo === false) {
        goto a;
    } else {
        $bar = 9000;
   +
        goto b;
    }

Example #7
~~~~~~~~~~

With configuration: ``['statements' => ['if']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    $a = 9000;
   +
    if (true) {
        $foo = $bar;
    }

Example #8
~~~~~~~~~~

With configuration: ``['statements' => ['return']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php

    if (true) {
        $foo = $bar;
   +
        return;
    }

Example #9
~~~~~~~~~~

With configuration: ``['statements' => ['switch']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    $a = 9000;
   +
    switch ($a) {
        case 42:
            break;
    }

Example #10
~~~~~~~~~~~

With configuration: ``['statements' => ['throw']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    if (null === $a) {
        $foo->bar();
   +
        throw new \UnexpectedValueException("A cannot be null.");
    }

Example #11
~~~~~~~~~~~

With configuration: ``['statements' => ['try']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    $a = 9000;
   +
    try {
        $foo->bar();
    } catch (\Exception $exception) {
        $a = -1;
    }

Example #12
~~~~~~~~~~~

With configuration: ``['statements' => ['yield']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    function getValues() {
        yield 1;
   +
        yield 2;
   +
        // comment
        yield 3;
    }

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ with config:

  ``['statements' => ['break', 'case', 'continue', 'declare', 'default', 'exit', 'goto', 'include', 'include_once', 'phpdoc', 'require', 'require_once', 'return', 'switch', 'throw', 'try', 'yield', 'yield_from']]``

- `@Symfony <./../../ruleSets/Symfony.rst>`_ with config:

  ``['statements' => ['return']]``


References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Whitespace\\BlankLineBeforeStatementFixer <./../../../src/Fixer/Whitespace/BlankLineBeforeStatementFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Whitespace\\BlankLineBeforeStatementFixerTest <./../../../tests/Fixer/Whitespace/BlankLineBeforeStatementFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
