==========================
Rule ``no_short_echo_tag``
==========================

.. warning:: This rule is deprecated and will be removed on next major version.

   You should use ``echo_tag_syntax`` instead.

Replaces short-echo ``<?=`` with long format ``<?php echo`` syntax.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   -<?= "foo";
   +<?php echo "foo";
