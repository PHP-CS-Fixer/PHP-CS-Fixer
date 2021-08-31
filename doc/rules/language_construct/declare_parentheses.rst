============================
Rule ``declare_parentheses``
============================

There must not be spaces around ``declare`` statement parentheses.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   -<?php declare ( strict_types=1 );
   +<?php declare(strict_types=1);
