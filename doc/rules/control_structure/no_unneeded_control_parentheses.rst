========================================
Rule ``no_unneeded_control_parentheses``
========================================

Removes unneeded parentheses around control statements.

Configuration
-------------

``statements``
~~~~~~~~~~~~~~

List of control statements to fix.

Allowed types: ``array``

Default value: ``['break', 'clone', 'continue', 'echo_print', 'return', 'switch_case', 'yield']``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,9 +1,9 @@
    <?php
   -while ($x) { while ($y) { break (2); } }
   -clone($a);
   -while ($y) { continue (2); }
   -echo("foo");
   -print("foo");
   -return (1 + 2);
   -switch ($a) { case($x); }
   -yield(2);
   +while ($x) { while ($y) { break 2; } }
   +clone $a;
   +while ($y) { continue 2; }
   +echo "foo";
   +print "foo";
   +return 1 + 2;
   +switch ($a) { case $x; }
   +yield 2;

Example #2
~~~~~~~~~~

With configuration: ``['statements' => ['break', 'continue']]``.

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,9 +1,9 @@
    <?php
   -while ($x) { while ($y) { break (2); } }
   +while ($x) { while ($y) { break 2; } }
    clone($a);
   -while ($y) { continue (2); }
   +while ($y) { continue 2; }
    echo("foo");
    print("foo");
    return (1 + 2);
    switch ($a) { case($x); }
    yield(2);

Rule sets
---------

The rule is part of the following rule sets:

@Symfony
  Using the ``@Symfony`` rule set will enable the ``no_unneeded_control_parentheses`` rule with the default config.

@PhpCsFixer
  Using the ``@PhpCsFixer`` rule set will enable the ``no_unneeded_control_parentheses`` rule with the default config.
