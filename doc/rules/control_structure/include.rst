================
Rule ``include``
================

Include/Require and file path should be divided with a single space. File path
should not be placed under brackets.

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

