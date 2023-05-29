============================
Rule ``const_type_required``
============================

Class constants must be typed.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
    interface Doc
    {
   -    const URL = "https://github.com/FriendsOfPHP/PHP-CS-Fixer/";
   +    const string URL = "https://github.com/FriendsOfPHP/PHP-CS-Fixer/";
    }
