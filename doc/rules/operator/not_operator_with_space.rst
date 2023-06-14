================================
Rule ``not_operator_with_space``
================================

Logical NOT operators (``!``) should have leading and trailing whitespaces.

Warning
-------

This rule is deprecated and will be removed on next major version
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

You should use ``unary_operator_spaces`` instead.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php

   -if (!$bar) {
   +if ( ! $bar) {
        echo "Help!";
    }
