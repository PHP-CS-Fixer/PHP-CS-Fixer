================================
Rule ``type_declaration_spaces``
================================

Ensure single space between a variable and its type declaration in function
arguments and properties.

Configuration
-------------

``elements``
~~~~~~~~~~~~

Structural elements where the spacing after the type declaration should be
fixed.

Allowed values: a subset of ``['function', 'property']``

Default value: ``['function', 'property']``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    class Bar
    {
   -    private string    $a;
   -    private bool   $b;
   +    private string $a;
   +    private bool $b;

   -    public function __invoke(array   $c) {}
   +    public function __invoke(array $c) {}
    }

Example #2
~~~~~~~~~~

With configuration: ``['elements' => ['function']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    class Foo
    {
        public int   $bar;

   -    public function baz(string     $a)
   +    public function baz(string $a)
        {
   -        return fn(bool    $c): string => (string) $c;
   +        return fn(bool $c): string => (string) $c;
        }
    }

Example #3
~~~~~~~~~~

With configuration: ``['elements' => ['property']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    class Foo
    {
   -    public int   $bar;
   +    public int $bar;

        public function baz(string     $a) {}
    }

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

Source class
------------

`PhpCsFixer\\Fixer\\Whitespace\\TypeDeclarationSpacesFixer <./../src/Fixer/Whitespace/TypeDeclarationSpacesFixer.php>`_
