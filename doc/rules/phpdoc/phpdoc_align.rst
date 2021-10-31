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

Allowed values: a subset of ``['param', 'property', 'property-read', 'property-write', 'return', 'throws', 'type', 'var', 'method', 'param-out', 'template', 'template-covariant', 'extends', 'implements', 'deprecated', 'internal', 'readonly', 'no-named-arguments', 'psalm-allow-private-mutation', 'psalm-assert', 'psalm-assert-if-false', 'psalm-if-this-is', 'psalm-assert-if-true', 'psalm-consistent-constructor', 'psalm-consistent-templates', 'psalm-external-mutation-free', 'psalm-ignore-falsable-return', 'psalm-ignore-nullable-return', 'psalm-ignore-var', 'psalm-immutable', 'psalm-import-type', 'psalm-internal', 'psalm-method', 'psalm-mutation-free', 'psalm-param', 'psalm-param-out', 'psalm-property', 'psalm-property-read', 'psalm-property-write', 'psalm-pure', 'psalm-readonly', 'psalm-readonly-allow-private-mutation', 'psalm-require-extends', 'psalm-require-implements', 'psalm-return', 'psalm-seal-properties', 'psalm-suppress SomeIssueName', 'psalm-taint-*', 'psalm-trace', 'psalm-type', 'psalm-var', 'phpstan-var', 'phpstan-param', 'phpstan-return', 'phpstan-template', 'phpstan-template-covariant', 'phpstan-extends', 'phpstan-implements']``

Default value: ``['method', 'param', 'property', 'return', 'throws', 'type', 'var']``

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
