=============================
Rule ``phpdoc_to_param_type``
=============================

Takes ``@param`` annotations of non-mixed types and adjusts accordingly the
function signature.

Warnings
--------

This rule is EXPERIMENTAL
~~~~~~~~~~~~~~~~~~~~~~~~~

Rule is not covered with backward compatibility promise and may produce unstable
or unexpected results, use it at your own risk. Rule's behaviour may be changed
at any point, including rule's name; its options' names, availability and
allowed values; its default configuration. Rule may be even removed without
prior notice. Feel free to provide feedback and help with determining final
state of the rule.

This rule is RISKY
~~~~~~~~~~~~~~~~~~

The ``@param`` annotation is mandatory for the fixer to make changes, signatures
of methods without it (no docblock, inheritdocs) will not be fixed. Manual
actions are required if inherited signatures are not properly documented.

This rule is CONFIGURABLE
~~~~~~~~~~~~~~~~~~~~~~~~~

You can configure this rule using the following options: ``scalar_types``,
``types_map``, ``union_types``.

Configuration
-------------

``scalar_types``
~~~~~~~~~~~~~~~~

Fix also scalar types; may have unexpected behaviour due to PHP bad type
coercion system.

Allowed types: ``bool``

Default value: ``true``

``types_map``
~~~~~~~~~~~~~

Map of custom types, e.g. template types from PHPStan.

Allowed types: ``array<string, string>``

Default value: ``[]``

``union_types``
~~~~~~~~~~~~~~~

Fix also union types; turned on by default on PHP >= 8.0.0.

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

    /**
     * @param string $foo
     * @param string|null $bar
     */
   -function f($foo, $bar)
   +function f(string $foo, ?string $bar)
    {}

Example #2
~~~~~~~~~~

With configuration: ``['scalar_types' => false]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php

    /** @param Foo $foo */
   -function foo($foo) {}
   +function foo(Foo $foo) {}
    /** @param string $foo */
    function bar($foo) {}

Example #3
~~~~~~~~~~

With configuration: ``['union_types' => false]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php

    /** @param Foo $foo */
   -function foo($foo) {}
   +function foo(Foo $foo) {}
    /** @param int|string $foo */
    function bar($foo) {}

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\FunctionNotation\\PhpdocToParamTypeFixer <./../../../src/Fixer/FunctionNotation/PhpdocToParamTypeFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\FunctionNotation\\PhpdocToParamTypeFixerTest <./../../../tests/Fixer/FunctionNotation/PhpdocToParamTypeFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
