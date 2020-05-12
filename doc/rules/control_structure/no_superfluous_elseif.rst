==============================
Rule ``no_superfluous_elseif``
==============================

Replaces superfluous ``elseif`` with ``if``.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
    if ($a) {
        return 1;
   -} elseif ($b) {
   +}
   +if ($b) {
        return 2;
    }

Rule sets
---------

The rule is part of the following rule set:

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``no_superfluous_elseif`` rule.
