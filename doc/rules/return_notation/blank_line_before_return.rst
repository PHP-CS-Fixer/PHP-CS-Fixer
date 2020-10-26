=================================
Rule ``blank_line_before_return``
=================================

.. warning:: This rule is deprecated and will be removed on next major version.

   You should use ``blank_line_before_statement`` instead.

An empty line feed should precede a return statement.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -2,5 +2,6 @@
    function A()
    {
        echo 1;
   +
        return 1;
    }
