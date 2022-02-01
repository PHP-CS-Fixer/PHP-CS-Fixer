==========================
Rule ``switch_case_space``
==========================

Removes extra spaces between colon and case value.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
        switch($a) {
   -        case 1   :
   +        case 1:
                break;
   -        default     :
   +        default:
                return 2;
        }

Rule sets
---------

The rule is part of the following rule sets:

@PSR12
  Using the `@PSR12 <./../../ruleSets/PSR12.rst>`_ rule set will enable the ``switch_case_space`` rule.

@PSR2
  Using the `@PSR2 <./../../ruleSets/PSR2.rst>`_ rule set will enable the ``switch_case_space`` rule.

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``switch_case_space`` rule.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``switch_case_space`` rule.
