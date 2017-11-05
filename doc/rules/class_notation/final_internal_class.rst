=============================
Rule ``final_internal_class``
=============================

Internal classes should be ``final``.

.. warning:: Using this rule is risky.

   Changing classes to ``final`` might cause code execution to break.

Configuration
-------------

``annotation-white-list``
~~~~~~~~~~~~~~~~~~~~~~~~~

Class level annotations tags that must be set in order to fix the class. (case
insensitive)

Allowed types: ``array``

Default value: ``['@internal']``

``annotation-black-list``
~~~~~~~~~~~~~~~~~~~~~~~~~

Class level annotations tags that must be omitted to fix the class, even if all
of the excluded ones are used as well. (case insensitive)

Allowed types: ``array``

Default value: ``['@final', '@Entity', '@ORM\\Entity', '@ORM\\Mapping\\Entity', '@Mapping\\Entity']``

``consider-absent-docblock-as-internal-class``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Should classes without any DocBlock be fixed to final?

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
   @@ -2,6 +2,6 @@
    /**
     * @internal
     */
   -class Sample
   +final class Sample
    {
    }

Example #2
~~~~~~~~~~

With configuration: ``['annotation-white-list' => ['@Custom']]``.

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,2 +1,2 @@
    <?php
   -/** @CUSTOM */class A{}
   +/** @CUSTOM */final class A{}

Rule sets
---------

The rule is part of the following rule set:

@PhpCsFixer:risky
  Using the ``@PhpCsFixer:risky`` rule set will enable the ``final_internal_class`` rule with the default config.
