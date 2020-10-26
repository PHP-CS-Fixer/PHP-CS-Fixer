=====================================
Rule ``phpdoc_return_self_reference``
=====================================

The type of ``@return`` annotations of methods returning a reference to itself
must the configured one.

Configuration
-------------

``replacements``
~~~~~~~~~~~~~~~~

Mapping between replaced return types with new ones.

Allowed types: ``array``

Default value: ``['this' => '$this', '@this' => '$this', '$self' => 'self', '@self' => 'self', '$static' => 'static', '@static' => 'static']``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
   @@ -2,7 +2,7 @@
    class Sample
    {
        /**
   -     * @return this
   +     * @return $this
         */
        public function test1()
        {
   @@ -10,5 +10,5 @@
        }

        /**
   -     * @return $self
   +     * @return self
         */

Example #2
~~~~~~~~~~

With configuration: ``['replacements' => ['this' => 'self']]``.

.. code-block:: diff

   --- Original
   +++ New
   @@ -2,7 +2,7 @@
    class Sample
    {
        /**
   -     * @return this
   +     * @return self
         */
        public function test1()
        {

Rule sets
---------

The rule is part of the following rule sets:

@Symfony
  Using the ``@Symfony`` rule set will enable the ``phpdoc_return_self_reference`` rule with the default config.

@PhpCsFixer
  Using the ``@PhpCsFixer`` rule set will enable the ``phpdoc_return_self_reference`` rule with the default config.
