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
- `@PER-CS1.0 <./../../ruleSets/PER-CS1.0.rst>`_
- `@PER-CS2.0 <./../../ruleSets/PER-CS2.0.rst>`_
- `@PSR2 <./../../ruleSets/PSR2.rst>`_
- `@PSR12 <./../../ruleSets/PSR12.rst>`_
- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

