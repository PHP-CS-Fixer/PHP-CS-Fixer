=============================
Rule ``no_extra_blank_lines``
=============================

Removes extra blank lines and/or blank lines following configuration.

Configuration
-------------

``tokens``
~~~~~~~~~~

List of tokens to fix.

Allowed values: a subset of ``['attribute', 'break', 'case', 'continue', 'curly_brace_block', 'default', 'extra', 'parenthesis_brace_block', 'return', 'square_brace_block', 'switch', 'throw', 'use', 'use_trait']``

Default value: ``['extra']``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php

    $foo = array("foo");

   -
    $bar = "bar";

Example #2
~~~~~~~~~~

With configuration: ``['tokens' => ['break']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php

    switch ($foo) {
        case 41:
            echo "foo";
            break;
   -
        case 42:
            break;
    }

Example #3
~~~~~~~~~~

With configuration: ``['tokens' => ['continue']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php

    for ($i = 0; $i < 9000; ++$i) {
        if (true) {
            continue;
   -
        }
    }

Example #4
~~~~~~~~~~

With configuration: ``['tokens' => ['curly_brace_block']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php

    for ($i = 0; $i < 9000; ++$i) {
   -
        echo $i;
   -
    }

Example #5
~~~~~~~~~~

With configuration: ``['tokens' => ['extra']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php

    $foo = array("foo");

   -
    $bar = "bar";

Example #6
~~~~~~~~~~

With configuration: ``['tokens' => ['parenthesis_brace_block']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php

    $foo = array(
   -
        "foo"
   -
    );

Example #7
~~~~~~~~~~

With configuration: ``['tokens' => ['return']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php

    function foo($bar)
    {
        return $bar;
   -
    }

Example #8
~~~~~~~~~~

With configuration: ``['tokens' => ['square_brace_block']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php

    $foo = [
   -
        "foo"
   -
    ];

Example #9
~~~~~~~~~~

With configuration: ``['tokens' => ['throw']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php

    function foo($bar)
    {
        throw new \Exception("Hello!");
   -
    }

Example #10
~~~~~~~~~~~

With configuration: ``['tokens' => ['use']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php

    namespace Foo;

    use Bar\Baz;
   -
    use Baz\Bar;

    class Bar
    {
    }

Example #11
~~~~~~~~~~~

With configuration: ``['tokens' => ['switch', 'case', 'default']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    switch($a) {
   -
        case 1:
   -
        default:
   -
            echo 3;
    }

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ with config:

  ``['tokens' => ['attribute', 'break', 'case', 'continue', 'curly_brace_block', 'default', 'extra', 'parenthesis_brace_block', 'return', 'square_brace_block', 'switch', 'throw', 'use']]``

- `@Symfony <./../../ruleSets/Symfony.rst>`_ with config:

  ``['tokens' => ['attribute', 'case', 'continue', 'curly_brace_block', 'default', 'extra', 'parenthesis_brace_block', 'square_brace_block', 'switch', 'throw', 'use']]``


References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Whitespace\\NoExtraBlankLinesFixer <./../../../src/Fixer/Whitespace/NoExtraBlankLinesFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Whitespace\\NoExtraBlankLinesFixerTest <./../../../tests/Fixer/Whitespace/NoExtraBlankLinesFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
