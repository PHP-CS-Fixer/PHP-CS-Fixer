========================================
Rule ``whitespace_after_comma_in_array``
========================================

In array declaration, there MUST be a whitespace after each comma.

Configuration
-------------

``ensure_single_space``
~~~~~~~~~~~~~~~~~~~~~~~

If there are only horizontal whitespaces after the comma then ensure it is a
single space.

Allowed types: ``bool``

Default value: ``false``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$sample = array(1,'a',$b,);
   +$sample = array(1, 'a', $b, );

Example #2
~~~~~~~~~~

With configuration: ``['ensure_single_space' => true]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$sample = [1,2, 3,  4,    5];
   +$sample = [1, 2, 3, 4, 5];

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ with config:

  ``['ensure_single_space' => true]``

- `@Symfony <./../../ruleSets/Symfony.rst>`_

