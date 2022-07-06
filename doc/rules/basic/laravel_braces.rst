=======================
Rule ``laravel_braces``
=======================

The body of each structure MUST be enclosed by braces. Braces should be properly
placed. Body of braces should be properly indented.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
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

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$positive = function ($item) { return $item >= 0; };
   +$positive = function ($item) {
   +    return $item >= 0;
   +};
    $negative = function ($item) {
   -                return $item < 0; };
   +    return $item < 0;
   +};

Example #3
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php

    class Foo
    {
        public function bar($baz)
        {
   -        if ($baz = 900) echo "Hello!";
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

The rule is part of the following rule set:

@Laravel
  Using the `@Laravel <./../../ruleSets/Laravel.rst>`_ rule set will enable the ``laravel_braces`` rule.
