=====================
Rule ``phpdoc_align``
=====================

All items of the given phpdoc tags must be either left-aligned or (by default)
aligned vertically.

Configuration
-------------

``align``
~~~~~~~~~

How comments should be aligned.

Allowed values: ``'left'`` and ``'vertical'``

Default value: ``'vertical'``

``tags``
~~~~~~~~

The tags that should be aligned. Allowed values are tags with name (``'param',
'property', 'property-read', 'property-write', 'phpstan-param',
'phpstan-property', 'phpstan-property-read', 'phpstan-property-write',
'phpstan-assert', 'phpstan-assert-if-true', 'phpstan-assert-if-false',
'psalm-param', 'psalm-param-out', 'psalm-property', 'psalm-property-read',
'psalm-property-write', 'psalm-assert', 'psalm-assert-if-true',
'psalm-assert-if-false'``), tags with method signature (``'method',
'phpstan-method', 'psalm-method'``) and any custom tag with description (e.g.
``@tag <desc>``).

Allowed types: ``array``

Default value: ``['method', 'param', 'property', 'return', 'throws', 'type', 'var']``

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

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

Source class
------------

`PhpCsFixer\\Fixer\\Phpdoc\\PhpdocAlignFixer <./../src/Fixer/Phpdoc/PhpdocAlignFixer.php>`_
