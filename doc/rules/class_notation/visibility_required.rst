============================
Rule ``visibility_required``
============================

Classes, constants, properties, and methods keyword modifiers MUST be in the
following order: inheritance modifier (``abstract`` or ``final``), visibility
modifier (``public``, ``protected``, or ``private``), scope modifier
(``static``), mutation modifier (``readonly``), type declaration, name.

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
    class Sample
    {
   -    var $a;
   -    static protected $var_foo2;
   +    public $a;
   +    protected static $var_foo2;

   -    function A()
   +    public function A()
        {
        }
    }

Example #2
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

Example #3
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php
        abstract class ClassName
        {
            protected string $foo;

            protected int $beep;

   -        static public final function bar() {}
   +        final public static function bar() {}

   -        protected abstract function zim();
   +        abstract protected function zim();
        }

Example #4
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    abstract class ClassName
    {
   -    readonly protected string $foo;
   +    protected readonly string $foo;

        protected int $beep;

   -    static public final function bar() {}
   +    final public static function bar() {}

   -    protected abstract function zim();
   +    abstract protected function zim();
    }

    readonly final class ValueObject
    {
        // ...
    }

Example #5
~~~~~~~~~~

*Default* configuration.

.. error::
   Cannot generate diff for code sample #5 of rule visibility_required:
   the sample is not suitable for current version of PHP (8.3.19).

Rule sets
---------

The rule is part of the following rule sets:

- `@PER <./../../ruleSets/PER.rst>`_
- `@PER-CS <./../../ruleSets/PER-CS.rst>`_
- `@PER-CS1.0 <./../../ruleSets/PER-CS1.0.rst>`_
- `@PER-CS2.0 <./../../ruleSets/PER-CS2.0.rst>`_
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
