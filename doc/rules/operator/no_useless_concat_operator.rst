===================================
Rule ``no_useless_concat_operator``
===================================

There should not be useless concat operations.

Configuration
-------------

``juggle_simple_strings``
~~~~~~~~~~~~~~~~~~~~~~~~~

Allow for simple string quote juggling if it results in more concat-operations
merges.

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
   -$a = 'a'.'b';
   +$a = 'ab';

Example #2
~~~~~~~~~~

With configuration: ``['juggle_simple_strings' => true]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$a = 'a'."b";
   +$a = "ab";

Rule sets
---------

The rule is part of the following rule sets:

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``no_useless_concat_operator`` rule with the default config.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``no_useless_concat_operator`` rule with the default config.
