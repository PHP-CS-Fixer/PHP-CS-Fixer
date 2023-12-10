===================================
Rule ``doctrine_annotation_braces``
===================================

Doctrine annotations without arguments must use the configured syntax.

Configuration
-------------

``ignored_tags``
~~~~~~~~~~~~~~~~

List of tags that must not be treated as Doctrine Annotations.

Allowed types: ``array``

Default value: ``['abstract', 'access', 'code', 'deprec', 'encode', 'exception', 'final', 'ingroup', 'inheritdoc', 'inheritDoc', 'magic', 'name', 'toc', 'tutorial', 'private', 'static', 'staticvar', 'staticVar', 'throw', 'api', 'author', 'category', 'copyright', 'deprecated', 'example', 'filesource', 'global', 'ignore', 'internal', 'license', 'link', 'method', 'package', 'param', 'property', 'property-read', 'property-write', 'return', 'see', 'since', 'source', 'subpackage', 'throws', 'todo', 'TODO', 'usedBy', 'uses', 'var', 'version', 'after', 'afterClass', 'backupGlobals', 'backupStaticAttributes', 'before', 'beforeClass', 'codeCoverageIgnore', 'codeCoverageIgnoreStart', 'codeCoverageIgnoreEnd', 'covers', 'coversDefaultClass', 'coversNothing', 'dataProvider', 'depends', 'expectedException', 'expectedExceptionCode', 'expectedExceptionMessage', 'expectedExceptionMessageRegExp', 'group', 'large', 'medium', 'preserveGlobalState', 'requires', 'runTestsInSeparateProcesses', 'runInSeparateProcess', 'small', 'test', 'testdox', 'ticket', 'uses', 'SuppressWarnings', 'noinspection', 'package_version', 'enduml', 'startuml', 'psalm', 'phpstan', 'template', 'fix', 'FIXME', 'fixme', 'override']``

``syntax``
~~~~~~~~~~

Whether to add or remove braces.

Allowed values: ``'with_braces'`` and ``'without_braces'``

Default value: ``'without_braces'``

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
   - * @Foo()
   + * @Foo
     */
    class Bar {}

Example #2
~~~~~~~~~~

With configuration: ``['syntax' => 'with_braces']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    /**
   - * @Foo
   + * @Foo()
     */
    class Bar {}

Rule sets
---------

The rule is part of the following rule set:

- `@DoctrineAnnotation <./../../ruleSets/DoctrineAnnotation.rst>`_

Source class
------------

`PhpCsFixer\\Fixer\\DoctrineAnnotation\\DoctrineAnnotationBracesFixer <./../../../src/Fixer/DoctrineAnnotation/DoctrineAnnotationBracesFixer.php>`_
