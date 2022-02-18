====================
Rule ``final_class``
====================

All classes must be final, except abstract ones and Doctrine entities.

Description
-----------

No exception and no configuration are intentional. Beside Doctrine entities and
of course abstract classes, there is no single reason not to declare all classes
final. If you want to subclass a class, mark the parent class as abstract and
create two child classes, one empty if necessary: you'll gain much more fine
grained type-hinting. If you need to mock a standalone class, create an
interface, or maybe it's a value-object that shouldn't be mocked at all. If you
need to extend a standalone class, create an interface and use the Composite
pattern. If you aren't ready yet for serious OOP, go with
FinalInternalClassFixer, it's fine.

Warning
-------

Using this rule is risky
~~~~~~~~~~~~~~~~~~~~~~~~

Risky when subclassing non-abstract classes.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -class MyApp {}
   +final class MyApp {}
