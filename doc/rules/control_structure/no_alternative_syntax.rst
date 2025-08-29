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

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\ControlStructure\\NoAlternativeSyntaxFixer <./../../../src/Fixer/ControlStructure/NoAlternativeSyntaxFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\ControlStructure\\NoAlternativeSyntaxFixerTest <./../../../tests/Fixer/ControlStructure/NoAlternativeSyntaxFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
