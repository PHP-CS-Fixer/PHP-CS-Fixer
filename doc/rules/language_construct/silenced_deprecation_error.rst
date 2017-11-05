===================================
Rule ``silenced_deprecation_error``
===================================

.. warning:: This rule is deprecated and will be removed on next major version.

   You should use ``error_suppression`` instead.

Ensures deprecation notices are silenced.

.. warning:: Using this rule is risky.

   Silencing of deprecation errors might cause changes to code behaviour.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,2 +1,2 @@
    <?php
   -trigger_error('Warning.', E_USER_DEPRECATED);
   +@trigger_error('Warning.', E_USER_DEPRECATED);
