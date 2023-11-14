======================================
Rule ``phpdoc_annotation_without_dot``
======================================

PHPDoc annotation descriptions should not be a sentence.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
    /**
   - * @param string $bar Some string.
   + * @param string $bar some string
     */
    function foo ($bar) {}

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

Source class
------------

`PhpCsFixer\\Fixer\\Phpdoc\\PhpdocAnnotationWithoutDotFixer <./../src/Fixer/Phpdoc/PhpdocAnnotationWithoutDotFixer.php>`_
