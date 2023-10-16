============================================
Rule ``no_whitespace_before_comma_in_array``
============================================

In array declaration, there MUST NOT be a whitespace before each comma.

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

- `@PHP73Migration <./../../ruleSets/PHP73Migration.rst>`_ with config:

  ``['after_heredoc' => true]``

- `@PHP74Migration <./../../ruleSets/PHP74Migration.rst>`_ with config:

  ``['after_heredoc' => true]``

- `@PHP80Migration <./../../ruleSets/PHP80Migration.rst>`_ with config:

  ``['after_heredoc' => true]``

- `@PHP81Migration <./../../ruleSets/PHP81Migration.rst>`_ with config:

  ``['after_heredoc' => true]``

- `@PHP82Migration <./../../ruleSets/PHP82Migration.rst>`_ with config:

  ``['after_heredoc' => true]``

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

