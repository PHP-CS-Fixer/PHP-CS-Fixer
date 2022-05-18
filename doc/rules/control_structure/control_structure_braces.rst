=================================
Rule ``control_structure_braces``
=================================

The body of each control structure MUST be enclosed within braces.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -if (foo()) echo 'Hello!';
   +if (foo()) { echo 'Hello!'; }
