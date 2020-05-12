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
    <?php
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

@PSR12
  Using the `@PSR12 <./../../ruleSets/PSR12.rst>`_ rule set will enable the ``single_class_element_per_statement`` rule with the config below:

  ``['elements' => ['property']]``

@PSR2
  Using the `@PSR2 <./../../ruleSets/PSR2.rst>`_ rule set will enable the ``single_class_element_per_statement`` rule with the config below:

  ``['elements' => ['property']]``

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``single_class_element_per_statement`` rule with the default config.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``single_class_element_per_statement`` rule with the default config.
