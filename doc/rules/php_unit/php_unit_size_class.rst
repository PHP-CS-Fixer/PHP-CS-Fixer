============================
Rule ``php_unit_size_class``
============================

All PHPUnit test cases should have ``@small``, ``@medium`` or ``@large``
annotation to enable run time limits.

Description
-----------

The special groups [small, medium, large] provides a way to identify tests that
are taking long to be executed.

Warning
-------

This rule is CONFIGURABLE
~~~~~~~~~~~~~~~~~~~~~~~~~

You can configure this rule using the following option: ``group``.

Configuration
-------------

``group``
~~~~~~~~~

Define a specific group to be used in case no group is already in use.

Allowed values: ``'large'``, ``'medium'`` and ``'small'``

Default value: ``'small'``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   +
   +/**
   + * @small
   + */
    class MyTest extends TestCase {}

Example #2
~~~~~~~~~~

With configuration: ``['group' => 'medium']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   +
   +/**
   + * @medium
   + */
    class MyTest extends TestCase {}

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\PhpUnit\\PhpUnitSizeClassFixer <./../../../src/Fixer/PhpUnit/PhpUnitSizeClassFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\PhpUnit\\PhpUnitSizeClassFixerTest <./../../../tests/Fixer/PhpUnit/PhpUnitSizeClassFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
