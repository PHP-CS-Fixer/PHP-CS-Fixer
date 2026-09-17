=================================
Rule ``php_unit_test_annotation``
=================================

Adds or removes @test annotations from tests, following configuration.

Warnings
--------

This rule is RISKY
~~~~~~~~~~~~~~~~~~

This fixer may change the name of your tests, and could cause incompatibility
with abstract classes or interfaces.

This rule is CONFIGURABLE
~~~~~~~~~~~~~~~~~~~~~~~~~

You can configure this rule using the following option: ``style``.

Configuration
-------------

``style``
~~~~~~~~~

Whether to use the @test annotation or not.

Allowed values: ``'annotation'`` and ``'prefix'``

Default value: ``'prefix'``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php
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

- `@PhpCsFixer:risky <./../../ruleSets/PhpCsFixerRisky.rst>`_
- `@Symfony:risky <./../../ruleSets/SymfonyRisky.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\PhpUnit\\PhpUnitTestAnnotationFixer <./../../../src/Fixer/PhpUnit/PhpUnitTestAnnotationFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\PhpUnit\\PhpUnitTestAnnotationFixerTest <./../../../tests/Fixer/PhpUnit/PhpUnitTestAnnotationFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
