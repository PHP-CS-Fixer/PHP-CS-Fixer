=================================
Rule ``php_unit_test_annotation``
=================================

Adds or removes @test annotations from tests, following configuration.

.. warning:: Using this rule is risky.

   This fixer may change the name of your tests, and could cause incompatibility
   with abstract classes or interfaces.

Configuration
-------------

``style``
~~~~~~~~~

Whether to use the @test annotation or not.

Allowed values: ``'annotation'``, ``'prefix'``

Default value: ``'prefix'``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
   @@ -2,6 +2,6 @@
    class Test extends \PhpUnit\FrameWork\TestCase
    {
        /**
   -     * @test
   +     *
         */
   -    public function itDoesSomething() {} }
   +    public function testItDoesSomething() {} }

Example #2
~~~~~~~~~~

With configuration: ``['style' => 'annotation']``.

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,4 +1,7 @@
    <?php
    class Test extends \PhpUnit\FrameWork\TestCase
    {
   -public function testItDoesSomething() {}}
   +/**
   + * @test
   + */
   +public function itDoesSomething() {}}

Rule sets
---------

The rule is part of the following rule sets:

@Symfony:risky
  Using the ``@Symfony:risky`` rule set will enable the ``php_unit_test_annotation`` rule with the default config.

@PhpCsFixer:risky
  Using the ``@PhpCsFixer:risky`` rule set will enable the ``php_unit_test_annotation`` rule with the default config.
