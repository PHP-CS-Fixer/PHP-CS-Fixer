============================
Rule ``no_mixed_echo_print``
============================

Either language construct ``print`` or ``echo`` should be used.

Configuration
-------------

``use``
~~~~~~~

The desired language construct.

Allowed values: ``'echo'``, ``'print'``

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

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``no_mixed_echo_print`` rule with the default config.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``no_mixed_echo_print`` rule with the default config.
