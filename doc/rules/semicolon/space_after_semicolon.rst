==============================
Rule ``space_after_semicolon``
==============================

Fix whitespace after a semicolon.

Configuration
-------------

``remove_in_empty_for_expressions``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Whether spaces should be removed for empty ``for`` expressions.

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
   -                        sample();     $test = 1;
   -                        sample();$test = 2;
   -                        for ( ;;++$sample) {
   +                        sample(); $test = 1;
   +                        sample(); $test = 2;
   +                        for ( ; ; ++$sample) {
                            }

Example #2
~~~~~~~~~~

With configuration: ``['remove_in_empty_for_expressions' => true]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -for ($i = 0; ; ++$i) {
   +for ($i = 0;; ++$i) {
    }

Rule sets
---------

The rule is part of the following rule sets:

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``space_after_semicolon`` rule with the config below:

  ``['remove_in_empty_for_expressions' => true]``

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``space_after_semicolon`` rule with the config below:

  ``['remove_in_empty_for_expressions' => true]``
