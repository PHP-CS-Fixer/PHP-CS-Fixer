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
   @@ -5,6 +5,6 @@

        public function getBar()
        {
   -        return static::class.static::test().static::$A;
   +        return self::class.self::test().self::$A;
        }

Example #2
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -3,6 +3,6 @@
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
   @@ -3,6 +3,6 @@
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
   @@ -2,6 +2,6 @@
    $a = new class() {
        public function getBar()
        {
   -        return static::class;
   +        return self::class;
        }
    };
