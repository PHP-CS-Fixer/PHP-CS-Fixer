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

@PER
  Using the `@PER <./../../ruleSets/PER.rst>`_ rule set will enable the ``visibility_required`` rule with the default config.

@PHP71Migration
  Using the `@PHP71Migration <./../../ruleSets/PHP71Migration.rst>`_ rule set will enable the ``visibility_required`` rule with the default config.

@PHP73Migration
  Using the `@PHP73Migration <./../../ruleSets/PHP73Migration.rst>`_ rule set will enable the ``visibility_required`` rule with the default config.

@PHP74Migration
  Using the `@PHP74Migration <./../../ruleSets/PHP74Migration.rst>`_ rule set will enable the ``visibility_required`` rule with the default config.

@PHP80Migration
  Using the `@PHP80Migration <./../../ruleSets/PHP80Migration.rst>`_ rule set will enable the ``visibility_required`` rule with the default config.

@PHP81Migration
  Using the `@PHP81Migration <./../../ruleSets/PHP81Migration.rst>`_ rule set will enable the ``visibility_required`` rule with the default config.

@PHP82Migration
  Using the `@PHP82Migration <./../../ruleSets/PHP82Migration.rst>`_ rule set will enable the ``visibility_required`` rule with the default config.

@PSR12
  Using the `@PSR12 <./../../ruleSets/PSR12.rst>`_ rule set will enable the ``visibility_required`` rule with the default config.

@PSR2
  Using the `@PSR2 <./../../ruleSets/PSR2.rst>`_ rule set will enable the ``visibility_required`` rule with the config below:

  ``['elements' => ['method', 'property']]``

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``visibility_required`` rule with the default config.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``visibility_required`` rule with the default config.
