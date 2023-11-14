=========================
Rule ``phpdoc_line_span``
=========================

Changes doc blocks from single to multi line, or reversed. Works for class
constants, properties and methods only.

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
Source class
------------

`PhpCsFixer\\Fixer\\Phpdoc\\PhpdocLineSpanFixer <./../src/Fixer/Phpdoc/PhpdocLineSpanFixer.php>`_
