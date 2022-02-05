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

@PhpCsFixer:risky
  Using the `@PhpCsFixer:risky <./../../ruleSets/PhpCsFixerRisky.rst>`_ rule set will enable the ``fopen_flags`` rule with the config below:

  ``['b_mode' => false]``

@Symfony:risky
  Using the `@Symfony:risky <./../../ruleSets/SymfonyRisky.rst>`_ rule set will enable the ``fopen_flags`` rule with the config below:

  ``['b_mode' => false]``
