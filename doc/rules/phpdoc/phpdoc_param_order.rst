===========================
Rule ``phpdoc_param_order``
===========================

Orders all ``@param`` annotations in DocBlocks according to method signature.

Configuration
-------------

``param_aliases``
~~~~~~~~~~~~~~~~~

List of param-like tags to treat as aliases of ``@param`` and reorder together
with them. When multiple annotations exist for the same parameter (e.g.,
``@param`` and ``@psalm-param``), they are grouped together with ``@param``
appearing first, followed by aliases in the order specified in the
configuration.

Allowed types: ``list<string>``

Default value: ``[]``

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
     * Annotations in wrong order
     *
     * @param int   $a
   + * @param array $b
     * @param Foo   $c
   - * @param array $b
     */
    function m($a, array $b, Foo $c) {}

Example #2
~~~~~~~~~~

With configuration: ``['param_aliases' => ['psalm-param']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    /**
   - * @psalm-param int $b
     * @param int $a
   + * @psalm-param int $b
     * @param string $c
     */
    function foo($a, $b, $c) {}

Example #3
~~~~~~~~~~

With configuration: ``['param_aliases' => ['psalm-param', 'phpstan-param']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    /**
   - * @phpstan-param int $b
   - * @psalm-param string $b
     * @param int $a
     * @param int $b
   + * @psalm-param string $b
   + * @phpstan-param int $b
     * @param string $c
     */
    function bar($a, $b, $c) {}

Example #4
~~~~~~~~~~

With configuration: ``['param_aliases' => ['psalm-param']]``.

Demonstrates grouping: when the same parameter has both ``@param`` and an alias,
they are kept together with ``@param`` first.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    /**
   - * @psalm-param positive-int $count
     * @param string $name
     * @param int $count
   + * @psalm-param positive-int $count
     */
    function process($name, $count) {}

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Phpdoc\\PhpdocParamOrderFixer <./../../../src/Fixer/Phpdoc/PhpdocParamOrderFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Phpdoc\\PhpdocParamOrderFixerTest <./../../../tests/Fixer/Phpdoc/PhpdocParamOrderFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
