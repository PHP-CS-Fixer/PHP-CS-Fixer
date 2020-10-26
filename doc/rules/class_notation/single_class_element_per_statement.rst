===========================================
Rule ``single_class_element_per_statement``
===========================================

There MUST NOT be more than one property or constant declared per statement.

Configuration
-------------

``elements``
~~~~~~~~~~~~

List of strings which element should be modified.

Allowed values: a subset of ``['const', 'property']``

Default value: ``['const', 'property']``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,6 +1,8 @@
    <?php
    final class Example
    {
   -    const FOO_1 = 1, FOO_2 = 2;
   -    private static $bar1 = array(1,2,3), $bar2 = [1,2,3];
   +    const FOO_1 = 1;
   +    const FOO_2 = 2;
   +    private static $bar1 = array(1,2,3);
   +    private static $bar2 = [1,2,3];
    }

Example #2
~~~~~~~~~~

With configuration: ``['elements' => ['property']]``.

.. code-block:: diff

   --- Original
   +++ New
   @@ -2,5 +2,6 @@
    final class Example
    {
        const FOO_1 = 1, FOO_2 = 2;
   -    private static $bar1 = array(1,2,3), $bar2 = [1,2,3];
   +    private static $bar1 = array(1,2,3);
   +    private static $bar2 = [1,2,3];
    }

Rule sets
---------

The rule is part of the following rule sets:

@PSR2
  Using the ``@PSR2`` rule set will enable the ``single_class_element_per_statement`` rule with the config below:

  ``['elements' => ['property']]``

@Symfony
  Using the ``@Symfony`` rule set will enable the ``single_class_element_per_statement`` rule with the default config.

@PhpCsFixer
  Using the ``@PhpCsFixer`` rule set will enable the ``single_class_element_per_statement`` rule with the default config.
