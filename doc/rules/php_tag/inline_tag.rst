===================
Rule ``inline_tag``
===================

Changes spaces and semicolons in inline PHP tags.

Configuration
-------------

``space_before``
~~~~~~~~~~~~~~~~

The desired number of spaces at the beginning of the tag.

Allowed values: ``'keep'``, ``'minimum'``, ``'one'``

Default value: ``'one'``

``space_after``
~~~~~~~~~~~~~~~

The desired number of spaces at the end of the tag.

Allowed values: ``'keep'``, ``'minimum'``, ``'one'``

Default value: ``'one'``

``semicolon``
~~~~~~~~~~~~~

Whether there should be a semi-colon at the end.

Allowed types: ``bool``, ``null``

Default value: ``false``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,4 +1,4 @@
   -<?=1?> <?= 1 ?> <?=  1  ?>
   -<?=2;?> <?= 2; ?> <?=  2;  ?>
   -<?=3  ;?> <?= 3  ;?> <?=3  ;?> <?=  3  ;  ?>
   -<?php   echo 4  ;  ?>
   +<?= 1 ?> <?= 1 ?> <?= 1 ?>
   +<?= 2 ?> <?= 2 ?> <?= 2 ?>
   +<?= 3 ?> <?= 3 ?> <?= 3 ?> <?= 3 ?>
   +<?php echo 4 ?>

Example #2
~~~~~~~~~~

With configuration: ``['space_before' => 'keep']``.

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,4 +1,4 @@
   -<?=1?> <?= 1 ?> <?=  1  ?>
   -<?=2;?> <?= 2; ?> <?=  2;  ?>
   -<?=3  ;?> <?= 3  ;?> <?=3  ;?> <?=  3  ;  ?>
   -<?php   echo 4  ;  ?>
   +<?=1 ?> <?= 1 ?> <?=  1 ?>
   +<?=2 ?> <?= 2 ?> <?=  2 ?>
   +<?=3 ?> <?= 3 ?> <?=3 ?> <?=  3 ?>
   +<?php   echo 4 ?>

Example #3
~~~~~~~~~~

With configuration: ``['space_before' => 'minimum']``.

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,4 +1,4 @@
   -<?=1?> <?= 1 ?> <?=  1  ?>
   -<?=2;?> <?= 2; ?> <?=  2;  ?>
   -<?=3  ;?> <?= 3  ;?> <?=3  ;?> <?=  3  ;  ?>
   -<?php   echo 4  ;  ?>
   +<?=1 ?> <?=1 ?> <?=1 ?>
   +<?=2 ?> <?=2 ?> <?=2 ?>
   +<?=3 ?> <?=3 ?> <?=3 ?> <?=3 ?>
   +<?php echo 4 ?>

Example #4
~~~~~~~~~~

With configuration: ``['space_after' => 'keep']``.

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,4 +1,4 @@
   -<?=1?> <?= 1 ?> <?=  1  ?>
   -<?=2;?> <?= 2; ?> <?=  2;  ?>
   -<?=3  ;?> <?= 3  ;?> <?=3  ;?> <?=  3  ;  ?>
   -<?php   echo 4  ;  ?>
   +<?= 1?> <?= 1 ?> <?= 1  ?>
   +<?= 2?> <?= 2 ?> <?= 2  ?>
   +<?= 3  ?> <?= 3  ?> <?= 3  ?> <?= 3    ?>
   +<?php echo 4    ?>

Example #5
~~~~~~~~~~

With configuration: ``['space_after' => 'minimum']``.

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,4 +1,4 @@
   -<?=1?> <?= 1 ?> <?=  1  ?>
   -<?=2;?> <?= 2; ?> <?=  2;  ?>
   -<?=3  ;?> <?= 3  ;?> <?=3  ;?> <?=  3  ;  ?>
   -<?php   echo 4  ;  ?>
   +<?= 1?> <?= 1?> <?= 1?>
   +<?= 2?> <?= 2?> <?= 2?>
   +<?= 3?> <?= 3?> <?= 3?> <?= 3?>
   +<?php echo 4?>

Example #6
~~~~~~~~~~

With configuration: ``['semicolon' => null]``.

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,4 +1,4 @@
   -<?=1?> <?= 1 ?> <?=  1  ?>
   -<?=2;?> <?= 2; ?> <?=  2;  ?>
   -<?=3  ;?> <?= 3  ;?> <?=3  ;?> <?=  3  ;  ?>
   -<?php   echo 4  ;  ?>
   +<?= 1 ?> <?= 1 ?> <?= 1 ?>
   +<?= 2; ?> <?= 2; ?> <?= 2; ?>
   +<?= 3; ?> <?= 3; ?> <?= 3; ?> <?= 3; ?>
   +<?php echo 4; ?>

Example #7
~~~~~~~~~~

With configuration: ``['semicolon' => true]``.

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,4 +1,4 @@
   -<?=1?> <?= 1 ?> <?=  1  ?>
   -<?=2;?> <?= 2; ?> <?=  2;  ?>
   -<?=3  ;?> <?= 3  ;?> <?=3  ;?> <?=  3  ;  ?>
   -<?php   echo 4  ;  ?>
   +<?= 1; ?> <?= 1; ?> <?= 1; ?>
   +<?= 2; ?> <?= 2; ?> <?= 2; ?>
   +<?= 3; ?> <?= 3; ?> <?= 3; ?> <?= 3; ?>
   +<?php echo 4; ?>
