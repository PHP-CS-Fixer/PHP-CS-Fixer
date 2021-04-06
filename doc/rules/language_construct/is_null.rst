================
Rule ``is_null``
================

Replaces ``is_null($var)`` expression with ``null === $var``.

.. warning:: Using this rule is risky.

   Risky when the function ``is_null`` is overridden.

Configuration
-------------

``use_yoda_style``
~~~~~~~~~~~~~~~~~~

.. warning:: This option is deprecated and will be removed on next major version. Use ``yoda_style`` fixer instead.

Whether Yoda style conditions should be used.

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
   -$a = is_null($b);
   +$a = null === $b;

Rule sets
---------

The rule is part of the following rule sets:

@PhpCsFixer:risky
  Using the `@PhpCsFixer:risky <./../../ruleSets/PhpCsFixerRisky.rst>`_ rule set will enable the ``is_null`` rule with the default config.

@Symfony:risky
  Using the `@Symfony:risky <./../../ruleSets/SymfonyRisky.rst>`_ rule set will enable the ``is_null`` rule with the default config.
