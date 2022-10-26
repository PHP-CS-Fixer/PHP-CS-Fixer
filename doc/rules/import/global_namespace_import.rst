================================
Rule ``global_namespace_import``
================================

Imports or fully qualifies global classes/functions/constants.

Configuration
-------------

``import_constants``
~~~~~~~~~~~~~~~~~~~~

Whether to import, not import or ignore global constants.

Allowed values: ``false``, ``null``, ``true``

Default value: ``null``

``import_functions``
~~~~~~~~~~~~~~~~~~~~

Whether to import, not import or ignore global functions.

Allowed values: ``false``, ``null``, ``true``

Default value: ``null``

``import_classes``
~~~~~~~~~~~~~~~~~~

Whether to import, not import or ignore global classes.

Allowed values: ``false``, ``null``, ``true``

Default value: ``true``

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

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``global_namespace_import`` rule with the config below:

  ``['import_classes' => false, 'import_constants' => false, 'import_functions' => false]``

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``global_namespace_import`` rule with the config below:

  ``['import_classes' => false, 'import_constants' => false, 'import_functions' => false]``
