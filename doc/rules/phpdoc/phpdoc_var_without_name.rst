================================
Rule ``phpdoc_var_without_name``
================================

``@var`` and ``@type`` annotations of classy properties should not contain the
name.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
    final class Foo
    {
        /**
   -     * @var int $bar
   +     * @var int
         */
        public $bar;

        /**
   -     * @type $baz float
   +     * @type float
         */
        public $baz;
    }

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

Source class
------------

`PhpCsFixer\\Fixer\\Phpdoc\\PhpdocVarWithoutNameFixer <./../src/Fixer/Phpdoc/PhpdocVarWithoutNameFixer.php>`_
