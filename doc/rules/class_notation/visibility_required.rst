============================
Rule ``visibility_required``
============================

Visibility MUST be declared on all properties and methods; ``abstract`` and
``final`` MUST be declared before the visibility; ``static`` MUST be declared
after the visibility.

Configuration
-------------

``elements``
~~~~~~~~~~~~

The structural elements to fix (PHP >= 7.1 required for ``const``).

Allowed values: a subset of ``['const', 'method', 'property']``

Default value: ``['property', 'method', 'const']``

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
- `@PSR2 <./../../ruleSets/PSR2.rst>`_ with config:

  ``['elements' => ['method', 'property']]``

- `@PSR12 <./../../ruleSets/PSR12.rst>`_
- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

Source class
------------

`PhpCsFixer\\Fixer\\ClassNotation\\VisibilityRequiredFixer <./../src/Fixer/ClassNotation/VisibilityRequiredFixer.php>`_
