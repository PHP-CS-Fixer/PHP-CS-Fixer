===================================
Rule ``lowercase_static_reference``
===================================

Class static references ``self``, ``static`` and ``parent`` MUST be in lower
case.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
    class Foo extends Bar
    {
        public function baz1()
        {
   -        return STATIC::baz2();
   +        return static::baz2();
        }

        public function baz2($x)
        {
   -        return $x instanceof Self;
   +        return $x instanceof self;
        }

   -    public function baz3(PaRent $x)
   +    public function baz3(parent $x)
        {
            return true;
        }
    }

Example #2
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
    class Foo extends Bar
    {
   -    public function baz(?self $x) : SELF
   +    public function baz(?self $x) : self
        {
            return false;
        }
    }

Rule sets
---------

The rule is part of the following rule sets:

- `@PER <./../../ruleSets/PER.rst>`_
- `@PER-CS1.0 <./../../ruleSets/PER-CS1.0.rst>`_
- `@PER-CS2.0 <./../../ruleSets/PER-CS2.0.rst>`_
- `@PSR12 <./../../ruleSets/PSR12.rst>`_
- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

