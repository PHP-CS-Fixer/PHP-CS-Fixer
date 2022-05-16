=====================================
Rule ``fully_qualified_strict_types``
=====================================

Removes the leading part of fully qualified symbol references if a given symbol
is imported or belongs to the current namespace. Fixes function arguments,
caught exception ``classes``, ``extend`` and ``implements`` of ``classes`` and
``interfaces`` to short version.

Configuration
-------------

``shorten_globals_in_global_ns``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

remove leading ``\`` when in global namespace.

Allowed types: ``bool``

Default value: ``false``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    use Foo\Bar;
    use Foo\Bar\Baz;

    class SomeClass
    {
   -    public function doSomething(\Foo\Bar $foo, \Exception $e): \Foo\Bar\Baz
   +    public function doSomething(Bar $foo, \Exception $e): Baz
        {
        }
    }

Example #2
~~~~~~~~~~

With configuration: ``['shorten_globals_in_global_ns' => true]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    namespace {
        use Foo\A;

        try {
            foo();
   -    } catch (\Exception|\Foo\A $e) {
   +    } catch (Exception|A $e) {

        }
    }

    namespace Foo\Bar {
   -    class SomeClass implements \Foo\Bar\Baz
   +    class SomeClass implements Baz
        {
        }
    }

Rule sets
---------

The rule is part of the following rule sets:

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``fully_qualified_strict_types`` rule with the default config.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``fully_qualified_strict_types`` rule with the default config.
