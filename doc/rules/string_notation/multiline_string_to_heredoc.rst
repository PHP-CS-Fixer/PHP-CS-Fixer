====================================
Rule ``multiline_string_to_heredoc``
====================================

Convert multiline string to ``heredoc`` or ``nowdoc``.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$a = 'line1
   -line2';
   +$a = <<<'EOD'
   +line1
   +line2
   +EOD;

Example #2
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$a = "line1
   -{$obj->getName()}";
   +$a = <<<EOD
   +line1
   +{$obj->getName()}
   +EOD;
References
----------

- Fixer class: `PhpCsFixer\\Fixer\\StringNotation\\MultilineStringToHeredocFixer <./../../../src/Fixer/StringNotation/MultilineStringToHeredocFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\StringNotation\\MultilineStringToHeredocFixerTest <./../../../tests/Fixer/StringNotation/MultilineStringToHeredocFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
