=============
Rule ``psr0``
=============

Classes must be in a path that matches their namespace, be at least one
namespace deep and the class name should match the file name.

.. warning:: Using this rule is risky.

   This fixer may change your class name, which will break the code that depends
   on the old name.

Configuration
-------------

``dir``
~~~~~~~

The directory where the project code is placed.

Allowed types: ``string``

Default value: ``''``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,3 +1,3 @@
    <?php
    namespace PhpCsFixer\FIXER\Basic;
   -class InvalidName {}
   +class Psr0Fixer {}

Example #2
~~~~~~~~~~

With configuration: ``['dir' => './src']``.

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,3 +1,3 @@
    <?php
   -namespace PhpCsFixer\FIXER\Basic;
   -class InvalidName {}
   +namespace PhpCsFixer\Fixer\Basic;
   +class Psr0Fixer {}
