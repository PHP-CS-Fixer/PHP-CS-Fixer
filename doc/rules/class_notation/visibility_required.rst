============================
Rule ``visibility_required``
============================

Classes, constants, properties, and methods MUST have visibility declared, and
keyword modifiers MUST be in the following order: inheritance modifier
(``abstract`` or ``final``), visibility modifier (``public``, ``protected``, or
``private``), set-visibility modifier (``public(set)``, ``protected(set)``, or
``private(set)``), scope modifier (``static``), mutation modifier
(``readonly``), type declaration, name.

Configuration
-------------

``elements``
~~~~~~~~~~~~

The structural elements to fix (PHP >= 7.1 required for ``const``).

Allowed values: a subset of ``['const', 'method', 'property']``

Default value: ``['const', 'method', 'property']``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    abstract class ClassName
    {
   -    const SAMPLE = 1;
   +    public const SAMPLE = 1;

   -    var $a;
   +    public $a;

        protected string $foo;

   -    static protected int $beep;
   +    protected static int $beep;

   -    static public final function bar() {}
   +    final public static function bar() {}

   -    protected abstract function zim();
   +    abstract protected function zim();

   -    function zex() {}
   +    public function zex() {}
    }

Example #2
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    abstract class ClassName
    {
   -    const SAMPLE = 1;
   +    public const SAMPLE = 1;

   -    var $a;
   +    public $a;

   -    readonly protected string $foo;
   +    protected readonly string $foo;

   -    static protected int $beep;
   +    protected static int $beep;

   -    static public final function bar() {}
   +    final public static function bar() {}

   -    protected abstract function zim();
   +    abstract protected function zim();

   -    function zex() {}
   +    public function zex() {}
    }

    readonly final class ValueObject
    {
        // ...
    }

Example #3
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    abstract class ClassName
    {
   -    const SAMPLE = 1;
   +    public const SAMPLE = 1;

   -    var $a;
   +    public $a;

   -    protected abstract string $bar { get => "a"; set; }
   +    abstract protected string $bar { get => "a"; set; }

   -    readonly final protected string $foo;
   +    final protected readonly string $foo;

   -    static protected final int $beep;
   +    final protected static int $beep;

   -    static public final function bar() {}
   +    final public static function bar() {}

   -    protected abstract function zim();
   +    abstract protected function zim();

   -    function zex() {}
   +    public function zex() {}
    }

    readonly final class ValueObject
    {
        // ...
    }

Example #4
~~~~~~~~~~

With configuration: ``['elements' => ['const']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    class Sample
    {
   -    const SAMPLE = 1;
   +    public const SAMPLE = 1;
    }

Rule sets
---------

The rule is part of the following rule sets:

- `@PER <./../../ruleSets/PER.rst>`_
- `@PER-CS <./../../ruleSets/PER-CS.rst>`_
- `@PER-CS1.0 <./../../ruleSets/PER-CS1.0.rst>`_
- `@PER-CS2.0 <./../../ruleSets/PER-CS2.0.rst>`_
- `@PER-CS3.0 <./../../ruleSets/PER-CS3.0.rst>`_
- `@PHP71Migration <./../../ruleSets/PHP71Migration.rst>`_
- `@PHP73Migration <./../../ruleSets/PHP73Migration.rst>`_
- `@PHP74Migration <./../../ruleSets/PHP74Migration.rst>`_
- `@PHP80Migration <./../../ruleSets/PHP80Migration.rst>`_
- `@PHP81Migration <./../../ruleSets/PHP81Migration.rst>`_
- `@PHP82Migration <./../../ruleSets/PHP82Migration.rst>`_
- `@PHP83Migration <./../../ruleSets/PHP83Migration.rst>`_
- `@PHP84Migration <./../../ruleSets/PHP84Migration.rst>`_
- `@PSR2 <./../../ruleSets/PSR2.rst>`_ with config:

  ``['elements' => ['method', 'property']]``

- `@PSR12 <./../../ruleSets/PSR12.rst>`_
- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\ClassNotation\\VisibilityRequiredFixer <./../../../src/Fixer/ClassNotation/VisibilityRequiredFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\ClassNotation\\VisibilityRequiredFixerTest <./../../../tests/Fixer/ClassNotation/VisibilityRequiredFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
