============================
Rule ``date_time_immutable``
============================

Class ``DateTimeImmutable`` should be used instead of ``DateTime``.

Warning
-------

Using this rule is risky
~~~~~~~~~~~~~~~~~~~~~~~~

Risky when the code relies on modifying ``DateTime`` objects or if any of the
``date_create*`` functions are overridden.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -new DateTime();
   +new DateTimeImmutable();

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\ClassUsage\\DateTimeImmutableFixer <./../../../src/Fixer/ClassUsage/DateTimeImmutableFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\ClassUsage\\DateTimeImmutableFixerTest <./../../../tests/Fixer/ClassUsage/DateTimeImmutableFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
