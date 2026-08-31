=================================
Rule ``phpdoc_multi_line_throws``
=================================

Throws annotations should be either in union or multiline format.

Description
-----------

Formatting is only applied on lines without comments. Intersection types are
preserved when one of the union types is an intersection type of itself.

Warning
-------

This rule is CONFIGURABLE
~~~~~~~~~~~~~~~~~~~~~~~~~

You can configure this rule using the following option: ``format``.

Configuration
-------------

``format``
~~~~~~~~~~

Whether to use union or multiline.

Allowed values: ``'multi'`` and ``'union'``

Default value: ``'union'``

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
   - * @throws InvalidArgumentException|RuntimeException
   - * @throws DivisionByZeroException
   - * @throws Throwable&Exception
   + * @throws InvalidArgumentException|RuntimeException|DivisionByZeroException|(Throwable&Exception)
     * @throws OutOfBoundsException with a comment about this specific exception
     * @throws OverFlowException|RangeException with a comment about this specific union exception
     */

Example #2
~~~~~~~~~~

With configuration: ``['format' => 'union']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    /**
   - * @throws InvalidArgumentException|RuntimeException
   - * @throws DivisionByZeroException
   - * @throws Throwable&Exception
   + * @throws InvalidArgumentException|RuntimeException|DivisionByZeroException|(Throwable&Exception)
     * @throws OutOfBoundsException with a comment about this specific exception
     * @throws OverFlowException|RangeException with a comment about this specific union exception
     */

Example #3
~~~~~~~~~~

With configuration: ``['format' => 'multi']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    /**
   - * @throws InvalidArgumentException|RuntimeException
   + * @throws InvalidArgumentException
   + * @throws RuntimeException
     * @throws DivisionByZeroException
     * @throws Throwable&Exception
     * @throws OutOfBoundsException with a comment about this specific exception
     * @throws OverFlowException|RangeException with a comment about this specific union exception
     */

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Phpdoc\\PhpdocMultiLineThrowsFixer <./../../../src/Fixer/Phpdoc/PhpdocMultiLineThrowsFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Phpdoc\\PhpdocMultiLineThrowsFixerTest <./../../../tests/Fixer/Phpdoc/PhpdocMultiLineThrowsFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
