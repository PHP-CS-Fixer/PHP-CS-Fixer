==========================
Rule ``error_suppression``
==========================

Error control operator should be added to deprecation notices and/or removed
from other cases.

Warning
-------

Using this rule is risky
~~~~~~~~~~~~~~~~~~~~~~~~

Risky because adding/removing ``@`` might cause changes to code behaviour or if
``trigger_error`` function is overridden.

Configuration
-------------

``mute_deprecation_error``
~~~~~~~~~~~~~~~~~~~~~~~~~~

Whether to add ``@`` in deprecation notices.

Allowed types: ``bool``

Default value: ``true``

``noise_remaining_usages``
~~~~~~~~~~~~~~~~~~~~~~~~~~

Whether to remove ``@`` in remaining usages.

Allowed types: ``bool``

Default value: ``false``

``noise_remaining_usages_exclude``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

List of global functions to exclude from removing ``@``

Allowed types: ``array``

Default value: ``[]``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -trigger_error('Warning.', E_USER_DEPRECATED);
   +@trigger_error('Warning.', E_USER_DEPRECATED);

Example #2
~~~~~~~~~~

With configuration: ``['noise_remaining_usages' => true]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -@mkdir($dir);
   -@unlink($path);
   +mkdir($dir);
   +unlink($path);

Example #3
~~~~~~~~~~

With configuration: ``['noise_remaining_usages' => true, 'noise_remaining_usages_exclude' => ['unlink']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -@mkdir($dir);
   +mkdir($dir);
    @unlink($path);

Rule sets
---------

The rule is part of the following rule sets:

@PhpCsFixer:risky
  Using the `@PhpCsFixer:risky <./../../ruleSets/PhpCsFixerRisky.rst>`_ rule set will enable the ``error_suppression`` rule with the default config.

@Symfony:risky
  Using the `@Symfony:risky <./../../ruleSets/SymfonyRisky.rst>`_ rule set will enable the ``error_suppression`` rule with the default config.
