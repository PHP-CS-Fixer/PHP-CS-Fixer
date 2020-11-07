=================================
Rule ``no_unneeded_curly_braces``
=================================

Removes unneeded curly braces that are superfluous and aren't part of a control
structure's body.

Configuration
-------------

``namespaces``
~~~~~~~~~~~~~~

Remove unneeded curly braces from bracketed namespaces.

Allowed types: ``bool``

Default value: ``false``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,9 +1,9 @@
   -<?php {
   +<?php 
        echo 1;
   -}

   +
    switch ($b) {
   -    case 1: {
   +    case 1: 
            break;
   -    }
   +    
    }

Example #2
~~~~~~~~~~

With configuration: ``['namespaces' => true]``.

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,4 +1,4 @@
    <?php
   -namespace Foo {
   +namespace Foo;
        function Bar(){}
   -}
   +

Rule sets
---------

The rule is part of the following rule sets:

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``no_unneeded_curly_braces`` rule with the config below:

  ``['namespaces' => true]``

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``no_unneeded_curly_braces`` rule with the config below:

  ``['namespaces' => true]``
