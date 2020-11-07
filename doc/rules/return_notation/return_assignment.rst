==========================
Rule ``return_assignment``
==========================

Local, dynamic and directly referenced variables should not be assigned and
directly returned by a function or method.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,5 +1,4 @@
    <?php
    function a() {
   -    $a = 1;
   -    return $a;
   +    return 1;
    }

Rule sets
---------

The rule is part of the following rule set:

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``return_assignment`` rule.
