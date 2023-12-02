==============================================
Rule ``no_trailing_comma_in_singleline_array``
==============================================

PHP single-line arrays should not have trailing comma.

Warning
-------

This rule is deprecated and will be removed in the next major version
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

You should use ``no_trailing_comma_in_singleline`` instead.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$a = array('sample',  );
   +$a = array('sample');
Source class
------------

`PhpCsFixer\\Fixer\\ArrayNotation\\NoTrailingCommaInSinglelineArrayFixer <./../../../src/Fixer/ArrayNotation/NoTrailingCommaInSinglelineArrayFixer.php>`_
