==========================================
Rule ``trailing_comma_in_multiline_array``
==========================================

.. warning:: This rule is deprecated and will be removed on next major version.

   You should use ``trailing_comma_in_multiline`` instead.

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
    <?php
        $x = [
            'foo',
            <<<EOD
                bar
   -            EOD
   +            EOD,
        ];
