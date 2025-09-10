=========================
Rule ``modernize_strpos``
=========================

Replace ``strpos()`` and ``stripos()`` calls with ``str_starts_with()`` or
``str_contains()`` if possible.

Warning
-------

Using this rule is risky
~~~~~~~~~~~~~~~~~~~~~~~~

Risky if ``strpos``, ``stripos``, ``str_starts_with``, ``str_contains`` or
``strtolower`` functions are overridden.

Configuration
-------------

``modernize_stripos``
~~~~~~~~~~~~~~~~~~~~~

Whether to modernize ``stripos`` calls as well.

Allowed types: ``bool``

Default value: ``false``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

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

Example #2
~~~~~~~~~~

With configuration: ``['modernize_stripos' => true]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -if (strpos($haystack, $needle) === 0) {}
   -if (strpos($haystack, $needle) !== 0) {}
   -if (strpos($haystack, $needle) !== false) {}
   -if (strpos($haystack, $needle) === false) {}
   -if (stripos($haystack, $needle) === 0) {}
   -if (stripos($haystack, $needle) !== 0) {}
   -if (stripos($haystack, $needle) !== false) {}
   -if (stripos($haystack, $needle) === false) {}
   +if (str_starts_with($haystack, $needle)  ) {}
   +if (!str_starts_with($haystack, $needle)  ) {}
   +if (str_contains($haystack, $needle)  ) {}
   +if (!str_contains($haystack, $needle)  ) {}
   +if (str_starts_with(strtolower($haystack), strtolower($needle))  ) {}
   +if (!str_starts_with(strtolower($haystack), strtolower($needle))  ) {}
   +if (str_contains(strtolower($haystack), strtolower($needle))  ) {}
   +if (!str_contains(strtolower($haystack), strtolower($needle))  ) {}

Rule sets
---------

The rule is part of the following rule sets:

- `@PHP8x0Migration:risky <./../../ruleSets/PHP8x0MigrationRisky.rst>`_
- `@PHP8x2Migration:risky <./../../ruleSets/PHP8x2MigrationRisky.rst>`_
- `@PHP80Migration:risky <./../../ruleSets/PHP80MigrationRisky.rst>`_ *(deprecated)*
- `@PHP82Migration:risky <./../../ruleSets/PHP82MigrationRisky.rst>`_ *(deprecated)*
- `@Symfony:risky <./../../ruleSets/SymfonyRisky.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Alias\\ModernizeStrposFixer <./../../../src/Fixer/Alias/ModernizeStrposFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Alias\\ModernizeStrposFixerTest <./../../../tests/Fixer/Alias/ModernizeStrposFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
