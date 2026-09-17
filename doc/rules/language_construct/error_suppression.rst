==========================
Rule ``error_suppression``
==========================

Error control operator should be added to deprecation notices and/or removed
from other cases.

Warnings
--------

This rule is RISKY
~~~~~~~~~~~~~~~~~~

Risky because adding/removing ``@`` might cause changes to code behaviour or if
``trigger_error`` function is overridden.

This rule is CONFIGURABLE
~~~~~~~~~~~~~~~~~~~~~~~~~

You can configure this rule using the following options:
``mute_deprecation_error``, ``noise_remaining_usages``,
``noise_remaining_usages_exclude``.

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

List of global functions to exclude from removing ``@``.

Allowed types: ``list<string>``

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

- `@PhpCsFixer:risky <./../../ruleSets/PhpCsFixerRisky.rst>`_
- `@Symfony:risky <./../../ruleSets/SymfonyRisky.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\LanguageConstruct\\ErrorSuppressionFixer <./../../../src/Fixer/LanguageConstruct/ErrorSuppressionFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\LanguageConstruct\\ErrorSuppressionFixerTest <./../../../tests/Fixer/LanguageConstruct/ErrorSuppressionFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
