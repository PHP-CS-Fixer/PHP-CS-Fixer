=====================================
Rule ``blank_lines_before_namespace``
=====================================

Controls blank lines before a namespace declaration.

Configuration
-------------

``min_line_breaks``
~~~~~~~~~~~~~~~~~~~

Minimum line breaks that should exist before namespace declaration.

Allowed types: ``int``

Default value: ``2``

``max_line_breaks``
~~~~~~~~~~~~~~~~~~~

Maximum line breaks that should exist before namespace declaration.

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

   -
    namespace A{}
