================================
Rule ``global_namespace_import``
================================

Imports or fully qualifies global classes/functions/constants.

Configuration
-------------

``import_classes``
~~~~~~~~~~~~~~~~~~

Whether to import, not import or ignore global classes.

Allowed types: ``null`` and ``bool``

Default value: ``true``

``import_constants``
~~~~~~~~~~~~~~~~~~~~

Whether to import, not import or ignore global constants.

Allowed types: ``null`` and ``bool``

Default value: ``null``

``import_functions``
~~~~~~~~~~~~~~~~~~~~

Whether to import, not import or ignore global functions.

Allowed types: ``null`` and ``bool``

Default value: ``null``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php

    namespace Foo;
   +use DateTimeImmutable;

   -$d = new \DateTimeImmutable();
   +$d = new DateTimeImmutable();

Example #2
~~~~~~~~~~

With configuration: ``['import_classes' => true, 'import_constants' => true, 'import_functions' => true]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php

    namespace Foo;
   +use DateTimeImmutable;
   +use function count;
   +use const M_PI;

   -if (\count($x)) {
   -    /** @var \DateTimeImmutable $d */
   -    $d = new \DateTimeImmutable();
   -    $p = \M_PI;
   +if (count($x)) {
   +    /** @var DateTimeImmutable $d */
   +    $d = new DateTimeImmutable();
   +    $p = M_PI;
    }

Example #3
~~~~~~~~~~

With configuration: ``['import_classes' => false, 'import_constants' => false, 'import_functions' => false]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php

    namespace Foo;

    use DateTimeImmutable;
    use function count;
    use const M_PI;

   -if (count($x)) {
   -    /** @var DateTimeImmutable $d */
   -    $d = new DateTimeImmutable();
   -    $p = M_PI;
   +if (\count($x)) {
   +    /** @var \DateTimeImmutable $d */
   +    $d = new \DateTimeImmutable();
   +    $p = \M_PI;
    }

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ with config:

  ``['import_classes' => false, 'import_constants' => false, 'import_functions' => false]``

- `@Symfony <./../../ruleSets/Symfony.rst>`_ with config:

  ``['import_classes' => false, 'import_constants' => false, 'import_functions' => false]``

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Import\\GlobalNamespaceImportFixer <./../../../src/Fixer/Import/GlobalNamespaceImportFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Import\\GlobalNamespaceImportFixerTest <./../../../tests/Fixer/Import/GlobalNamespaceImportFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
