=========================
Rule ``fopen_flag_order``
=========================

Order the flags in ``fopen`` calls, ``b`` and ``t`` must be last.

.. warning:: Using this rule is risky.

   Risky when the function ``fopen`` is overridden.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,2 +1,2 @@
    <?php
   -$a = fopen($foo, 'br+');
   +$a = fopen($foo, 'r+b');

Rule sets
---------

The rule is part of the following rule sets:

@Symfony:risky
  Using the ``@Symfony:risky`` rule set will enable the ``fopen_flag_order`` rule.

@PhpCsFixer:risky
  Using the ``@PhpCsFixer:risky`` rule set will enable the ``fopen_flag_order`` rule.
