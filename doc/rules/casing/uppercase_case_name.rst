============================
Rule ``uppercase_case_name``
============================

PHP enums case names must be uppercased.

Warning
-------

Using this rule is risky
~~~~~~~~~~~~~~~~~~~~~~~~

Since this changes variable names, it might break references.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
    enum Example
    {
   -    case element1;
   -    case element2;
   +    case ELEMENT1;
   +    case ELEMENT2;
    }
