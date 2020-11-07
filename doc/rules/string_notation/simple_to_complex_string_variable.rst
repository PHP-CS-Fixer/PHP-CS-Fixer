==========================================
Rule ``simple_to_complex_string_variable``
==========================================

Converts explicit variables in double-quoted strings and heredoc syntax from
simple to complex format (``${`` to ``{$``).

Description
-----------

Doesn't touch implicit variables. Works together nicely with
``explicit_string_variable``.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,3 +1,3 @@
    <?php
    $name = 'World';
   -echo "Hello ${name}!";
   +echo "Hello {$name}!";

Example #2
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,5 +1,5 @@
    <?php
    $name = 'World';
    echo <<<TEST
   -Hello ${name}!
   +Hello {$name}!
    TEST;

Rule sets
---------

The rule is part of the following rule set:

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``simple_to_complex_string_variable`` rule.
