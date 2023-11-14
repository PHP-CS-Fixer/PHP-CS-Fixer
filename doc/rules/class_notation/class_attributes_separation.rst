====================================
Rule ``class_attributes_separation``
====================================

Class, trait and interface elements must be separated with one or none blank
line.

Configuration
-------------

``elements``
~~~~~~~~~~~~

Dictionary of ``const|method|property|trait_import|case`` =>
``none|one|only_if_meta`` values.

Allowed types: ``array``

Default value: ``['const' => 'one', 'method' => 'one', 'property' => 'one', 'trait_import' => 'none', 'case' => 'none']``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    final class Sample
    {
        protected function foo()
        {
        }
   +
        protected function bar()
        {
        }
   -
   -
    }

Example #2
~~~~~~~~~~

With configuration: ``['elements' => ['property' => 'one']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    class Sample
   -{private $a; // foo
   +{
   +private $a; // foo
   +
        /** second in a hour */
        private $b;
    }

Example #3
~~~~~~~~~~

With configuration: ``['elements' => ['const' => 'one']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    class Sample
    {
        const A = 1;
   +
        /** seconds in some hours */
        const B = 3600;
    }

Example #4
~~~~~~~~~~

With configuration: ``['elements' => ['const' => 'only_if_meta']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    class Sample
    {
        /** @var int */
        const SECOND = 1;
   +
        /** @var int */
        const MINUTE = 60;
   -
        const HOUR = 3600;
   -
        const DAY = 86400;
    }

Example #5
~~~~~~~~~~

With configuration: ``['elements' => ['property' => 'only_if_meta']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    class Sample
    {
        public $a;
   +
        #[SetUp]
        public $b;
   +
        /** @var string */
        public $c;
   +
        /** @internal */
        #[Assert\String()]
        public $d;
   -
        public $e;
    }

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ with config:

  ``['elements' => ['method' => 'one']]``

- `@Symfony <./../../ruleSets/Symfony.rst>`_ with config:

  ``['elements' => ['method' => 'one']]``


Source class
------------

`PhpCsFixer\\Fixer\\ClassNotation\\ClassAttributesSeparationFixer <./../src/Fixer/ClassNotation/ClassAttributesSeparationFixer.php>`_
