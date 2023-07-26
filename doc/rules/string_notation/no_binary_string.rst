=========================
Rule ``no_binary_string``
=========================

There should not be a binary flag before strings.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   -<?php $a = b'foo';
   +<?php $a = 'foo';

Example #2
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   -<?php $a = b<<<EOT
   +<?php $a = <<<EOT
    foo
    EOT;

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

