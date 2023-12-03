===========================================
Rule ``single_blank_line_before_namespace``
===========================================

There should be exactly one blank line before a namespace declaration.

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
Source class
------------

`PhpCsFixer\\Fixer\\NamespaceNotation\\SingleBlankLineBeforeNamespaceFixer <./../../../src/Fixer/NamespaceNotation/SingleBlankLineBeforeNamespaceFixer.php>`_
