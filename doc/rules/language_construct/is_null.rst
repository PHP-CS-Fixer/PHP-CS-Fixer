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
   @@ -1,2 +1,2 @@
    <?php
   -$a = is_null($b);
   +$a = null === $b;

Rule sets
---------

The rule is part of the following rule sets:

@Symfony:risky
  Using the ``@Symfony:risky`` rule set will enable the ``is_null`` rule with the default config.

@PhpCsFixer:risky
  Using the ``@PhpCsFixer:risky`` rule set will enable the ``is_null`` rule with the default config.
