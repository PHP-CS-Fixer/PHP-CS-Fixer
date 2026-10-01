=============================
Rule ``phpdoc_nullable_type``
=============================

Nullable PHPDoc types should be standardised using configured syntax.

Warning
-------

This rule is CONFIGURABLE
~~~~~~~~~~~~~~~~~~~~~~~~~

You can configure this rule using the following option: ``syntax``.

Configuration
-------------

``syntax``
~~~~~~~~~~

Whether to use question mark (``?``) or explicit ``null`` union for nullable
PHPDoc types.

Allowed values: ``'question_mark'`` and ``'union'``

Default value: ``'question_mark'``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -/** @var null|Foo */
   +/** @var ?Foo */

Example #2
~~~~~~~~~~

With configuration: ``['syntax' => 'union']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -/** @var ?Foo */
   +/** @var null|Foo */

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Phpdoc\\PhpdocNullableTypeFixer <./../../../src/Fixer/Phpdoc/PhpdocNullableTypeFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Phpdoc\\PhpdocNullableTypeFixerTest <./../../../tests/Fixer/Phpdoc/PhpdocNullableTypeFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
