=============================
Rule ``function_to_constant``
=============================

Replace core functions calls returning constants with the constants.

.. warning:: Using this rule is risky.

   Risky when any of the configured functions to replace are overridden.

Configuration
-------------

``functions``
~~~~~~~~~~~~~

List of function names to fix.

Allowed values: a subset of ``['get_called_class', 'get_class', 'get_class_this', 'php_sapi_name', 'phpversion', 'pi']``

Default value: ``['get_class', 'php_sapi_name', 'phpversion', 'pi']``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,12 +1,12 @@
    <?php
   -echo phpversion();
   -echo pi();
   -echo php_sapi_name();
   +echo PHP_VERSION;
   +echo M_PI;
   +echo PHP_SAPI;
    class Foo
    {
        public function Bar()
        {
   -        echo get_class();
   +        echo __CLASS__;
            echo get_called_class();
        }
    }

Example #2
~~~~~~~~~~

With configuration: ``['functions' => ['get_called_class', 'get_class_this', 'phpversion']]``.

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,5 +1,5 @@
    <?php
   -echo phpversion();
   +echo PHP_VERSION;
    echo pi();
    class Foo
    {
   @@ -6,7 +6,7 @@
        public function Bar()
        {
            echo get_class();
   -        get_class($this);
   -        echo get_called_class();
   +        static::class;
   +        echo static::class;
        }
    }

Rule sets
---------

The rule is part of the following rule sets:

@PhpCsFixer:risky
  Using the `@PhpCsFixer:risky <./../../ruleSets/PhpCsFixerRisky.rst>`_ rule set will enable the ``function_to_constant`` rule with the config below:

  ``['functions' => ['get_called_class', 'get_class', 'get_class_this', 'php_sapi_name', 'phpversion', 'pi']]``

@Symfony:risky
  Using the `@Symfony:risky <./../../ruleSets/SymfonyRisky.rst>`_ rule set will enable the ``function_to_constant`` rule with the config below:

  ``['functions' => ['get_called_class', 'get_class', 'get_class_this', 'php_sapi_name', 'phpversion', 'pi']]``
