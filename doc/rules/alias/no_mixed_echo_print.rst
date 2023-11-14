============================
Rule ``no_mixed_echo_print``
============================

Either language construct ``print`` or ``echo`` should be used.

Configuration
-------------

``use``
~~~~~~~

The desired language construct.

Allowed values: ``'echo'`` and ``'print'``

Default value: ``'echo'``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
   -<?php print 'example';
   +<?php echo 'example';

Example #2
~~~~~~~~~~

With configuration: ``['use' => 'print']``.

.. code-block:: diff

   --- Original
   +++ New
   -<?php echo('example');
   +<?php print('example');

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

Source class
------------

`PhpCsFixer\\Fixer\\Alias\\NoMixedEchoPrintFixer <./../src/Fixer/Alias/NoMixedEchoPrintFixer.php>`_
