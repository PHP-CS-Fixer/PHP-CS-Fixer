========================================
Rule ``no_leading_namespace_whitespace``
========================================

The namespace declaration line shouldn't contain leading whitespace.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   - namespace Test8a;
   -    namespace Test8b;
   +namespace Test8a;
   +namespace Test8b;

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\NamespaceNotation\\NoLeadingNamespaceWhitespaceFixer <./../../../src/Fixer/NamespaceNotation/NoLeadingNamespaceWhitespaceFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\NamespaceNotation\\NoLeadingNamespaceWhitespaceFixerTest <./../../../tests/Fixer/NamespaceNotation/NoLeadingNamespaceWhitespaceFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
