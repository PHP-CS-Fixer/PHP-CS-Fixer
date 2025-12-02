=========================
Rule ``phpdoc_line_span``
=========================

Changes doc blocks from single to multi line, or reversed.

Warning
-------

This rule is CONFIGURABLE
~~~~~~~~~~~~~~~~~~~~~~~~~

You can configure this rule using the following options: ``const``, ``method``,
``property``.

Configuration
-------------

``class``
~~~~~~~~~

Whether class/interface/trait blocks should be single or multi line.

Allowed values: ``'multi'``, ``'single'`` and ``null``

Default value: ``'multi'``

``const``
~~~~~~~~~

Whether const blocks should be single or multi line.

Allowed values: ``'multi'``, ``'single'`` and ``null``

Default value: ``'multi'``

``method``
~~~~~~~~~~

Whether method doc blocks should be single or multi line.

Allowed values: ``'multi'``, ``'single'`` and ``null``

Default value: ``'multi'``

``other``
~~~~~~~~~

Whether blocks for other code lines should be single or multi line.

Allowed values: ``'multi'``, ``'single'`` and ``null``

Default value: ``null``

``property``
~~~~~~~~~~~~

Whether property doc blocks should be single or multi line.

Allowed values: ``'multi'``, ``'single'`` and ``null``

Default value: ``'multi'``

``trait_import``
~~~~~~~~~~~~~~~~

Whether trait usage blocks should be single or multi line.

Allowed values: ``'multi'``, ``'single'`` and ``null``

Default value: ``null``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php

    class Foo{
   -    /** @var bool */
   +    /**
   +     * @var bool
   +     */
        public $var;
    }

Example #2
~~~~~~~~~~

With configuration: ``['property' => 'single']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php

    class Foo{
   -    /**
   -    * @var bool
   -    */
   +    /** @var bool */
        public $var;
    }

Example #3
~~~~~~~~~~

With configuration: ``['other' => 'single']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -/**
   - * @var string
   - */
   +/** @var string */
    $var = foo();

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Phpdoc\\PhpdocLineSpanFixer <./../../../src/Fixer/Phpdoc/PhpdocLineSpanFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Phpdoc\\PhpdocLineSpanFixerTest <./../../../tests/Fixer/Phpdoc/PhpdocLineSpanFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
