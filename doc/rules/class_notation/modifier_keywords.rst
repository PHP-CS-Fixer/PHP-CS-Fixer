==========================
Rule ``modifier_keywords``
==========================

Classes, constants, properties, and methods MUST have visibility declared, and
keyword modifiers MUST be in the following order: inheritance modifier
(``abstract`` or ``final``), visibility modifier (``public``, ``protected``, or
``private``), set-visibility modifier (``public(set)``, ``protected(set)``, or
``private(set)``), scope modifier (``static``), mutation modifier
(``readonly``), type declaration, name.

Warning
-------

This rule is CONFIGURABLE
~~~~~~~~~~~~~~~~~~~~~~~~~

You can configure this rule using the following options: ``elements``,
``omit_public_visibility_set_visibility_present``.

Configuration
-------------

``elements``
~~~~~~~~~~~~

The structural elements to fix.

Allowed values: a subset of ``['const', 'method', 'property']``

Default value: ``['const', 'method', 'property']``

``omit_public_visibility_set_visibility_present``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Whether the general public visibility modifier should be omitted, if a
set-visibility is specified.

Allowed types: ``bool``

Default value: ``false``

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

With configuration: ``['omit_public_visibility_set_visibility_present' => true]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    class Sample
    {
   -    public private(set) $baz;
   +    private(set) $baz;
    }

Example #5
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

- `@PER <./../../ruleSets/PER.rst>`_ *(deprecated)*
- `@PER-CS <./../../ruleSets/PER-CS.rst>`_
- `@PER-CS1.0 <./../../ruleSets/PER-CS1.0.rst>`_ *(deprecated)*
- `@PER-CS1x0 <./../../ruleSets/PER-CS1x0.rst>`_
- `@PER-CS2.0 <./../../ruleSets/PER-CS2.0.rst>`_ *(deprecated)*
- `@PER-CS2x0 <./../../ruleSets/PER-CS2x0.rst>`_
- `@PER-CS3.0 <./../../ruleSets/PER-CS3.0.rst>`_ *(deprecated)*
- `@PER-CS3x0 <./../../ruleSets/PER-CS3x0.rst>`_
- `@PHP7x1Migration <./../../ruleSets/PHP7x1Migration.rst>`_
- `@PHP7x3Migration <./../../ruleSets/PHP7x3Migration.rst>`_
- `@PHP7x4Migration <./../../ruleSets/PHP7x4Migration.rst>`_
- `@PHP8x0Migration <./../../ruleSets/PHP8x0Migration.rst>`_
- `@PHP8x1Migration <./../../ruleSets/PHP8x1Migration.rst>`_
- `@PHP8x2Migration <./../../ruleSets/PHP8x2Migration.rst>`_
- `@PHP8x3Migration <./../../ruleSets/PHP8x3Migration.rst>`_
- `@PHP8x4Migration <./../../ruleSets/PHP8x4Migration.rst>`_
- `@PHP8x5Migration <./../../ruleSets/PHP8x5Migration.rst>`_
- `@PHP71Migration <./../../ruleSets/PHP71Migration.rst>`_ *(deprecated)*
- `@PHP73Migration <./../../ruleSets/PHP73Migration.rst>`_ *(deprecated)*
- `@PHP74Migration <./../../ruleSets/PHP74Migration.rst>`_ *(deprecated)*
- `@PHP80Migration <./../../ruleSets/PHP80Migration.rst>`_ *(deprecated)*
- `@PHP81Migration <./../../ruleSets/PHP81Migration.rst>`_ *(deprecated)*
- `@PHP82Migration <./../../ruleSets/PHP82Migration.rst>`_ *(deprecated)*
- `@PHP83Migration <./../../ruleSets/PHP83Migration.rst>`_ *(deprecated)*
- `@PHP84Migration <./../../ruleSets/PHP84Migration.rst>`_ *(deprecated)*
- `@PHP85Migration <./../../ruleSets/PHP85Migration.rst>`_ *(deprecated)*
- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@PSR2 <./../../ruleSets/PSR2.rst>`_ with config:

  ``['elements' => ['method', 'property']]``

- `@PSR12 <./../../ruleSets/PSR12.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\ClassNotation\\ModifierKeywordsFixer <./../../../src/Fixer/ClassNotation/ModifierKeywordsFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\ClassNotation\\ModifierKeywordsFixerTest <./../../../tests/Fixer/ClassNotation/ModifierKeywordsFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
