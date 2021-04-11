=============================================
Rule ``doctrine_annotation_array_assignment``
=============================================

Doctrine annotations must use configured operator for assignment in arrays.

Configuration
-------------

``ignored_tags``
~~~~~~~~~~~~~~~~

List of tags that must not be treated as Doctrine Annotations.

Allowed types: ``array``

Default value: ``['abstract', 'access', 'code', 'deprec', 'encode', 'exception', 'final', 'ingroup', 'inheritdoc', 'inheritDoc', 'magic', 'name', 'toc', 'tutorial', 'private', 'static', 'staticvar', 'staticVar', 'throw', 'api', 'author', 'category', 'copyright', 'deprecated', 'example', 'filesource', 'global', 'ignore', 'internal', 'license', 'link', 'method', 'package', 'param', 'property', 'property-read', 'property-write', 'return', 'see', 'since', 'source', 'subpackage', 'throws', 'todo', 'TODO', 'usedBy', 'uses', 'var', 'version', 'after', 'afterClass', 'backupGlobals', 'backupStaticAttributes', 'before', 'beforeClass', 'codeCoverageIgnore', 'codeCoverageIgnoreStart', 'codeCoverageIgnoreEnd', 'covers', 'coversDefaultClass', 'coversNothing', 'dataProvider', 'depends', 'expectedException', 'expectedExceptionCode', 'expectedExceptionMessage', 'expectedExceptionMessageRegExp', 'group', 'large', 'medium', 'preserveGlobalState', 'requires', 'runTestsInSeparateProcesses', 'runInSeparateProcess', 'small', 'test', 'testdox', 'ticket', 'uses', 'SuppressWarnings', 'noinspection', 'package_version', 'enduml', 'startuml', 'psalm', 'phpstan', 'fix', 'FIXME', 'fixme', 'override']``

``operator``
~~~~~~~~~~~~

The operator to use.

Allowed values: ``':'``, ``'='``

Default value: ``'='``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    /**
   - * @Foo({bar : "baz"})
   + * @Foo({bar = "baz"})
     */
    class Bar {}

Example #2
~~~~~~~~~~

With configuration: ``['operator' => ':']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    /**
   - * @Foo({bar = "baz"})
   + * @Foo({bar : "baz"})
     */
    class Bar {}

Rule sets
---------

The rule is part of the following rule set:

@DoctrineAnnotation
  Using the `@DoctrineAnnotation <./../../ruleSets/DoctrineAnnotation.rst>`_ rule set will enable the ``doctrine_annotation_array_assignment`` rule with the config below:

  ``['operator' => ':']``
