========================================
Rule ``no_trailing_comma_in_singleline``
========================================

In a single line statement there MUST NOT be a trailing comma after: the last
argument when calling a function, the last element when declaring array, the
last variable assignment when destructuring, the last ``use`` group when
declaring usage.

Configuration
-------------

``elements``
~~~~~~~~~~~~

Which elements to fix.

Allowed values: a subset of ``['arguments', 'array_destructuring', 'array', 'group_import']``

Default value: ``['arguments', 'array_destructuring', 'array', 'group_import']``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -foo($a,);
   -$foo = array(1,);
   -[$foo, $bar,] = $array;
   -use a\{ClassA, ClassB,};
   +foo($a);
   +$foo = array(1);
   +[$foo, $bar] = $array;
   +use a\{ClassA, ClassB};

Example #2
~~~~~~~~~~

With configuration: ``['elements' => ['array_destructuring']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    foo($a,);
   -[$foo, $bar,] = $array;
   +[$foo, $bar] = $array;

Rule sets
---------

The rule is part of the following rule sets:

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``no_trailing_comma_in_singleline`` rule with the default config.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``no_trailing_comma_in_singleline`` rule with the default config.
