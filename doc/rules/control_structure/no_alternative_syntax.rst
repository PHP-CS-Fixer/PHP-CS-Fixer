==============================
Rule ``no_alternative_syntax``
==============================

Replace control structure alternative syntax to use braces.

Configuration
-------------

``fix_non_monolithic_code``
~~~~~~~~~~~~~~~~~~~~~~~~~~~

Whether to also fix code with inline HTML.

Allowed types: ``bool``

Default value: ``true``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -if(true):echo 't';else:echo 'f';endif;
   +if(true) { echo 't';} else { echo 'f';}

Example #2
~~~~~~~~~~

With configuration: ``['fix_non_monolithic_code' => true]``.

.. code-block:: diff

   --- Original
   +++ New
   -<?php if ($condition): ?>
   +<?php if ($condition) { ?>
    Lorem ipsum.
   -<?php endif; ?>
   +<?php } ?>

Rule sets
---------

The rule is part of the following rule sets:

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``no_alternative_syntax`` rule with the default config.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``no_alternative_syntax`` rule with the default config.
