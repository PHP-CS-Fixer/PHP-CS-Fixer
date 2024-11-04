==========================
Rule ``no_unused_imports``
==========================

Unused ``use`` statements must be removed.

Configuration
-------------

``comments_match_case``
~~~~~~~~~~~~~~~~~~~~~~~

Whether to treat comments as case-sensitive.

Allowed values: ``false`` and ``true``

Default value: ``false``

``comments_search_annotations_only``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Whether to analyze only annotations when considering comments.

Allowed values: ``false`` and ``true``

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
    use \DateTime;
   -use \Exception;

    new DateTime();

Example #2
~~~~~~~~~~

With configuration: ``['comments_match_case' => true]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    use \DateTime;
   -use \Exception;

    // Any exception will be ignored
    new DateTime();

Example #3
~~~~~~~~~~

With configuration: ``['comments_search_annotations_only' => true]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    use \DateTime;
   -use \Exception;
   -use \Throwable;

    // Throwable is the exception to the rule
    new DateTime();

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Import\\NoUnusedImportsFixer <./../../../src/Fixer/Import/NoUnusedImportsFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Import\\NoUnusedImportsFixerTest <./../../../tests/Fixer/Import/NoUnusedImportsFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
