====================================
Rule ``class_attributes_separation``
====================================

Class, trait and interface elements must be separated with one blank line.

Configuration
-------------

``elements``
~~~~~~~~~~~~

List of classy elements; 'const', 'method', 'property'.

Allowed values: a subset of ``['const', 'method', 'property']``

Default value: ``['const', 'method', 'property']``

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

With configuration: ``['elements' => ['property']]``.

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

With configuration: ``['elements' => ['const']]``.

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

@Symfony
  Using the ``@Symfony`` rule set will enable the ``class_attributes_separation`` rule with the config below:

  ``['elements' => ['method']]``

@PhpCsFixer
  Using the ``@PhpCsFixer`` rule set will enable the ``class_attributes_separation`` rule with the config below:

  ``['elements' => ['method']]``
