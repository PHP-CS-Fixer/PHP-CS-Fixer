==========================================
Rule ``trailing_comma_in_multiline_array``
==========================================

PHP multi-line arrays should have a trailing comma.

Configuration
-------------

``after_heredoc``
~~~~~~~~~~~~~~~~~

Whether a trailing comma should also be placed after heredoc end.

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
   @@ -1,5 +1,5 @@
    <?php
    array(
        1,
   -    2
   +    2,
    );

Example #2
~~~~~~~~~~

With configuration: ``['after_heredoc' => true]``.

.. code-block:: diff

   --- Original
   +++ New
   @@ -3,5 +3,5 @@
            'foo',
            <<<EOD
                bar
   -            EOD
   +            EOD,
        ];

Rule sets
---------

The rule is part of the following rule sets:

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``trailing_comma_in_multiline_array`` rule with the default config.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``trailing_comma_in_multiline_array`` rule with the default config.
