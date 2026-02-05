===========================================
Rule ``single_blank_line_before_namespace``
===========================================

There should be exactly one blank line before a namespace declaration.

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
   -<?php  namespace A {}
   +<?php
   +
   +namespace A {}

Example #2
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php

   -
    namespace A{}

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\NamespaceNotation\\SingleBlankLineBeforeNamespaceFixer <./../../../src/Fixer/NamespaceNotation/SingleBlankLineBeforeNamespaceFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\NamespaceNotation\\SingleBlankLineBeforeNamespaceFixerTest <./../../../tests/Fixer/NamespaceNotation/SingleBlankLineBeforeNamespaceFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
