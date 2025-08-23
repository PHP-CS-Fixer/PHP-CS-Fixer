======================================
Rule ``phpdoc_tag_no_named_arguments``
======================================

There must be ``@no-named-arguments`` tag in PHPDoc of a
class/enum/interface/trait.

Configuration
-------------

``description``
~~~~~~~~~~~~~~~

Description of the tag.

Allowed types: ``string``

Default value: ``''``

``fix_attribute``
~~~~~~~~~~~~~~~~~

Whether to fix attribute classes.

Allowed types: ``bool``

Default value: ``true``

``fix_internal``
~~~~~~~~~~~~~~~~

Whether to fix internal elements (marked with ``@internal``).

Allowed types: ``bool``

Default value: ``true``

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
   + * @no-named-arguments
   + */
    class Foo
    {
        public function bar(string $s) {}
    }

Example #2
~~~~~~~~~~

With configuration: ``['description' => 'the reason']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   +
   +/**
   + * @no-named-arguments the reason
   + */
    class Foo
    {
        public function bar(string $s) {}
    }

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Phpdoc\\PhpdocTagNoNamedArgumentsFixer <./../../../src/Fixer/Phpdoc/PhpdocTagNoNamedArgumentsFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Phpdoc\\PhpdocTagNoNamedArgumentsFixerTest <./../../../tests/Fixer/Phpdoc/PhpdocTagNoNamedArgumentsFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
