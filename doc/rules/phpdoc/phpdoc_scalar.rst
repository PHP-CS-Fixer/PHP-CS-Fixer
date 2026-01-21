======================
Rule ``phpdoc_scalar``
======================

Scalar types should always be written in the same form. ``int`` not ``integer``,
``bool`` not ``boolean``, ``float`` not ``real`` or ``double``.

Warning
-------

This rule is CONFIGURABLE
~~~~~~~~~~~~~~~~~~~~~~~~~

You can configure this rule using the following option: ``types``.

Configuration
-------------

``types``
~~~~~~~~~

A list of types to fix.

Allowed values: a subset of ``['boolean', 'callback', 'double', 'integer', 'never-return', 'never-returns', 'no-return', 'real', 'str']``

Default value: ``['boolean', 'callback', 'double', 'integer', 'real', 'str']``

Default value (future-mode): ``['boolean', 'callback', 'double', 'integer', 'never-return', 'never-returns', 'no-return', 'real', 'str']``

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
   - * @param integer $a
   - * @param boolean $b
   - * @param real $c
   + * @param int $a
   + * @param bool $b
   + * @param float $c
     *
   - * @return double
   + * @return float
     */
    function sample($a, $b, $c)
    {
        return sample2($a, $b, $c);
    }

Example #2
~~~~~~~~~~

With configuration: ``['types' => ['boolean']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    /**
     * @param integer $a
   - * @param boolean $b
   + * @param bool $b
     * @param real $c
     */
    function sample($a, $b, $c)
    {
        return sample2($a, $b, $c);
    }

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ with config:

  ``['types' => ['boolean', 'callback', 'double', 'integer', 'never-return', 'never-returns', 'no-return', 'real', 'str']]``

- `@Symfony <./../../ruleSets/Symfony.rst>`_ with config:

  ``['types' => ['boolean', 'callback', 'double', 'integer', 'never-return', 'never-returns', 'no-return', 'real', 'str']]``

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Phpdoc\\PhpdocScalarFixer <./../../../src/Fixer/Phpdoc/PhpdocScalarFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Phpdoc\\PhpdocScalarFixerTest <./../../../tests/Fixer/Phpdoc/PhpdocScalarFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
