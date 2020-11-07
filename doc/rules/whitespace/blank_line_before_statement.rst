====================================
Rule ``blank_line_before_statement``
====================================

An empty line feed must precede any configured statement.

Configuration
-------------

``statements``
~~~~~~~~~~~~~~

List of statements which must be preceded by an empty line.

Allowed values: a subset of ``['break', 'case', 'continue', 'declare', 'default', 'die', 'do', 'exit', 'for', 'foreach', 'goto', 'if', 'include', 'include_once', 'require', 'require_once', 'return', 'switch', 'throw', 'try', 'while', 'yield', 'yield_from']``

Default value: ``['break', 'continue', 'declare', 'return', 'throw', 'try']``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,5 +1,6 @@
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
   @@ -2,7 +2,8 @@
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
   @@ -2,6 +2,7 @@
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
   @@ -1,5 +1,6 @@
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
   @@ -3,5 +3,6 @@
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
   @@ -5,5 +5,6 @@
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
   @@ -1,5 +1,6 @@
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
   @@ -2,5 +2,6 @@

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
   @@ -1,6 +1,7 @@
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
   @@ -1,5 +1,6 @@
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
   @@ -1,7 +1,8 @@
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
   @@ -2,5 +2,6 @@

    if (true) {
        $foo = $bar;
   +
        yield $foo;
    }

Rule sets
---------

The rule is part of the following rule sets:

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``blank_line_before_statement`` rule with the config below:

  ``['statements' => ['break', 'case', 'continue', 'declare', 'default', 'exit', 'goto', 'include', 'include_once', 'require', 'require_once', 'return', 'switch', 'throw', 'try']]``

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``blank_line_before_statement`` rule with the config below:

  ``['statements' => ['return']]``
