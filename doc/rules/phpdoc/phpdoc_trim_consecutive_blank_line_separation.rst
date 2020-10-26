======================================================
Rule ``phpdoc_trim_consecutive_blank_line_separation``
======================================================

Removes extra blank lines after summary and after description in PHPDoc.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -2,16 +2,13 @@
    /**
     * Summary.
     *
   - *
     * Description that contain 4 lines,
     *
     *
     * while 2 of them are blank!
     *
   - *
     * @param string $foo
   - *
     *
     * @dataProvider provideFixCases
     */
    function fnc($foo) {}

Rule sets
---------

The rule is part of the following rule sets:

@Symfony
  Using the ``@Symfony`` rule set will enable the ``phpdoc_trim_consecutive_blank_line_separation`` rule.

@PhpCsFixer
  Using the ``@PhpCsFixer`` rule set will enable the ``phpdoc_trim_consecutive_blank_line_separation`` rule.
