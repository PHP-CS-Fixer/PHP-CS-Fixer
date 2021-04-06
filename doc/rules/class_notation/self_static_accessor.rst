=============================
Rule ``self_static_accessor``
=============================

Inside a ``final`` class or anonymous class ``self`` should be preferred to
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
