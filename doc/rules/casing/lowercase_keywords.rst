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
   @@ -1,11 +1,11 @@
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

@PSR2
  Using the ``@PSR2`` rule set will enable the ``lowercase_keywords`` rule.

@Symfony
  Using the ``@Symfony`` rule set will enable the ``lowercase_keywords`` rule.

@PhpCsFixer
  Using the ``@PhpCsFixer`` rule set will enable the ``lowercase_keywords`` rule.
