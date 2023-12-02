========================================
Rule ``no_blank_lines_before_namespace``
========================================

There should be no blank lines before a namespace declaration.

Warning
-------

This rule is deprecated and will be removed in the next major version
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

You should use ``blank_lines_before_namespace`` instead.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -
   -
   -
    namespace Example;
Source class
------------

`PhpCsFixer\\Fixer\\NamespaceNotation\\NoBlankLinesBeforeNamespaceFixer <./../../../src/Fixer/NamespaceNotation/NoBlankLinesBeforeNamespaceFixer.php>`_
