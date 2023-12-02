========================
Rule ``clean_namespace``
========================

Namespace must not contain spacing, comments or PHPDoc.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -namespace Foo \ Bar;
   +namespace Foo\Bar;

Example #2
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -echo foo /* comment */ \ bar();
   +echo foo\bar();

Rule sets
---------

The rule is part of the following rule sets:

- `@PHP80Migration <./../../ruleSets/PHP80Migration.rst>`_
- `@PHP81Migration <./../../ruleSets/PHP81Migration.rst>`_
- `@PHP82Migration <./../../ruleSets/PHP82Migration.rst>`_
- `@PHP83Migration <./../../ruleSets/PHP83Migration.rst>`_
- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

Source class
------------

`PhpCsFixer\\Fixer\\NamespaceNotation\\CleanNamespaceFixer <./../../../src/Fixer/NamespaceNotation/CleanNamespaceFixer.php>`_
