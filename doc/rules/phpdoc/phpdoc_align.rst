=====================
Rule ``phpdoc_align``
=====================

All items of the given PHPDoc tags must be either left-aligned or (by default)
aligned vertically.

Configuration
-------------

``align``
~~~~~~~~~

How comments should be aligned.

Allowed values: ``'left'``, ``'left_multiline'`` and ``'vertical'``

Default value: ``'vertical'``

``spacing``
~~~~~~~~~~~

Spacing between tag, hint, comment, signature, etc. You can set same spacing for
all tags using a positive integer or different spacings for different tags using
an associative array of positive integers ``['tagA' => spacingForA, 'tagB' =>
spacingForB]``. If you want to define default spacing to more than 1 space use
``_default`` key in config array, e.g.: ``['tagA' => spacingForA, 'tagB' =>
spacingForB, '_multiline' => spacingForMultiline, '_default' =>
spacingForAllOthers]``.

Allowed types: ``int`` and ``array<string, int>``

Default value: ``1``

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

Allowed types: ``list<string>``

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
     *
     * @return Foo description foo
     *
   - * @throws Foo            description foo
   + * @throws Foo description foo
     *             description foo
     *
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
     *
     * @return Foo description foo
     *
   - * @throws Foo            description foo
   + * @throws Foo description foo
     *             description foo
     *
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
     *
     * @return Foo description foo
     *
   - * @throws Foo            description foo
   + * @throws Foo description foo
     *             description foo
     *
     */

Example #4
~~~~~~~~~~

With configuration: ``['align' => 'left', 'spacing' => 2]``.

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
   + * @param  EngineInterface  $templating
   + * @param  string  $format
   + * @param  int  $code  an HTTP response status code
   + * @param  bool  $debug
   + * @param  mixed  &$reference  a parameter passed by reference
     *
   - * @return Foo description foo
   + * @return  Foo  description foo
     *
   - * @throws Foo            description foo
   - *             description foo
   + * @throws  Foo  description foo
   + *               description foo
     *
     */

Example #5
~~~~~~~~~~

With configuration: ``['align' => 'left', 'spacing' => ['param' => 2]]``.

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
   + * @param  EngineInterface  $templating
   + * @param  string  $format
   + * @param  int  $code  an HTTP response status code
   + * @param  bool  $debug
   + * @param  mixed  &$reference  a parameter passed by reference
     *
     * @return Foo description foo
     *
   - * @throws Foo            description foo
   + * @throws Foo description foo
     *             description foo
     *
     */

Example #6
~~~~~~~~~~

With configuration: ``['align' => 'left_multiline']``.

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
     *
     * @return Foo description foo
     *
   - * @throws Foo            description foo
   - *             description foo
   + * @throws Foo description foo
   + *     description foo
     *
     */

Example #7
~~~~~~~~~~

With configuration: ``['align' => 'left_multiline', 'spacing' => ['_multiline' => 4]]``.

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
     *
     * @return Foo description foo
     *
   - * @throws Foo            description foo
   - *             description foo
   + * @throws Foo description foo
   + *     description foo
     *
     */

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Phpdoc\\PhpdocAlignFixer <./../../../src/Fixer/Phpdoc/PhpdocAlignFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Phpdoc\\PhpdocAlignFixerTest <./../../../tests/Fixer/Phpdoc/PhpdocAlignFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
