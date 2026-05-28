=================================
Rule ``general_attribute_remove``
=================================

Removes configured attributes by their respective FQN.

Warning
-------

This rule is CONFIGURABLE
~~~~~~~~~~~~~~~~~~~~~~~~~

You can configure this rule using the following option: ``attributes``.

Configuration
-------------

``attributes``
~~~~~~~~~~~~~~

List of FQNs of attributes for removal.

Allowed types: ``list<class-string>``

Default value: ``[]``

Examples
--------

Example #1
~~~~~~~~~~

With configuration: ``['attributes' => ['\\A\\B\\Foo']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -#[\A\B\Foo]
    function foo() {}

Example #2
~~~~~~~~~~

With configuration: ``['attributes' => ['\\A\\B\\Foo', 'A\\B\\Bar']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    use A\B\Bar as BarAlias;

   -#[\A\B\Foo]
   -#[BarAlias]
    function foo() {}

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\AttributeNotation\\GeneralAttributeRemoveFixer <./../../../src/Fixer/AttributeNotation/GeneralAttributeRemoveFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\AttributeNotation\\GeneralAttributeRemoveFixerTest <./../../../tests/Fixer/AttributeNotation/GeneralAttributeRemoveFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
