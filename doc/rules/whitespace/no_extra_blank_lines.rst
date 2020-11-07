=============================
Rule ``no_extra_blank_lines``
=============================

Removes extra blank lines and/or blank lines following configuration.

Configuration
-------------

``tokens``
~~~~~~~~~~

List of tokens to fix.

Allowed values: a subset of ``['break', 'case', 'continue', 'curly_brace_block', 'default', 'extra', 'parenthesis_brace_block', 'return', 'square_brace_block', 'switch', 'throw', 'use', 'useTrait', 'use_trait']``

Default value: ``['extra']``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
   @@ -2,5 +2,4 @@

    $foo = array("foo");

   -
    $bar = "bar";

Example #2
~~~~~~~~~~

With configuration: ``['tokens' => ['break']]``.

.. code-block:: diff

   --- Original
   +++ New
   @@ -4,7 +4,6 @@
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
   @@ -3,6 +3,5 @@
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
   @@ -1,7 +1,5 @@
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
   @@ -2,5 +2,4 @@

    $foo = array("foo");

   -
    $bar = "bar";

Example #6
~~~~~~~~~~

With configuration: ``['tokens' => ['parenthesis_brace_block']]``.

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,7 +1,5 @@
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
   @@ -3,5 +3,4 @@
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
   @@ -1,7 +1,5 @@
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
   @@ -3,5 +3,4 @@
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
   @@ -3,9 +3,8 @@
    namespace Foo;

    use Bar\Baz;
   -
    use Baz\Bar;

    class Bar
    {
    }

Example #11
~~~~~~~~~~~

With configuration: ``['tokens' => ['use_trait']]``.

.. code-block:: diff

   --- Original
   +++ New
   @@ -3,6 +3,5 @@
    class Foo
    {
        use Bar;
   -
        use Baz;
    }

Example #12
~~~~~~~~~~~

With configuration: ``['tokens' => ['switch', 'case', 'default']]``.

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,9 +1,6 @@
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

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``no_extra_blank_lines`` rule with the config below:

  ``['tokens' => ['break', 'continue', 'curly_brace_block', 'extra', 'parenthesis_brace_block', 'return', 'square_brace_block', 'throw', 'use']]``

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``no_extra_blank_lines`` rule with the config below:

  ``['tokens' => ['curly_brace_block', 'extra', 'parenthesis_brace_block', 'square_brace_block', 'throw', 'use']]``
