=============================
Rule ``named_argument_space``
=============================

There MUST NOT be a space between the argument name and colon, and there MUST be
a single space between the colon and the argument value.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -foo(foo  :1);
   +foo(foo: 1);

Rule sets
---------

The rule is part of the following rule sets:

- `@PER <./../../ruleSets/PER.rst>`_
- `@PER-CS <./../../ruleSets/PER-CS.rst>`_
- `@PER-CS2.0 <./../../ruleSets/PER-CS2.0.rst>`_
- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_

