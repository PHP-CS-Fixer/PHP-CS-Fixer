===========================
Rule ``ordered_interfaces``
===========================

Orders the interfaces in an ``implements`` or ``interface extends`` clause.

Configuration
-------------

``case_sensitive``
~~~~~~~~~~~~~~~~~~

Whether the sorting should be case sensitive.

Allowed types: ``bool``

Default value: ``false``

``direction``
~~~~~~~~~~~~~

Which direction the interfaces should be ordered.

Allowed values: ``'ascend'`` and ``'descend'``

Default value: ``'ascend'``

``order``
~~~~~~~~~

How the interfaces should be ordered.

Allowed values: ``'alpha'`` and ``'length'``

Default value: ``'alpha'``

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

Example #5
~~~~~~~~~~

With configuration: ``['order' => 'alpha']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php

   -final class ExampleA implements IgnorecaseB, IgNoReCaSeA, IgnoreCaseC {}
   +final class ExampleA implements IgNoReCaSeA, IgnorecaseB, IgnoreCaseC {}

   -interface ExampleB extends IgnorecaseB, IgNoReCaSeA, IgnoreCaseC {}
   +interface ExampleB extends IgNoReCaSeA, IgnorecaseB, IgnoreCaseC {}

Example #6
~~~~~~~~~~

With configuration: ``['order' => 'alpha', 'case_sensitive' => true]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php

   -final class ExampleA implements Casesensitivea, CaseSensitiveA, CasesensitiveA {}
   +final class ExampleA implements CaseSensitiveA, CasesensitiveA, Casesensitivea {}

   -interface ExampleB extends Casesensitivea, CaseSensitiveA, CasesensitiveA {}
   +interface ExampleB extends CaseSensitiveA, CasesensitiveA, Casesensitivea {}
Source class
------------

`PhpCsFixer\\Fixer\\ClassNotation\\OrderedInterfacesFixer <./../src/Fixer/ClassNotation/OrderedInterfacesFixer.php>`_
