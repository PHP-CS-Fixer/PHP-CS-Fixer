============================================
Rule ``phpdoc_add_missing_param_annotation``
============================================

PHPDoc should contain ``@param`` for all params.

Configuration
-------------

``only_untyped``
~~~~~~~~~~~~~~~~

Whether to add missing ``@param`` annotations for untyped parameters only.

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
   @@ -1,7 +1,8 @@
    <?php
    /**
     * @param int $bar
   + * @param mixed $baz
     *
     * @return void
     */
    function f9(string $foo, $bar, $baz) {}

Example #2
~~~~~~~~~~

With configuration: ``['only_untyped' => true]``.

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,7 +1,8 @@
    <?php
    /**
     * @param int $bar
   + * @param mixed $baz
     *
     * @return void
     */
    function f9(string $foo, $bar, $baz) {}

Example #3
~~~~~~~~~~

With configuration: ``['only_untyped' => false]``.

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,7 +1,9 @@
    <?php
    /**
     * @param int $bar
   + * @param string $foo
   + * @param mixed $baz
     *
     * @return void
     */
    function f9(string $foo, $bar, $baz) {}

Rule sets
---------

The rule is part of the following rule set:

@PhpCsFixer
  Using the ``@PhpCsFixer`` rule set will enable the ``phpdoc_add_missing_param_annotation`` rule with the default config.
