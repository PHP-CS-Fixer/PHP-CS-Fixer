============================================
Rule ``no_whitespace_before_comma_in_array``
============================================

In array declaration, there MUST NOT be a whitespace before each comma.

Warning
-------

This rule is CONFIGURABLE
~~~~~~~~~~~~~~~~~~~~~~~~~

You can configure this rule using the following option: ``after_heredoc``.

Configuration
-------------

``after_heredoc``
~~~~~~~~~~~~~~~~~

Whether the whitespace between heredoc end and comma should be removed.

Allowed types: ``bool``

Default value: ``false``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
   -<?php $x = array(1 , "2");
   +<?php $x = array(1, "2");

Example #2
~~~~~~~~~~

With configuration: ``['after_heredoc' => true]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
        $x = [<<<EOD
    foo
   -EOD
   -        , 'bar'
   +EOD, 'bar'
        ];

Rule sets
---------

The rule is part of the following rule sets:

- `@PHP7x3Migration <./../../ruleSets/PHP7x3Migration.rst>`_ with config:

  ``['after_heredoc' => true]``

- `@PHP7x4Migration <./../../ruleSets/PHP7x4Migration.rst>`_ with config:

  ``['after_heredoc' => true]``

- `@PHP8x0Migration <./../../ruleSets/PHP8x0Migration.rst>`_ with config:

  ``['after_heredoc' => true]``

- `@PHP8x1Migration <./../../ruleSets/PHP8x1Migration.rst>`_ with config:

  ``['after_heredoc' => true]``

- `@PHP8x2Migration <./../../ruleSets/PHP8x2Migration.rst>`_ with config:

  ``['after_heredoc' => true]``

- `@PHP8x3Migration <./../../ruleSets/PHP8x3Migration.rst>`_ with config:

  ``['after_heredoc' => true]``

- `@PHP8x4Migration <./../../ruleSets/PHP8x4Migration.rst>`_ with config:

  ``['after_heredoc' => true]``

- `@PHP8x5Migration <./../../ruleSets/PHP8x5Migration.rst>`_ with config:

  ``['after_heredoc' => true]``

- `@PHP73Migration <./../../ruleSets/PHP73Migration.rst>`_ *(deprecated)* with config:

  ``['after_heredoc' => true]``

- `@PHP74Migration <./../../ruleSets/PHP74Migration.rst>`_ *(deprecated)* with config:

  ``['after_heredoc' => true]``

- `@PHP80Migration <./../../ruleSets/PHP80Migration.rst>`_ *(deprecated)* with config:

  ``['after_heredoc' => true]``

- `@PHP81Migration <./../../ruleSets/PHP81Migration.rst>`_ *(deprecated)* with config:

  ``['after_heredoc' => true]``

- `@PHP82Migration <./../../ruleSets/PHP82Migration.rst>`_ *(deprecated)* with config:

  ``['after_heredoc' => true]``

- `@PHP83Migration <./../../ruleSets/PHP83Migration.rst>`_ *(deprecated)* with config:

  ``['after_heredoc' => true]``

- `@PHP84Migration <./../../ruleSets/PHP84Migration.rst>`_ *(deprecated)* with config:

  ``['after_heredoc' => true]``

- `@PHP85Migration <./../../ruleSets/PHP85Migration.rst>`_ *(deprecated)* with config:

  ``['after_heredoc' => true]``

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ with config:

  ``['after_heredoc' => true]``

- `@Symfony <./../../ruleSets/Symfony.rst>`_ with config:

  ``['after_heredoc' => true]``

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\ArrayNotation\\NoWhitespaceBeforeCommaInArrayFixer <./../../../src/Fixer/ArrayNotation/NoWhitespaceBeforeCommaInArrayFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\ArrayNotation\\NoWhitespaceBeforeCommaInArrayFixerTest <./../../../tests/Fixer/ArrayNotation/NoWhitespaceBeforeCommaInArrayFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
