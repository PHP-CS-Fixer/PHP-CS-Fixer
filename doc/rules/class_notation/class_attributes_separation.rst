====================================
Rule ``class_attributes_separation``
====================================

Class, trait and interface elements must be separated with one or none blank
line.

Configuration
-------------

``elements``
~~~~~~~~~~~~

Dictionary of ``const|method|property`` => ``none|one`` values.

Allowed types: ``array``

Default value: ``['const' => 'one', 'method' => 'one', 'property' => 'one']``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
   @@ -4,9 +4,8 @@
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
   @@ -1,6 +1,8 @@
    <?php
    class Sample
   -{private $a; // a is awesome
   +{
   +private $a; // a is awesome
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
   @@ -2,6 +2,7 @@
    class Sample
    {
        const A = 1;
   +
        /** seconds in some hours */
        const B = 3600;
    }

Rule sets
---------

The rule is part of the following rule sets:

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``class_attributes_separation`` rule with the config below:

  ``['elements' => ['method' => 'one']]``

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``class_attributes_separation`` rule with the config below:

  ``['elements' => ['method' => 'one']]``
