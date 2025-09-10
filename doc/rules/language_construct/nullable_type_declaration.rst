==================================
Rule ``nullable_type_declaration``
==================================

Nullable single type declaration should be standardised using configured syntax.

Configuration
-------------

``syntax``
~~~~~~~~~~

Whether to use question mark (``?``) or explicit ``null`` union for nullable
type.

Allowed values: ``'question_mark'`` and ``'union'``

Default value: ``'question_mark'``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -function bar(null|int $value, null|\Closure $callable): int|null {}
   +function bar(?int $value, ?\Closure $callable): ?int {}

Example #2
~~~~~~~~~~

With configuration: ``['syntax' => 'union']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -function baz(?int $value, ?\stdClass $obj, ?array $config): ?int {}
   +function baz(null|int $value, null|\stdClass $obj, null|array $config): null|int {}

Example #3
~~~~~~~~~~

With configuration: ``['syntax' => 'question_mark']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    class ValueObject
    {
   -    public null|string $name;
   +    public ?string $name;
        public ?int $count;
   -    public null|bool $internal;
   -    public null|\Closure $callback;
   +    public ?bool $internal;
   +    public ?\Closure $callback;
    }

Rule sets
---------

The rule is part of the following rule sets:

- `@PER <./../../ruleSets/PER.rst>`_ *(deprecated)*
- `@PER-CS <./../../ruleSets/PER-CS.rst>`_
- `@PER-CS3.0 <./../../ruleSets/PER-CS3.0.rst>`_ *(deprecated)*
- `@PER-CS3x0 <./../../ruleSets/PER-CS3x0.rst>`_
- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\LanguageConstruct\\NullableTypeDeclarationFixer <./../../../src/Fixer/LanguageConstruct/NullableTypeDeclarationFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\LanguageConstruct\\NullableTypeDeclarationFixerTest <./../../../tests/Fixer/LanguageConstruct/NullableTypeDeclarationFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
