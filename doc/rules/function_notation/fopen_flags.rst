====================
Rule ``fopen_flags``
====================

The flags in ``fopen`` calls must omit ``t``, and ``b`` must be omitted or
included consistently.

Warning
-------

Using this rule is risky
~~~~~~~~~~~~~~~~~~~~~~~~

Risky when the function ``fopen`` is overridden.

Configuration
-------------

``b_mode``
~~~~~~~~~~

The ``b`` flag must be used (``true``) or omitted (``false``).

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
   -$a = fopen($foo, 'rwt');
   +$a = fopen($foo, 'rwb');

Example #2
~~~~~~~~~~

With configuration: ``['b_mode' => false]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$a = fopen($foo, 'rwt');
   +$a = fopen($foo, 'rw');

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer:risky <./../../ruleSets/PhpCsFixerRisky.rst>`_ with config:

  ``['b_mode' => false]``

- `@Symfony:risky <./../../ruleSets/SymfonyRisky.rst>`_ with config:

  ``['b_mode' => false]``

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\FunctionNotation\\FopenFlagsFixer <./../../../src/Fixer/FunctionNotation/FopenFlagsFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\FunctionNotation\\FopenFlagsFixerTest <./../../../tests/Fixer/FunctionNotation/FopenFlagsFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
