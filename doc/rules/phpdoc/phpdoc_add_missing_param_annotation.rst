============================================
Rule ``phpdoc_add_missing_param_annotation``
============================================

PHPDoc should contain ``@param`` for all params.

Configuration
-------------

``only_untyped``
~~~~~~~~~~~~~~~~

Whether to add missing ``@param`` annotations for untyped parameters only.

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
     * @param int $bar
   + * @param mixed $baz
     *
     * @return void
     */
    function f9(string $foo, $bar, $baz) {}

Example #2
~~~~~~~~~~

With configuration: ``['only_untyped' => true]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    /**
     * @param int $bar
   + * @param mixed $baz
     *
     * @return void
     */
    function f9(string $foo, $bar, $baz) {}

Example #3
~~~~~~~~~~

With configuration: ``['only_untyped' => false]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    /**
     * @param int $bar
   + * @param string $foo
   + * @param mixed $baz
     *
     * @return void
     */
    function f9(string $foo, $bar, $baz) {}

Rule sets
---------

The rule is part of the following rule set:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Phpdoc\\PhpdocAddMissingParamAnnotationFixer <./../../../src/Fixer/Phpdoc/PhpdocAddMissingParamAnnotationFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Phpdoc\\PhpdocAddMissingParamAnnotationFixerTest <./../../../tests/Fixer/Phpdoc/PhpdocAddMissingParamAnnotationFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
