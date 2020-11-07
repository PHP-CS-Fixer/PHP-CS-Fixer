========================================
Rule ``doctrine_annotation_indentation``
========================================

Doctrine annotations must be indented with four spaces.

Configuration
-------------

``ignored_tags``
~~~~~~~~~~~~~~~~

List of tags that must not be treated as Doctrine Annotations.

Allowed types: ``array``

Default value: ``['abstract', 'access', 'code', 'deprec', 'encode', 'exception', 'final', 'ingroup', 'inheritdoc', 'inheritDoc', 'magic', 'name', 'toc', 'tutorial', 'private', 'static', 'staticvar', 'staticVar', 'throw', 'api', 'author', 'category', 'copyright', 'deprecated', 'example', 'filesource', 'global', 'ignore', 'internal', 'license', 'link', 'method', 'package', 'param', 'property', 'property-read', 'property-write', 'return', 'see', 'since', 'source', 'subpackage', 'throws', 'todo', 'TODO', 'usedBy', 'uses', 'var', 'version', 'after', 'afterClass', 'backupGlobals', 'backupStaticAttributes', 'before', 'beforeClass', 'codeCoverageIgnore', 'codeCoverageIgnoreStart', 'codeCoverageIgnoreEnd', 'covers', 'coversDefaultClass', 'coversNothing', 'dataProvider', 'depends', 'expectedException', 'expectedExceptionCode', 'expectedExceptionMessage', 'expectedExceptionMessageRegExp', 'group', 'large', 'medium', 'preserveGlobalState', 'requires', 'runTestsInSeparateProcesses', 'runInSeparateProcess', 'small', 'test', 'testdox', 'ticket', 'uses', 'SuppressWarnings', 'noinspection', 'package_version', 'enduml', 'startuml', 'fix', 'FIXME', 'fixme', 'override']``

``indent_mixed_lines``
~~~~~~~~~~~~~~~~~~~~~~

Whether to indent lines that have content before closing parenthesis.

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
   @@ -1,7 +1,7 @@
    <?php
    /**
   - *  @Foo(
   - *   foo="foo"
   - *  )
   + * @Foo(
   + *     foo="foo"
   + * )
     */
    class Bar {}

Example #2
~~~~~~~~~~

With configuration: ``['indent_mixed_lines' => true]``.

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,6 +1,6 @@
    <?php
    /**
   - *  @Foo({@Bar,
   - *   @Baz})
   + * @Foo({@Bar,
   + *     @Baz})
     */
    class Bar {}

Rule sets
---------

The rule is part of the following rule set:

@DoctrineAnnotation
  Using the `@DoctrineAnnotation <./../../ruleSets/DoctrineAnnotation.rst>`_ rule set will enable the ``doctrine_annotation_indentation`` rule with the default config.
