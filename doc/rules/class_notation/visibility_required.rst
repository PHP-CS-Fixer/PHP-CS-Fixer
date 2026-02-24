============================
Rule ``visibility_required``
============================

Classes, constants, properties, and methods MUST have visibility declared, and
keyword modifiers MUST be in the following order: inheritance modifier
(``abstract`` or ``final``), visibility modifier (``public``, ``protected``, or
``private``), set-visibility modifier (``public(set)``, ``protected(set)``, or
``private(set)``), scope modifier (``static``), mutation modifier
(``readonly``), type declaration, name.

Warnings
--------

This rule is DEPRECATED and will be removed in the next major version 4.0
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

You should use ``modifier_keywords`` instead.

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

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\ClassNotation\\VisibilityRequiredFixer <./../../../src/Fixer/ClassNotation/VisibilityRequiredFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\ClassNotation\\VisibilityRequiredFixerTest <./../../../tests/Fixer/ClassNotation/VisibilityRequiredFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
