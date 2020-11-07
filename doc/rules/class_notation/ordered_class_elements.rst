===============================
Rule ``ordered_class_elements``
===============================

Orders the elements of classes/interfaces/traits.

Configuration
-------------

``order``
~~~~~~~~~

List of strings defining order of elements.

Allowed values: a subset of ``['use_trait', 'public', 'protected', 'private', 'constant', 'constant_public', 'constant_protected', 'constant_private', 'property', 'property_static', 'property_public', 'property_protected', 'property_private', 'property_public_static', 'property_protected_static', 'property_private_static', 'method', 'method_abstract', 'method_static', 'method_public', 'method_protected', 'method_private', 'method_public_abstract', 'method_protected_abstract', 'method_public_abstract_static', 'method_protected_abstract_static', 'method_public_static', 'method_protected_static', 'method_private_static', 'construct', 'destruct', 'magic', 'phpunit']``

Default value: ``['use_trait', 'constant_public', 'constant_protected', 'constant_private', 'property_public', 'property_protected', 'property_private', 'construct', 'destruct', 'magic', 'phpunit', 'method_public', 'method_protected', 'method_private']``

``sort_algorithm``
~~~~~~~~~~~~~~~~~~

How multiple occurrences of same type statements should be sorted

.. note:: The previous name of this option was ``sortAlgorithm`` but it is now deprecated and will be removed on next major version.

Allowed values: ``'alpha'``, ``'none'``

Default value: ``'none'``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
   @@ -5,26 +5,26 @@
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
   @@ -1,6 +1,6 @@
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
   @@ -1,8 +1,8 @@
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

Rule sets
---------

The rule is part of the following rule set:

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``ordered_class_elements`` rule with the default config.
