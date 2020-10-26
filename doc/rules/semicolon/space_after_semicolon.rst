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
   @@ -1,5 +1,5 @@
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
   @@ -1,3 +1,3 @@
    <?php
   -for ($i = 0; ; ++$i) {
   +for ($i = 0;; ++$i) {
    }

Rule sets
---------

The rule is part of the following rule sets:

@Symfony
  Using the ``@Symfony`` rule set will enable the ``space_after_semicolon`` rule with the config below:

  ``['remove_in_empty_for_expressions' => true]``

@PhpCsFixer
  Using the ``@PhpCsFixer`` rule set will enable the ``space_after_semicolon`` rule with the config below:

  ``['remove_in_empty_for_expressions' => true]``
