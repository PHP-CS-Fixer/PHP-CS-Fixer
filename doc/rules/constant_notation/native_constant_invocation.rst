===================================
Rule ``native_constant_invocation``
===================================

Add leading ``\`` before constant invocation of internal constant to speed up
resolving. Constant name match is case-sensitive, except for ``null``, ``false``
and ``true``.

Warning
-------

Using this rule is risky
~~~~~~~~~~~~~~~~~~~~~~~~

Risky when any of the constants are namespaced or overridden.

Configuration
-------------

``fix_built_in``
~~~~~~~~~~~~~~~~

Whether to fix constants returned by ``get_defined_constants``. User constants
are not accounted in this list and must be specified in the include one.

Allowed types: ``bool``

Default value: ``true``

``include``
~~~~~~~~~~~

List of additional constants to fix.

Allowed types: ``array``

Default value: ``[]``

``exclude``
~~~~~~~~~~~

List of constants to ignore.

Allowed types: ``array``

Default value: ``['null', 'false', 'true']``

``scope``
~~~~~~~~~

Only fix constant invocations that are made within a namespace or fix all.

Allowed values: ``'all'``, ``'namespaced'``

Default value: ``'all'``

``strict``
~~~~~~~~~~

Whether leading ``\`` of constant invocation not meant to have it should be
removed.

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
   -<?php var_dump(PHP_VERSION, M_PI, MY_CUSTOM_PI);
   +<?php var_dump(\PHP_VERSION, \M_PI, MY_CUSTOM_PI);

Example #2
~~~~~~~~~~

With configuration: ``['scope' => 'namespaced']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    namespace space1 {
   -    echo PHP_VERSION;
   +    echo \PHP_VERSION;
    }
    namespace {
        echo M_PI;
    }

Example #3
~~~~~~~~~~

With configuration: ``['include' => ['MY_CUSTOM_PI']]``.

.. code-block:: diff

   --- Original
   +++ New
   -<?php var_dump(PHP_VERSION, M_PI, MY_CUSTOM_PI);
   +<?php var_dump(\PHP_VERSION, \M_PI, \MY_CUSTOM_PI);

Example #4
~~~~~~~~~~

With configuration: ``['fix_built_in' => false, 'include' => ['MY_CUSTOM_PI']]``.

.. code-block:: diff

   --- Original
   +++ New
   -<?php var_dump(PHP_VERSION, M_PI, MY_CUSTOM_PI);
   +<?php var_dump(PHP_VERSION, M_PI, \MY_CUSTOM_PI);

Example #5
~~~~~~~~~~

With configuration: ``['exclude' => ['M_PI']]``.

.. code-block:: diff

   --- Original
   +++ New
   -<?php var_dump(PHP_VERSION, M_PI, MY_CUSTOM_PI);
   +<?php var_dump(\PHP_VERSION, M_PI, MY_CUSTOM_PI);

Rule sets
---------

The rule is part of the following rule sets:

@PhpCsFixer:risky
  Using the `@PhpCsFixer:risky <./../../ruleSets/PhpCsFixerRisky.rst>`_ rule set will enable the ``native_constant_invocation`` rule with the config below:

  ``['fix_built_in' => false, 'include' => ['DIRECTORY_SEPARATOR', 'PHP_INT_SIZE', 'PHP_SAPI', 'PHP_VERSION_ID'], 'scope' => 'namespaced', 'strict' => true]``

@Symfony:risky
  Using the `@Symfony:risky <./../../ruleSets/SymfonyRisky.rst>`_ rule set will enable the ``native_constant_invocation`` rule with the default config.
