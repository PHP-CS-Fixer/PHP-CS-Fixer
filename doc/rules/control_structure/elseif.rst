===============
Rule ``elseif``
===============

The keyword ``elseif`` should be used instead of ``else if`` so that all control
keywords look like single words.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
    if ($a) {
   -} else if ($b) {
   +} elseif ($b) {
    }

Rule sets
---------

The rule is part of the following rule sets:

- `@PER <./../../ruleSets/PER.rst>`_
- `@PER-CS1.0 <./../../ruleSets/PER-CS1.0.rst>`_
- `@PER-CS2.0 <./../../ruleSets/PER-CS2.0.rst>`_
- `@PSR2 <./../../ruleSets/PSR2.rst>`_
- `@PSR12 <./../../ruleSets/PSR12.rst>`_
- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

