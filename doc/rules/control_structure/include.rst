================
Rule ``include``
================

Include/Require and file path should be divided with a single space. File path
should not be placed within parentheses.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -require ("sample1.php");
   -require_once  "sample2.php";
   -include       "sample3.php";
   -include_once("sample4.php");
   +require "sample1.php";
   +require_once "sample2.php";
   +include "sample3.php";
   +include_once "sample4.php";

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\ControlStructure\\IncludeFixer <./../../../src/Fixer/ControlStructure/IncludeFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\ControlStructure\\IncludeFixerTest <./../../../tests/Fixer/ControlStructure/IncludeFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
