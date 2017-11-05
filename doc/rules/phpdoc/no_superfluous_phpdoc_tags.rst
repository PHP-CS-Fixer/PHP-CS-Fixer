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

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,7 +1,6 @@
    <?php
    class Foo {
        /**
   -     * @var Bar
         */
        private Bar $bar;
    }

Rule sets
---------

The rule is part of the following rule sets:

@Symfony
  Using the ``@Symfony`` rule set will enable the ``no_superfluous_phpdoc_tags`` rule with the config below:

  ``['allow_mixed' => true]``

@PhpCsFixer
  Using the ``@PhpCsFixer`` rule set will enable the ``no_superfluous_phpdoc_tags`` rule with the config below:

  ``['allow_mixed' => true]``
