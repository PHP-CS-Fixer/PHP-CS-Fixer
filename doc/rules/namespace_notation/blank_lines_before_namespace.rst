=====================================
Rule ``blank_lines_before_namespace``
=====================================

Controls blank lines before a namespace declaration.

Configuration
-------------

``max_line_breaks``
~~~~~~~~~~~~~~~~~~~

Maximum line breaks that should exist before namespace declaration.

Allowed types: ``int``

Default value: ``2``

``min_line_breaks``
~~~~~~~~~~~~~~~~~~~

Minimum line breaks that should exist before namespace declaration.

Allowed types: ``int``

Default value: ``2``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
   -<?php  namespace A {}
   +<?php
   +
   +namespace A {}

Example #2
~~~~~~~~~~

With configuration: ``['min_line_breaks' => 1]``.

.. code-block:: diff

   --- Original
   +++ New
   -<?php  namespace A {}
   +<?php
   +namespace A {}

Example #3
~~~~~~~~~~

With configuration: ``['max_line_breaks' => 2]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php

    declare(strict_types=1);

   -
   -
    namespace A{}

Example #4
~~~~~~~~~~

With configuration: ``['min_line_breaks' => 2]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php

    /** Some comment */
   +
    namespace A{}

Example #5
~~~~~~~~~~

With configuration: ``['min_line_breaks' => 0, 'max_line_breaks' => 0]``.

.. code-block:: diff

   --- Original
   +++ New
   -<?php
   -
   -namespace A{}
   +<?php namespace A{}

Rule sets
---------

The rule is part of the following rule sets:

- `@PER <./../../ruleSets/PER.rst>`_
- `@PER-CS <./../../ruleSets/PER-CS.rst>`_
- `@PER-CS1.0 <./../../ruleSets/PER-CS1.0.rst>`_
- `@PER-CS2.0 <./../../ruleSets/PER-CS2.0.rst>`_
- `@PSR12 <./../../ruleSets/PSR12.rst>`_
- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\NamespaceNotation\\BlankLinesBeforeNamespaceFixer <./../../../src/Fixer/NamespaceNotation/BlankLinesBeforeNamespaceFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\NamespaceNotation\\BlankLinesBeforeNamespaceFixerTest <./../../../tests/Fixer/NamespaceNotation/BlankLinesBeforeNamespaceFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
