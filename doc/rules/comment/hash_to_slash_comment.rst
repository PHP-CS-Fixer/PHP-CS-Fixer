==============================
Rule ``hash_to_slash_comment``
==============================

.. warning:: This rule is deprecated and will be removed on next major version.

   You should use ``single_line_comment_style`` instead.

Single line comments should use double slashes ``//`` and not hash ``#``.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   -<?php # comment
   +<?php // comment
