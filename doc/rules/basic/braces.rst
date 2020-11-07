===============
Rule ``braces``
===============

The body of each structure MUST be enclosed by braces. Braces should be properly
placed. Body of braces should be properly indented.

Configuration
-------------

``allow_single_line_closure``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Whether single line lambda notation should be allowed.

Allowed types: ``bool``

Default value: ``false``

``position_after_functions_and_oop_constructs``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

whether the opening brace should be placed on "next" or "same" line after classy
constructs (non-anonymous classes, interfaces, traits, methods and non-lambda
functions).

Allowed values: ``'next'``, ``'same'``

Default value: ``'next'``

``position_after_control_structures``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

whether the opening brace should be placed on "next" or "same" line after
control structures.

Allowed values: ``'next'``, ``'same'``

Default value: ``'same'``

``position_after_anonymous_constructs``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

whether the opening brace should be placed on "next" or "same" line after
anonymous constructs (anonymous classes and lambda functions).

Allowed values: ``'next'``, ``'same'``

Default value: ``'same'``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,25 +1,27 @@
    <?php

   -class Foo {
   -    public function bar($baz) {
   -        if ($baz = 900) echo "Hello!";
   +class Foo
   +{
   +    public function bar($baz)
   +    {
   +        if ($baz = 900) {
   +            echo "Hello!";
   +        }

   -        if ($baz = 9000)
   +        if ($baz = 9000) {
                echo "Wait!";
   +        }

   -        if ($baz == true)
   -        {
   +        if ($baz == true) {
                echo "Why?";
   -        }
   -        else
   -        {
   +        } else {
                echo "Ha?";
            }

   -        if (is_array($baz))
   -            foreach ($baz as $b)
   -            {
   +        if (is_array($baz)) {
   +            foreach ($baz as $b) {
                    echo $b;
                }
   +        }
        }
    }

Example #2
~~~~~~~~~~

With configuration: ``['allow_single_line_closure' => true]``.

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,4 +1,5 @@
    <?php
    $positive = function ($item) { return $item >= 0; };
    $negative = function ($item) {
   -                return $item < 0; };
   +    return $item < 0;
   +};

Example #3
~~~~~~~~~~

With configuration: ``['position_after_functions_and_oop_constructs' => 'same']``.

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,27 +1,25 @@
    <?php

   -class Foo
   -{
   -    public function bar($baz)
   -    {
   -        if ($baz = 900) echo "Hello!";
   +class Foo {
   +    public function bar($baz) {
   +        if ($baz = 900) {
   +            echo "Hello!";
   +        }

   -        if ($baz = 9000)
   +        if ($baz = 9000) {
                echo "Wait!";
   +        }

   -        if ($baz == true)
   -        {
   +        if ($baz == true) {
                echo "Why?";
   -        }
   -        else
   -        {
   +        } else {
                echo "Ha?";
            }

   -        if (is_array($baz))
   -            foreach ($baz as $b)
   -            {
   +        if (is_array($baz)) {
   +            foreach ($baz as $b) {
                    echo $b;
                }
   +        }
        }
    }

Rule sets
---------

The rule is part of the following rule sets:

@PSR2
  Using the `@PSR2 <./../../ruleSets/PSR2.rst>`_ rule set will enable the ``braces`` rule with the default config.

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``braces`` rule with the config below:

  ``['allow_single_line_closure' => true]``

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``braces`` rule with the config below:

  ``['allow_single_line_closure' => true]``
