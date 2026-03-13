===================================
Rule ``array_singleline_multiline``
===================================

Arrays must be either singleline or multiline based on a configured length
threshold. Arrays containing comments shall be left unchanged.

Warning
-------

This rule is CONFIGURABLE
~~~~~~~~~~~~~~~~~~~~~~~~~

You can configure this rule using the following option: ``threshold``.

Configuration
-------------

``threshold``
~~~~~~~~~~~~~

Maximum length in characters (excluding whitespaces) for single-line arrays. 0 :
always multiline, -1 : makes empty arrays multiline, null : always singleline.

Allowed types: ``int`` and ``null``

Default value: ``0``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$array = ['a very very long element','another very long element'];
   +$array = [
   +'a very very long element',
   +'another very long element'
   +];

Example #2
~~~~~~~~~~

With configuration: ``['threshold' => 10]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$array = ['a very very long element','another very long element'];
   +$array = [
   +'a very very long element',
   +'another very long element'
   +];

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\ArrayNotation\\ArraySinglelineMultilineFixer <./../../../src/Fixer/ArrayNotation/ArraySinglelineMultilineFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\ArrayNotation\\ArraySinglelineMultilineFixerTest <./../../../tests/Fixer/ArrayNotation/ArraySinglelineMultilineFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
