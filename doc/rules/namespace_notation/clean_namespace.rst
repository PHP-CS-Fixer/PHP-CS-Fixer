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

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\NamespaceNotation\\CleanNamespaceFixer <./../../../src/Fixer/NamespaceNotation/CleanNamespaceFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\NamespaceNotation\\CleanNamespaceFixerTest <./../../../tests/Fixer/NamespaceNotation/CleanNamespaceFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
