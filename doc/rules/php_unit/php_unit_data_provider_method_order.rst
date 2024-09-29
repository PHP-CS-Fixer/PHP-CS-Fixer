============================================
Rule ``php_unit_data_provider_method_order``
============================================

Data provider method must be placed after/before the first test where used.

Configuration
-------------

``placement``
~~~~~~~~~~~~~

Prefix that replaces "test".

Allowed types: ``string``

Default value: ``'after'``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   

Example #2
~~~~~~~~~~

With configuration: ``['placement' => 'before']``.

.. code-block:: diff

   

Rule sets
---------

The rule is part of the following rule set:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\PhpUnit\\PhpUnitDataProviderMethodOrderFixer <./../../../src/Fixer/PhpUnit/PhpUnitDataProviderMethodOrderFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\PhpUnit\\PhpUnitDataProviderMethodOrderFixerTest <./../../../tests/Fixer/PhpUnit/PhpUnitDataProviderMethodOrderFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
