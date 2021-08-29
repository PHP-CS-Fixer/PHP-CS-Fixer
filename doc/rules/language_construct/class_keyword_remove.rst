=============================
Rule ``class_keyword_remove``
=============================

.. warning:: This rule is deprecated and will be removed on next major version.

Converts ``::class`` keywords to FQCN strings.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php

    use Foo\Bar\Baz;

   -$className = Baz::class;
   +$className = 'Foo\Bar\Baz';
