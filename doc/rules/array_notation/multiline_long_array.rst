=============================
Rule ``multiline_long_array``
=============================

A single-line array should be broken into multiple lines if it exceeds
configured limit. Arrays that contain comments should be left unchanged.

Configuration
-------------

``characters_threshold``
~~~~~~~~~~~~~~~~~~~~~~~~

Maximum length in characters (excluding whitespaces) for single-line arrays. 0 :
always multi-line, -1 : always single-line.

Allowed types: ``int``

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

With configuration: ``['characters_threshold' => 10]``.

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

- Fixer class: `PhpCsFixer\\Fixer\\ArrayNotation\\MultilineLongArrayFixer <./../../../src/Fixer/ArrayNotation/MultilineLongArrayFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\ArrayNotation\\MultilineLongArrayFixerTest <./../../../tests/Fixer/ArrayNotation/MultilineLongArrayFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
