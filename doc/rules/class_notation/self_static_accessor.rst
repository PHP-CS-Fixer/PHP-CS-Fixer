=============================
Rule ``self_static_accessor``
=============================

Inside an enum or ``final``/anonymous class, ``self`` should be preferred over
``static``.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
    final class Sample
    {
        private static $A = 1;

        public function getBar()
        {
   -        return static::class.static::test().static::$A;
   +        return self::class.self::test().self::$A;
        }

        private static function test()
        {
            return 'test';
        }
    }

Example #2
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
    final class Foo
    {
        public function bar()
        {
   -        return new static();
   +        return new self();
        }
    }

Example #3
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
    final class Foo
    {
        public function isBar()
        {
   -        return $foo instanceof static;
   +        return $foo instanceof self;
        }
    }

Example #4
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
    $a = new class() {
        public function getBar()
        {
   -        return static::class;
   +        return self::class;
        }
    };

Example #5
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
    enum Foo
    {
        public const A = 123;

        public static function bar(): void
        {
   -        echo static::A;
   +        echo self::A;
        }
    }

Rule sets
---------

The rule is part of the following rule set:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_

Source class
------------

`PhpCsFixer\\Fixer\\ClassNotation\\SelfStaticAccessorFixer <./../../../src/Fixer/ClassNotation/SelfStaticAccessorFixer.php>`_
