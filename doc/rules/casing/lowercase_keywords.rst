===========================
Rule ``lowercase_keywords``
===========================

PHP keywords MUST be in lower case.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -    FOREACH($a AS $B) {
   -        TRY {
   -            NEW $C($a, ISSET($B));
   -            WHILE($B) {
   -                INCLUDE "test.php";
   +    foreach($a as $B) {
   +        try {
   +            new $C($a, isset($B));
   +            while($B) {
   +                include "test.php";
                }
   -        } CATCH(\Exception $e) {
   -            EXIT(1);
   +        } catch(\Exception $e) {
   +            exit(1);
            }
        }

Rule sets
---------

The rule is part of the following rule sets:

- `@PER <./../../ruleSets/PER.rst>`_
- `@PER-CS <./../../ruleSets/PER-CS.rst>`_
- `@PER-CS1.0 <./../../ruleSets/PER-CS1.0.rst>`_
- `@PER-CS1x0 <./../../ruleSets/PER-CS1x0.rst>`_
- `@PER-CS2.0 <./../../ruleSets/PER-CS2.0.rst>`_
- `@PER-CS2x0 <./../../ruleSets/PER-CS2x0.rst>`_
- `@PER-CS3.0 <./../../ruleSets/PER-CS3.0.rst>`_
- `@PER-CS3x0 <./../../ruleSets/PER-CS3x0.rst>`_
- `@PSR2 <./../../ruleSets/PSR2.rst>`_
- `@PSR12 <./../../ruleSets/PSR12.rst>`_
- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Casing\\LowercaseKeywordsFixer <./../../../src/Fixer/Casing/LowercaseKeywordsFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Casing\\LowercaseKeywordsFixerTest <./../../../tests/Fixer/Casing/LowercaseKeywordsFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
