======================
Rule ``pre_increment``
======================

.. warning:: This rule is deprecated and will be removed on next major version.

   You should use ``increment_style`` instead.

Pre incrementation/decrementation should be used if possible.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$a++;
   -$b--;
   +++$a;
   +--$b;
