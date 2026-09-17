===================================
Rule ``no_superfluous_phpdoc_tags``
===================================

Removes ``@param``, ``@return`` and ``@var`` tags that don't provide any useful
information.

Warning
-------

This rule is CONFIGURABLE
~~~~~~~~~~~~~~~~~~~~~~~~~

You can configure this rule using the following options:
``allow_hidden_params``, ``allow_mixed``, ``allow_unused_params``,
``remove_inheritdoc``.

Configuration
-------------

``allow_hidden_params``
~~~~~~~~~~~~~~~~~~~~~~~

Whether ``param`` annotation for hidden params in method signature are allowed.

Allowed types: ``bool``

Default value: ``false``

Default value (future-mode): ``true``

``allow_mixed``
~~~~~~~~~~~~~~~

Whether type ``mixed`` without description is allowed (``true``) or considered
superfluous (``false``).

Allowed types: ``bool``

Default value: ``false``

``allow_unused_params``
~~~~~~~~~~~~~~~~~~~~~~~

Whether ``param`` annotation without actual signature is allowed (``true``) or
considered superfluous (``false``).

Allowed types: ``bool``

Default value: ``false``

``remove_inheritdoc``
~~~~~~~~~~~~~~~~~~~~~

Remove ``@inheritDoc`` tags.

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
    class Foo {
        /**
   -     * @param Bar $bar
   -     * @param mixed $baz
         *
   -     * @return Baz
         */
        public function doFoo(Bar $bar, $baz): Baz {}
    }

Example #2
~~~~~~~~~~

With configuration: ``['allow_mixed' => true]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    class Foo {
        /**
   -     * @param Bar $bar
         * @param mixed $baz
         */
        public function doFoo(Bar $bar, $baz) {}
    }

Example #3
~~~~~~~~~~

With configuration: ``['remove_inheritdoc' => true]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    class Foo {
        /**
   -     * @inheritDoc
   +     *
         */
        public function doFoo(Bar $bar, $baz) {}
    }

Example #4
~~~~~~~~~~

With configuration: ``['allow_hidden_params' => true]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    class Foo {
        /**
   -     * @param Bar $bar
   -     * @param mixed $baz
         * @param string|int|null $qux
   -     * @param mixed $foo
         */
        public function doFoo(Bar $bar, $baz /*, $qux = null */) {}
    }

Example #5
~~~~~~~~~~

With configuration: ``['allow_unused_params' => true]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    class Foo {
        /**
   -     * @param Bar $bar
   -     * @param mixed $baz
         * @param string|int|null $qux
         * @param mixed $foo
         */
        public function doFoo(Bar $bar, $baz /*, $qux = null */) {}
    }

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ with config:

  ``['allow_hidden_params' => true, 'allow_mixed' => true, 'remove_inheritdoc' => true]``

- `@Symfony <./../../ruleSets/Symfony.rst>`_ with config:

  ``['allow_hidden_params' => true, 'remove_inheritdoc' => true]``

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Phpdoc\\NoSuperfluousPhpdocTagsFixer <./../../../src/Fixer/Phpdoc/NoSuperfluousPhpdocTagsFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Phpdoc\\NoSuperfluousPhpdocTagsFixerTest <./../../../tests/Fixer/Phpdoc/NoSuperfluousPhpdocTagsFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
