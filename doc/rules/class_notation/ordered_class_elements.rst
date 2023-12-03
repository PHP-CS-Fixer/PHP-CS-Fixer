===============================
Rule ``ordered_class_elements``
===============================

Orders the elements of classes/interfaces/traits/enums.

Description
-----------

Accepts a subset of pre-defined element types, special element groups, and
custom patterns.

Element types: ``['use_trait', 'public', 'protected', 'private', 'case',
'constant', 'constant_public', 'constant_protected', 'constant_private',
'property', 'property_static', 'property_public', 'property_protected',
'property_private', 'property_public_readonly', 'property_protected_readonly',
'property_private_readonly', 'property_public_static',
'property_protected_static', 'property_private_static', 'method',
'method_abstract', 'method_static', 'method_public', 'method_protected',
'method_private', 'method_public_abstract', 'method_protected_abstract',
'method_private_abstract', 'method_public_abstract_static',
'method_protected_abstract_static', 'method_private_abstract_static',
'method_public_static', 'method_protected_static', 'method_private_static']``

Special element types: ``['construct', 'destruct', 'magic', 'phpunit']``

Custom values:

- ``method:*``: specify a single method name (e.g. ``method:__invoke``) to set
the order of that specific method.

Configuration
-------------

``case_sensitive``
~~~~~~~~~~~~~~~~~~

Whether the sorting should be case sensitive.

Allowed types: ``bool``

Default value: ``false``

``order``
~~~~~~~~~

List of strings defining order of elements.

Allowed types: ``array``

Default value: ``['use_trait', 'case', 'constant_public', 'constant_protected', 'constant_private', 'property_public', 'property_protected', 'property_private', 'construct', 'destruct', 'magic', 'phpunit', 'method_public', 'method_protected', 'method_private']``

``sort_algorithm``
~~~~~~~~~~~~~~~~~~

How multiple occurrences of same type statements should be sorted.

Allowed values: ``'alpha'`` and ``'none'``

Default value: ``'none'``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    final class Example
    {
        use BarTrait;
        use BazTrait;
        const C1 = 1;
        const C2 = 2;
   -    protected static $protStatProp;
        public static $pubStatProp1;
        public $pubProp1;
   +    var $pubProp2;
   +    public static $pubStatProp2;
   +    public $pubProp3;
   +    protected static $protStatProp;
        protected $protProp;
   -    var $pubProp2;
        private static $privStatProp;
        private $privProp;
   -    public static $pubStatProp2;
   -    public $pubProp3;
        protected function __construct() {}
   -    private static function privStatFunc() {}
   +    public function __destruct() {}
   +    public function __toString() {}
        public function pubFunc1() {}
   -    public function __toString() {}
   -    protected function protFunc() {}
        function pubFunc2() {}
        public static function pubStatFunc1() {}
        public function pubFunc3() {}
        static function pubStatFunc2() {}
   -    private function privFunc() {}
        public static function pubStatFunc3() {}
   +    protected function protFunc() {}
        protected static function protStatFunc() {}
   -    public function __destruct() {}
   +    private static function privStatFunc() {}
   +    private function privFunc() {}
    }

Example #2
~~~~~~~~~~

With configuration: ``['order' => ['method_private', 'method_public']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    class Example
    {
   +    private function B(){}
        public function A(){}
   -    private function B(){}
    }

Example #3
~~~~~~~~~~

With configuration: ``['order' => ['method_public'], 'sort_algorithm' => 'alpha']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    class Example
    {
   -    public function D(){}
   +    public function A(){}
        public function B(){}
   -    public function A(){}
        public function C(){}
   +    public function D(){}
    }

Example #4
~~~~~~~~~~

With configuration: ``['order' => ['method_public'], 'sort_algorithm' => 'alpha', 'case_sensitive' => true]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    class Example
    {
   +    public function AA(){}
   +    public function AWs(){}
        public function Aa(){}
   -    public function AA(){}
        public function AwS(){}
   -    public function AWs(){}
    }

Rule sets
---------

The rule is part of the following rule sets:

- `@PER <./../../ruleSets/PER.rst>`_ with config:

  ``['order' => ['use_trait']]``

- `@PER-CS <./../../ruleSets/PER-CS.rst>`_ with config:

  ``['order' => ['use_trait']]``

- `@PER-CS1.0 <./../../ruleSets/PER-CS1.0.rst>`_ with config:

  ``['order' => ['use_trait']]``

- `@PER-CS2.0 <./../../ruleSets/PER-CS2.0.rst>`_ with config:

  ``['order' => ['use_trait']]``

- `@PSR12 <./../../ruleSets/PSR12.rst>`_ with config:

  ``['order' => ['use_trait']]``

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_ with config:

  ``['order' => ['use_trait']]``


Source class
------------

`PhpCsFixer\\Fixer\\ClassNotation\\OrderedClassElementsFixer <./../../../src/Fixer/ClassNotation/OrderedClassElementsFixer.php>`_
