==========================================
Rule ``not_operator_with_successor_space``
==========================================

Logical NOT operators (``!``) should have one trailing whitespace.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php

   -if (!$bar) {
   +if (! $bar) {
        echo "Help!";
    }
