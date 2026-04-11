=================================
Rule ``blank_line_after_declare``
=================================

There MUST be a blank line after a ``declare()``.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -declare(strict_types=1);echo "Foo";
   +declare(strict_types=1);
   +
   +echo "Foo";

Example #2
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -declare(strict_types=1); echo "Foo";
   +declare(strict_types=1);
   +
   +echo "Foo";

Example #3
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
    declare(strict_types=1);
   +
    echo "Foo";

Example #4
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
    declare(ticks=1) {
        // Do stuff
    }
   +
    echo "Foo";

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Whitespace\\BlankLineAfterDeclareFixer <./../../../src/Fixer/Whitespace/BlankLineAfterDeclareFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Whitespace\\BlankLineAfterDeclareFixerTest <./../../../tests/Fixer/Whitespace/BlankLineAfterDeclareFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
