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

``directory``
~~~~~~~~~~~~~

Directory in which apply the changes, empty value will result with current
working directory (result of ``getcwd`` call).

Allowed types: ``string``

Default value: ``''``

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
