=============================================
Rule ``doctrine_annotation_array_assignment``
=============================================

Doctrine annotations must use configured operator for assignment in arrays.

Warning
-------

This rule is CONFIGURABLE
~~~~~~~~~~~~~~~~~~~~~~~~~

You can configure this rule using the following options: ``ignored_tags``,
``operator``.

Configuration
-------------

``ignored_tags``
~~~~~~~~~~~~~~~~

List of tags that must not be treated as Doctrine Annotations.

Allowed types: ``list<string>``

Default value: ``['abstract', 'access', 'code', 'deprec', 'encode', 'exception', 'final', 'ingroup', 'inheritdoc', 'inheritDoc', 'magic', 'name', 'toc', 'tutorial', 'private', 'static', 'staticvar', 'staticVar', 'throw', 'api', 'author', 'category', 'copyright', 'deprecated', 'example', 'filesource', 'global', 'ignore', 'internal', 'license', 'link', 'method', 'package', 'param', 'property', 'property-read', 'property-write', 'return', 'see', 'since', 'source', 'subpackage', 'throws', 'todo', 'TODO', 'usedBy', 'uses', 'var', 'version', 'after', 'afterClass', 'backupGlobals', 'backupStaticAttributes', 'before', 'beforeClass', 'codeCoverageIgnore', 'codeCoverageIgnoreStart', 'codeCoverageIgnoreEnd', 'covers', 'coversDefaultClass', 'coversNothing', 'dataProvider', 'depends', 'expectedException', 'expectedExceptionCode', 'expectedExceptionMessage', 'expectedExceptionMessageRegExp', 'group', 'large', 'medium', 'preserveGlobalState', 'requires', 'runTestsInSeparateProcesses', 'runInSeparateProcess', 'small', 'test', 'testdox', 'ticket', 'uses', 'SuppressWarnings', 'noinspection', 'package_version', 'enduml', 'startuml', 'psalm', 'phpstan', 'template', 'fix', 'FIXME', 'fixme', 'override']``

``operator``
~~~~~~~~~~~~

The operator to use.

Allowed values: ``':'`` and ``'='``

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

- `@DoctrineAnnotation <./../../ruleSets/DoctrineAnnotation.rst>`_ with config:

  ``['operator' => ':']``

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\DoctrineAnnotation\\DoctrineAnnotationArrayAssignmentFixer <./../../../src/Fixer/DoctrineAnnotation/DoctrineAnnotationArrayAssignmentFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\DoctrineAnnotation\\DoctrineAnnotationArrayAssignmentFixerTest <./../../../tests/Fixer/DoctrineAnnotation/DoctrineAnnotationArrayAssignmentFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
