===============================
Rule ``single_line_empty_body``
===============================

Empty body of class, interface, trait, enum or function must be abbreviated as
``{}`` and placed on the same line as the previous symbol, separated by a single
space.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php function foo(
        int $x
   -)
   -{
   -}
   +) {}
