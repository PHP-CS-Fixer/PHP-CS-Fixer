========================================
Rule ``no_blank_lines_before_namespace``
========================================

There should be no blank lines before a namespace declaration.

Warning
-------

This rule is DEPRECATED and will be removed in the next major version 4.0
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

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

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\NamespaceNotation\\NoBlankLinesBeforeNamespaceFixer <./../../../src/Fixer/NamespaceNotation/NoBlankLinesBeforeNamespaceFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\NamespaceNotation\\NoBlankLinesBeforeNamespaceFixerTest <./../../../tests/Fixer/NamespaceNotation/NoBlankLinesBeforeNamespaceFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
