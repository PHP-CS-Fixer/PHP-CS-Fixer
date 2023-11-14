=========================
Rule ``modernize_strpos``
=========================

Replace ``strpos()`` calls with ``str_starts_with()`` or ``str_contains()`` if
possible.

Warning
-------

Using this rule is risky
~~~~~~~~~~~~~~~~~~~~~~~~

Risky if ``strpos``, ``str_starts_with`` or ``str_contains`` functions are
overridden.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -if (strpos($haystack, $needle) === 0) {}
   -if (strpos($haystack, $needle) !== 0) {}
   -if (strpos($haystack, $needle) !== false) {}
   -if (strpos($haystack, $needle) === false) {}
   +if (str_starts_with($haystack, $needle)  ) {}
   +if (!str_starts_with($haystack, $needle)  ) {}
   +if (str_contains($haystack, $needle)  ) {}
   +if (!str_contains($haystack, $needle)  ) {}

Rule sets
---------

The rule is part of the following rule sets:

- `@PHP80Migration:risky <./../../ruleSets/PHP80MigrationRisky.rst>`_
- `@Symfony:risky <./../../ruleSets/SymfonyRisky.rst>`_

Source class
------------

`PhpCsFixer\\Fixer\\Alias\\ModernizeStrposFixer <./../src/Fixer/Alias/ModernizeStrposFixer.php>`_
