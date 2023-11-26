=================================
Rule ``no_unneeded_curly_braces``
=================================

Removes unneeded curly braces that are superfluous and aren't part of a control
structure's body.

Warning
-------

This rule is deprecated and will be removed in the next major version
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

You should use ``no_unneeded_braces`` instead.

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
Source class
------------

`PhpCsFixer\\Fixer\\ControlStructure\\NoUnneededCurlyBracesFixer <./../src/Fixer/ControlStructure/NoUnneededCurlyBracesFixer.php>`_
