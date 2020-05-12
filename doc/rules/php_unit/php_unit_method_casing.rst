===============================
Rule ``php_unit_method_casing``
===============================

Enforce camel (or snake) case for PHPUnit test methods, following configuration.

Configuration
-------------

``case``
~~~~~~~~

Apply camel or snake case to test methods

Allowed values: ``'camel_case'``, ``'snake_case'``

Default value: ``'camel_case'``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    class MyTest extends \PhpUnit\FrameWork\TestCase
    {
   -    public function test_my_code() {}
   +    public function testMyCode() {}
    }

Example #2
~~~~~~~~~~

With configuration: ``['case' => 'snake_case']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    class MyTest extends \PhpUnit\FrameWork\TestCase
    {
   -    public function testMyCode() {}
   +    public function test_my_code() {}
    }

Rule sets
---------

The rule is part of the following rule sets:

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``php_unit_method_casing`` rule with the default config.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``php_unit_method_casing`` rule with the default config.
