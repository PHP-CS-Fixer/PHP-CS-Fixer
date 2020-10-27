=====================
Rule ``phpdoc_align``
=====================

All items of the given phpdoc tags must be either left-aligned or (by default)
aligned vertically.

Configuration
-------------

``tags``
~~~~~~~~

The tags that should be aligned.

Allowed values: a subset of ``['param', 'property', 'property-read', 'property-write', 'return', 'throws', 'type', 'var', 'method']``

Default value: ``['param', 'return', 'throws', 'type', 'var']``

``align``
~~~~~~~~~

Align comments

Allowed values: ``'left'``, ``'vertical'``

Default value: ``'vertical'``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,8 +1,8 @@
    <?php
    /**
   - * @param  EngineInterface $templating
   - * @param string      $format
   - * @param  int  $code       an HTTP response status code
   - * @param    bool         $debug
   - * @param  mixed    &$reference     a parameter passed by reference
   + * @param EngineInterface $templating
   + * @param string          $format
   + * @param int             $code       an HTTP response status code
   + * @param bool            $debug
   + * @param mixed           &$reference a parameter passed by reference
     */

Example #2
~~~~~~~~~~

With configuration: ``['align' => 'vertical']``.

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,8 +1,8 @@
    <?php
    /**
   - * @param  EngineInterface $templating
   - * @param string      $format
   - * @param  int  $code       an HTTP response status code
   - * @param    bool         $debug
   - * @param  mixed    &$reference     a parameter passed by reference
   + * @param EngineInterface $templating
   + * @param string          $format
   + * @param int             $code       an HTTP response status code
   + * @param bool            $debug
   + * @param mixed           &$reference a parameter passed by reference
     */

Example #3
~~~~~~~~~~

With configuration: ``['align' => 'left']``.

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,8 +1,8 @@
    <?php
    /**
   - * @param  EngineInterface $templating
   - * @param string      $format
   - * @param  int  $code       an HTTP response status code
   - * @param    bool         $debug
   - * @param  mixed    &$reference     a parameter passed by reference
   + * @param EngineInterface $templating
   + * @param string $format
   + * @param int $code an HTTP response status code
   + * @param bool $debug
   + * @param mixed &$reference a parameter passed by reference
     */

Rule sets
---------

The rule is part of the following rule sets:

@Symfony
  Using the ``@Symfony`` rule set will enable the ``phpdoc_align`` rule with the config below:

  ``['tags' => ['method', 'param', 'property', 'return', 'throws', 'type', 'var']]``

@PhpCsFixer
  Using the ``@PhpCsFixer`` rule set will enable the ``phpdoc_align`` rule with the config below:

  ``['tags' => ['method', 'param', 'property', 'return', 'throws', 'type', 'var']]``
