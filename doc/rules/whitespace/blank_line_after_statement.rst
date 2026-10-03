===================================
Rule ``blank_line_after_statement``
===================================

An empty line feed must follow any configured statement.

Warning
-------

This rule is CONFIGURABLE
~~~~~~~~~~~~~~~~~~~~~~~~~

You can configure this rule using the following option: ``statements``.

Configuration
-------------

``statements``
~~~~~~~~~~~~~~

List of statements which must be followed by an empty line.

Allowed values: a subset of ``['declare', 'do', 'for', 'foreach', 'if', 'switch', 'try', 'while']``

Default value: ``['declare']``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php

    declare(strict_types=1);
   +
    use Foo\Bar;

    return [];

Example #2
~~~~~~~~~~

With configuration: ``['statements' => ['if']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    if (true) {
        $a = 1;
    }
   +
    $b = 2;

Example #3
~~~~~~~~~~

With configuration: ``['statements' => ['switch']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    switch ($a) {
        case 1:
            break;
    }
   +
    $b = 2;

Example #4
~~~~~~~~~~

With configuration: ``['statements' => ['for']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    for ($i = 0; $i < 3; ++$i) {
        $a = $i;
    }
   +
    $b = 2;

Example #5
~~~~~~~~~~

With configuration: ``['statements' => ['foreach']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    foreach ($arr as $v) {
        $a = $v;
    }
   +
    $b = 2;

Example #6
~~~~~~~~~~

With configuration: ``['statements' => ['while']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    while ($a) {
        --$a;
    }
   +
    $b = 2;

Example #7
~~~~~~~~~~

With configuration: ``['statements' => ['do']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    do {
        --$a;
    } while ($a > 0);
   +
    $b = 2;

Example #8
~~~~~~~~~~

With configuration: ``['statements' => ['try']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    try {
        $a = 1;
    } catch (\Throwable $t) {
        $a = 0;
    }
   +
    $b = 2;

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Whitespace\\BlankLineAfterStatementFixer <./../../../src/Fixer/Whitespace/BlankLineAfterStatementFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Whitespace\\BlankLineAfterStatementFixerTest <./../../../tests/Fixer/Whitespace/BlankLineAfterStatementFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
