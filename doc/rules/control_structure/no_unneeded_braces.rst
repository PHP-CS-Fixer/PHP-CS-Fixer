===========================
Rule ``no_unneeded_braces``
===========================

Removes unneeded braces that are superfluous and aren't part of a control
structure's body.

Configuration
-------------

``namespaces``
~~~~~~~~~~~~~~

Remove unneeded braces from bracketed namespaces.

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
    <?php
   -namespace Foo {
   +namespace Foo;
        function Bar(){}
   -}
   +

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ with config:

  ``['namespaces' => true]``

- `@Symfony <./../../ruleSets/Symfony.rst>`_ with config:

  ``['namespaces' => true]``


Source class
------------

`PhpCsFixer\\Fixer\\ControlStructure\\NoUnneededBracesFixer <./../src/Fixer/ControlStructure/NoUnneededBracesFixer.php>`_
