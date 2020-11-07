===================================
Rule ``no_superfluous_phpdoc_tags``
===================================

Removes ``@param``, ``@return`` and ``@var`` tags that don't provide any useful
information.

Configuration
-------------

``allow_mixed``
~~~~~~~~~~~~~~~

Whether type ``mixed`` without description is allowed (``true``) or considered
superfluous (``false``)

Allowed types: ``bool``

Default value: ``false``

``remove_inheritdoc``
~~~~~~~~~~~~~~~~~~~~~

Remove ``@inheritDoc`` tags

Allowed types: ``bool``

Default value: ``false``

``allow_unused_params``
~~~~~~~~~~~~~~~~~~~~~~~

Whether ``param`` annotation without actual signature is allowed (``true``) or
considered superfluous (``false``)

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
   @@ -1,8 +1,6 @@
    <?php
    class Foo {
        /**
   -     * @param Bar $bar
   -     * @param mixed $baz
         */
        public function doFoo(Bar $bar, $baz) {}
    }

Example #2
~~~~~~~~~~

With configuration: ``['allow_mixed' => true]``.

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,8 +1,7 @@
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

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,10 +1,7 @@
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

Example #4
~~~~~~~~~~

With configuration: ``['remove_inheritdoc' => true]``.

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,7 +1,7 @@
    <?php
    class Foo {
        /**
   -     * @inheritDoc
   +     *
         */
        public function doFoo(Bar $bar, $baz) {}
    }

Example #5
~~~~~~~~~~

With configuration: ``['allow_unused_params' => true]``.

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,9 +1,7 @@
    <?php
    class Foo {
        /**
   -     * @param Bar $bar
   -     * @param mixed $baz
         * @param string|int|null $qux
         */
        public function doFoo(Bar $bar, $baz /*, $qux = null */) {}
    }

Rule sets
---------

The rule is part of the following rule sets:

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``no_superfluous_phpdoc_tags`` rule with the config below:

  ``['allow_mixed' => true, 'allow_unused_params' => true]``

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``no_superfluous_phpdoc_tags`` rule with the config below:

  ``['allow_mixed' => true, 'allow_unused_params' => true]``
