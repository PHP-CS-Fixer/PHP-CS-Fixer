=============================
Rule ``function_to_constant``
=============================

Replace core functions calls returning constants with the constants.

Warning
-------

Using this rule is risky
~~~~~~~~~~~~~~~~~~~~~~~~

Risky when any of the configured functions to replace are overridden.

Configuration
-------------

``functions``
~~~~~~~~~~~~~

List of function names to fix.

Allowed values: a subset of ``['get_called_class', 'get_class', 'get_class_this', 'php_sapi_name', 'phpversion', 'pi']``

Default value: ``['get_called_class', 'get_class', 'get_class_this', 'php_sapi_name', 'phpversion', 'pi']``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
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
   -        echo get_called_class();
   +        echo __CLASS__;
   +        echo static::class;
        }
    }

Example #2
~~~~~~~~~~

With configuration: ``['functions' => ['get_called_class', 'get_class_this', 'phpversion']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -echo phpversion();
   +echo PHP_VERSION;
    echo pi();
    class Foo
    {
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

- `@PhpCsFixer:risky <./../../ruleSets/PhpCsFixerRisky.rst>`_
- `@Symfony:risky <./../../ruleSets/SymfonyRisky.rst>`_

Source class
------------

`PhpCsFixer\\Fixer\\LanguageConstruct\\FunctionToConstantFixer <./../src/Fixer/LanguageConstruct/FunctionToConstantFixer.php>`_
