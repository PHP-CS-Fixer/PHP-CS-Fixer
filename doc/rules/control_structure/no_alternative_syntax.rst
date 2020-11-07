==============================
Rule ``no_alternative_syntax``
==============================

Replace control structure alternative syntax to use braces.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,2 +1,2 @@
    <?php
   -if(true):echo 't';else:echo 'f';endif;
   +if(true) { echo 't';} else { echo 'f';}

Example #2
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,2 +1,2 @@
    <?php
   -while(true):echo 'red';endwhile;
   +while(true) { echo 'red';}

Example #3
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,2 +1,2 @@
    <?php
   -for(;;):echo 'xc';endfor;
   +for(;;) { echo 'xc';}

Example #4
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,2 +1,2 @@
    <?php
   -foreach(array('a') as $item):echo 'xc';endforeach;
   +foreach(array('a') as $item) { echo 'xc';}

Rule sets
---------

The rule is part of the following rule set:

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``no_alternative_syntax`` rule.
