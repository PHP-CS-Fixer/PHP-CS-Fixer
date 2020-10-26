=============================
Rule ``no_unset_on_property``
=============================

Properties should be set to ``null`` instead of using ``unset``.

.. warning:: Using this rule is risky.

   Risky when relying on attributes to be removed using ``unset`` rather than be
   set to ``null``. Changing variables to ``null`` instead of unsetting means
   these still show up when looping over class variables and reference
   properties remain unbroken. With PHP 7.4, this rule might introduce ``null``
   assignments to properties whose type declaration does not allow it.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,2 +1,2 @@
    <?php
   -unset($this->a);
   +$this->a = null;

Rule sets
---------

The rule is part of the following rule set:

@PhpCsFixer:risky
  Using the ``@PhpCsFixer:risky`` rule set will enable the ``no_unset_on_property`` rule.
