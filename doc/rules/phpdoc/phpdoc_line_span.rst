=========================
Rule ``phpdoc_line_span``
=========================

Changes doc blocks from single to multi line, or reversed. Works for class
constants, properties and methods only.

Warning
-------

This rule is CONFIGURABLE
~~~~~~~~~~~~~~~~~~~~~~~~~

You can configure this rule using the following options: ``const``, ``method``,
``property``.

Configuration
-------------

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

``property``
~~~~~~~~~~~~

Whether property doc blocks should be single or multi line.

Allowed values: ``'multi'``, ``'single'`` and ``null``

Default value: ``'multi'``

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

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Phpdoc\\PhpdocLineSpanFixer <./../../../src/Fixer/Phpdoc/PhpdocLineSpanFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Phpdoc\\PhpdocLineSpanFixerTest <./../../../tests/Fixer/Phpdoc/PhpdocLineSpanFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
