=================================
Rule ``explicit_string_variable``
=================================

Converts implicit variables into explicit ones in double-quoted strings or
heredoc syntax.

Description
-----------

The reasoning behind this rule is the following:
- When there are two valid ways of doing the same thing, using both is
confusing, there should be a coding standard to follow
- PHP manual marks ``"$var"`` syntax as implicit and ``"${var}"`` syntax as
explicit: explicit code should always be preferred
- Explicit syntax allows word concatenation inside strings, e.g.
``"${var}IsAVar"``, implicit doesn't
- Explicit syntax is easier to detect for IDE/editors and therefore has
colors/highlight with higher contrast, which is easier to read
Backtick operator is skipped because it is harder to handle; you can use
``backtick_to_shell_exec`` fixer to normalize backticks to strings

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$a = "My name is $name !";
   -$b = "I live in $state->country !";
   -$c = "I have $farm[0] chickens !";
   +$a = "My name is ${name} !";
   +$b = "I live in {$state->country} !";
   +$c = "I have {$farm[0]} chickens !";

Rule sets
---------

The rule is part of the following rule set:

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``explicit_string_variable`` rule.
