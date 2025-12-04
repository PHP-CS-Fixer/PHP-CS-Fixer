===============================
Rule ``heredoc_closing_marker``
===============================

Unify ``heredoc`` or ``nowdoc`` closing marker.

Warning
-------

This rule is CONFIGURABLE
~~~~~~~~~~~~~~~~~~~~~~~~~

You can configure this rule using the following options: ``closing_marker``,
``explicit_heredoc_style``, ``reserved_closing_markers``.

Configuration
-------------

``closing_marker``
~~~~~~~~~~~~~~~~~~

Preferred closing marker.

Allowed types: ``string``

Default value: ``'EOD'``

``explicit_heredoc_style``
~~~~~~~~~~~~~~~~~~~~~~~~~~

Whether the closing marker should be wrapped in double quotes.

Allowed types: ``bool``

Default value: ``false``

``reserved_closing_markers``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Reserved closing markers to be kept unchanged.

Allowed types: ``list<string>``

Default value: ``['CSS', 'DIFF', 'HTML', 'JS', 'JSON', 'MD', 'PHP', 'PYTHON', 'RST', 'TS', 'SQL', 'XML', 'YAML']``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
   -<?php $a = <<<"TEST"
   +<?php $a = <<<EOD
    Foo
   -TEST;
   +EOD;

Example #2
~~~~~~~~~~

With configuration: ``['closing_marker' => 'EOF']``.

.. code-block:: diff

   --- Original
   +++ New
   -<?php $a = <<<'TEST'
   +<?php $a = <<<'EOF'
    Foo
   -TEST;
   +EOF;

Example #3
~~~~~~~~~~

With configuration: ``['explicit_heredoc_style' => true]``.

.. code-block:: diff

   --- Original
   +++ New
   -<?php $a = <<<EOD
   +<?php $a = <<<"EOD"
    Foo
    EOD;

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\StringNotation\\HeredocClosingMarkerFixer <./../../../src/Fixer/StringNotation/HeredocClosingMarkerFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\StringNotation\\HeredocClosingMarkerFixerTest <./../../../tests/Fixer/StringNotation/HeredocClosingMarkerFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
