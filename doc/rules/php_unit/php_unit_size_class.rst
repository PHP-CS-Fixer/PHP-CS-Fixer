============================
Rule ``php_unit_size_class``
============================

All PHPUnit test cases should have ``@small``, ``@medium`` or ``@large``
annotation to enable run time limits.

Description
-----------

The special groups [small, medium, large] provides a way to identify tests that
are taking long to be executed.

Configuration
-------------

``group``
~~~~~~~~~

Define a specific group to be used in case no group is already in use.

Allowed values: ``'large'``, ``'medium'``, ``'small'``

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
   +/**
   + * @medium
   + */
    class MyTest extends TestCase {}
