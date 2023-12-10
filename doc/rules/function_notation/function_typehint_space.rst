================================
Rule ``function_typehint_space``
================================

Ensure single space between function's argument and its typehint.

Warning
-------

This rule is deprecated and will be removed in the next major version
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

You should use ``type_declaration_spaces`` instead.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -function sample(array$a)
   +function sample(array $a)
    {}

Example #2
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -function sample(array  $a)
   +function sample(array $a)
    {}
Source class
------------

`PhpCsFixer\\Fixer\\FunctionNotation\\FunctionTypehintSpaceFixer <./../../../src/Fixer/FunctionNotation/FunctionTypehintSpaceFixer.php>`_
