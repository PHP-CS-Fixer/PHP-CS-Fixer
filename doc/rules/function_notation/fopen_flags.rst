====================
Rule ``fopen_flags``
====================

The flags in ``fopen`` calls must omit ``t``, and ``b`` must be omitted or
included consistently.

.. warning:: Using this rule is risky.

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
   @@ -1,2 +1,2 @@
    <?php
   -$a = fopen($foo, 'rwt');
   +$a = fopen($foo, 'rwb');

Example #2
~~~~~~~~~~

With configuration: ``['b_mode' => false]``.

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,2 +1,2 @@
    <?php
   -$a = fopen($foo, 'rwt');
   +$a = fopen($foo, 'rw');

Rule sets
---------

The rule is part of the following rule sets:

@Symfony:risky
  Using the ``@Symfony:risky`` rule set will enable the ``fopen_flags`` rule with the config below:

  ``['b_mode' => false]``

@PhpCsFixer:risky
  Using the ``@PhpCsFixer:risky`` rule set will enable the ``fopen_flags`` rule with the config below:

  ``['b_mode' => false]``
