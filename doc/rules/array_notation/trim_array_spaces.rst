==========================
Rule ``trim_array_spaces``
==========================

Arrays should be formatted like function/method arguments, without leading or
trailing single line space.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$sample = array( );
   -$sample = array( 'a', 'b' );
   +$sample = array();
   +$sample = array('a', 'b');

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

