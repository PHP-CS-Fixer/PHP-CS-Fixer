===============================
Rule ``heredoc_closing_marker``
===============================

Unify ``heredoc`` or ``nowdoc`` closing marker.

Configuration
-------------

``closing_marker``
~~~~~~~~~~~~~~~~~~

Preferred closing marker.

Allowed types: ``string``

Default value: ``'EOD'``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
   -<?php $a = <<<"TEST"
   +<?php $a = <<<"EOD"
    Foo
   -TEST;
   +EOD;

Example #2
~~~~~~~~~~

With configuration: ``['closing_marker' => 'EOF']``.

.. code-block:: diff

   --- Original
   +++ New
   -<?php $a = <<<"TEST"
   +<?php $a = <<<"EOF"
    Foo
   -TEST;
   +EOF;
Source class
------------

`PhpCsFixer\\Fixer\\StringNotation\\HeredocClosingMarkerFixer <./../../../src/Fixer/StringNotation/HeredocClosingMarkerFixer.php>`_
