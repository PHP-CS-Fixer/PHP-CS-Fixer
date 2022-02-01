============================
Rule ``date_time_immutable``
============================

Class ``DateTimeImmutable`` should be used instead of ``DateTime``.

.. warning:: Using this rule is risky.

   Risky when the code relies on modifying ``DateTime`` objects or if any of the
   ``date_create*`` functions are overridden.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -new DateTime();
   +new DateTimeImmutable();
