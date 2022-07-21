=================================
Rule ``blank_lines_inside_block``
=================================

There must not be blank lines at start and end of braces blocks.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
    class Foo {
   -
        public function foo() {
   -
            if ($baz == true) {
   -
                echo "foo";
   -
            }
   -
        }
   -
    }
