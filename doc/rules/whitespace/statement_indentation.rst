==============================
Rule ``statement_indentation``
==============================

Each statement must be indented.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
    if ($baz == true) {
   -  echo "foo";
   +    echo "foo";
    }
    else {
   -      echo "bar";
   +    echo "bar";
    }
