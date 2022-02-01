======================
Rule ``phpdoc_scalar``
======================

Scalar types should always be written in the same form. ``int`` not ``integer``,
``bool`` not ``boolean``, ``float`` not ``real`` or ``double``.

Configuration
-------------

``types``
~~~~~~~~~

A list of types to fix.

Allowed values: a subset of ``['boolean', 'callback', 'double', 'integer', 'real', 'str']``

Default value: ``['boolean', 'callback', 'double', 'integer', 'real', 'str']``

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

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``phpdoc_scalar`` rule with the default config.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``phpdoc_scalar`` rule with the default config.
