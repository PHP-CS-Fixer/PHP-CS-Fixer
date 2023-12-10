===================================
Rule ``native_function_invocation``
===================================

Add leading ``\`` before function invocation to speed up resolving.

Warning
-------

Using this rule is risky
~~~~~~~~~~~~~~~~~~~~~~~~

Risky when any of the functions are overridden.

Configuration
-------------

``exclude``
~~~~~~~~~~~

List of functions to ignore.

Allowed types: ``array``

Default value: ``[]``

``include``
~~~~~~~~~~~

List of function names or sets to fix. Defined sets are ``@internal`` (all
native functions), ``@all`` (all global functions) and ``@compiler_optimized``
(functions that are specially optimized by Zend).

Allowed types: ``array``

Default value: ``['@compiler_optimized']``

``scope``
~~~~~~~~~

Only fix function calls that are made within a namespace or fix all.

Allowed values: ``'all'`` and ``'namespaced'``

Default value: ``'all'``

``strict``
~~~~~~~~~~

Whether leading ``\`` of function call not meant to have it should be removed.

Allowed types: ``bool``

Default value: ``true``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php

    function baz($options)
    {
   -    if (!array_key_exists("foo", $options)) {
   +    if (!\array_key_exists("foo", $options)) {
            throw new \InvalidArgumentException();
        }

        return json_encode($options);
    }

Example #2
~~~~~~~~~~

With configuration: ``['exclude' => ['json_encode']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php

    function baz($options)
    {
   -    if (!array_key_exists("foo", $options)) {
   +    if (!\array_key_exists("foo", $options)) {
            throw new \InvalidArgumentException();
        }

        return json_encode($options);
    }

Example #3
~~~~~~~~~~

With configuration: ``['scope' => 'all']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    namespace space1 {
   -    echo count([1]);
   +    echo \count([1]);
    }
    namespace {
   -    echo count([1]);
   +    echo \count([1]);
    }

Example #4
~~~~~~~~~~

With configuration: ``['scope' => 'namespaced']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    namespace space1 {
   -    echo count([1]);
   +    echo \count([1]);
    }
    namespace {
        echo count([1]);
    }

Example #5
~~~~~~~~~~

With configuration: ``['include' => ['myGlobalFunction']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -myGlobalFunction();
   +\myGlobalFunction();
    count();

Example #6
~~~~~~~~~~

With configuration: ``['include' => ['@all']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -myGlobalFunction();
   -count();
   +\myGlobalFunction();
   +\count();

Example #7
~~~~~~~~~~

With configuration: ``['include' => ['@internal']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    myGlobalFunction();
   -count();
   +\count();

Example #8
~~~~~~~~~~

With configuration: ``['include' => ['@compiler_optimized']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    $a .= str_repeat($a, 4);
   -$c = get_class($d);
   +$c = \get_class($d);

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer:risky <./../../ruleSets/PhpCsFixerRisky.rst>`_ with config:

  ``['include' => ['@compiler_optimized'], 'scope' => 'namespaced', 'strict' => true]``

- `@Symfony:risky <./../../ruleSets/SymfonyRisky.rst>`_ with config:

  ``['include' => ['@compiler_optimized'], 'scope' => 'namespaced', 'strict' => true]``


Source class
------------

`PhpCsFixer\\Fixer\\FunctionNotation\\NativeFunctionInvocationFixer <./../../../src/Fixer/FunctionNotation/NativeFunctionInvocationFixer.php>`_
