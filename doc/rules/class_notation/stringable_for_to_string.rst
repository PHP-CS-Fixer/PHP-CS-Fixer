=================================
Rule ``stringable_for_to_string``
=================================

A class that implements the ``__toString()`` method must explicitly implement
the ``Stringable`` interface.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -class Foo
   +class Foo implements \Stringable
    {
        public function __toString()
        {
            return "Foo";
        }
    }

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\ClassNotation\\StringableForToStringFixer <./../../../src/Fixer/ClassNotation/StringableForToStringFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\ClassNotation\\StringableForToStringFixerTest <./../../../tests/Fixer/ClassNotation/StringableForToStringFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
