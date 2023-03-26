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

Allowed values: a subset of ``['method', 'param', 'property', 'property-read', 'property-write', 'return', 'throws', 'type', 'var']``

Default value: ``['method', 'param', 'property', 'return', 'throws', 'type', 'var']``

``align``
~~~~~~~~~

How comments should be aligned.

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

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``phpdoc_align`` rule with the default config.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``phpdoc_align`` rule with the default config.
