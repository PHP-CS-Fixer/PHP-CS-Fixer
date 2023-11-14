=============================
Rule ``empty_loop_condition``
=============================

Empty loop-condition must be in configured style.

Configuration
-------------

``style``
~~~~~~~~~

Style of empty loop-condition.

Allowed values: ``'for'`` and ``'while'``

Default value: ``'while'``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -for(;;) {
   +while (true) {
        foo();
    }

   -do {
   +while (true) {
        foo();
   -} while(true); // do while
   +}  // do while

Example #2
~~~~~~~~~~

With configuration: ``['style' => 'for']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -while(true) {
   +for(;;) {
        foo();
    }

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

Source class
------------

`PhpCsFixer\\Fixer\\ControlStructure\\EmptyLoopConditionFixer <./../src/Fixer/ControlStructure/EmptyLoopConditionFixer.php>`_
