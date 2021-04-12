=============================
Rule ``final_internal_class``
=============================

Internal classes should be ``final``.

.. warning:: Using this rule is risky.

   Changing classes to ``final`` might cause code execution to break.

Configuration
-------------

``annotation_include``
~~~~~~~~~~~~~~~~~~~~~~

Class level annotations tags that must be set in order to fix the class. (case
insensitive)

.. note:: The previous name of this option was ``annotation-white-list`` but it is now deprecated and will be removed on next major version.

Allowed types: ``array``

Default value: ``['@internal']``

``annotation_exclude``
~~~~~~~~~~~~~~~~~~~~~~

Class level annotations tags that must be omitted to fix the class, even if all
of the white list ones are used as well. (case insensitive)

.. note:: The previous name of this option was ``annotation-black-list`` but it is now deprecated and will be removed on next major version.

Allowed types: ``array``

Default value: ``['@final', '@Entity', '@ORM\\Entity', '@ORM\\Mapping\\Entity', '@Mapping\\Entity', '@Document', '@ODM\\Document']``

``consider_absent_docblock_as_internal_class``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Should classes without any DocBlock be fixed to final?

.. note:: The previous name of this option was ``consider-absent-docblock-as-internal-class`` but it is now deprecated and will be removed on next major version.

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
    /**
     * @internal
     */
   -class Sample
   +final class Sample
    {
    }

Example #2
~~~~~~~~~~

With configuration: ``['annotation_include' => ['@Custom'], 'annotation_exclude' => ['@not-fix']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    /**
     * @CUSTOM
     */
   -class A{}
   +final class A{}

    /**
     * @CUSTOM
     * @not-fix
     */
    class B{}

Rule sets
---------

The rule is part of the following rule set:

@PhpCsFixer:risky
  Using the `@PhpCsFixer:risky <./../../ruleSets/PhpCsFixerRisky.rst>`_ rule set will enable the ``final_internal_class`` rule with the default config.
