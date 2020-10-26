=============================
Rule ``class_keyword_remove``
=============================

Converts ``::class`` keywords to FQCN strings.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -2,4 +2,4 @@

    use Foo\Bar\Baz;

   -$className = Baz::class;
   +$className = 'Foo\Bar\Baz';
