===========================
Rule ``ordered_interfaces``
===========================

Orders the interfaces in an ``implements`` or ``interface extends`` clause.

.. warning:: Using this rule is risky.

   Risky for ``implements`` when specifying both an interface and its parent
   interface, because PHP doesn't break on ``parent, child`` but does on
   ``child, parent``.

Configuration
-------------

``order``
~~~~~~~~~

How the interfaces should be ordered

Allowed values: ``'alpha'``, ``'length'``

Default value: ``'alpha'``

``direction``
~~~~~~~~~~~~~

Which direction the interfaces should be ordered

Allowed values: ``'ascend'``, ``'descend'``

Default value: ``'ascend'``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php

   -final class ExampleA implements Gamma, Alpha, Beta {}
   +final class ExampleA implements Alpha, Beta, Gamma {}

   -interface ExampleB extends Gamma, Alpha, Beta {}
   +interface ExampleB extends Alpha, Beta, Gamma {}

Example #2
~~~~~~~~~~

With configuration: ``['direction' => 'descend']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php

   -final class ExampleA implements Gamma, Alpha, Beta {}
   +final class ExampleA implements Gamma, Beta, Alpha {}

   -interface ExampleB extends Gamma, Alpha, Beta {}
   +interface ExampleB extends Gamma, Beta, Alpha {}

Example #3
~~~~~~~~~~

With configuration: ``['order' => 'length']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php

   -final class ExampleA implements MuchLonger, Short, Longer {}
   +final class ExampleA implements Short, Longer, MuchLonger {}

   -interface ExampleB extends MuchLonger, Short, Longer {}
   +interface ExampleB extends Short, Longer, MuchLonger {}

Example #4
~~~~~~~~~~

With configuration: ``['order' => 'length', 'direction' => 'descend']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php

   -final class ExampleA implements MuchLonger, Short, Longer {}
   +final class ExampleA implements MuchLonger, Longer, Short {}

   -interface ExampleB extends MuchLonger, Short, Longer {}
   +interface ExampleB extends MuchLonger, Longer, Short {}
