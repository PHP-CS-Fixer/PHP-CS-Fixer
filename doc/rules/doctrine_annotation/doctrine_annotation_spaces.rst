===================================
Rule ``doctrine_annotation_spaces``
===================================

Fixes spaces in Doctrine annotations.

Description
-----------

There must not be any space around parentheses; commas must be preceded by no
space and followed by one space; there must be no space around named arguments
assignment operator; there must be one space around array assignment operator.

Configuration
-------------

``ignored_tags``
~~~~~~~~~~~~~~~~

List of tags that must not be treated as Doctrine Annotations.

Allowed types: ``array``

Default value: ``['abstract', 'access', 'code', 'deprec', 'encode', 'exception', 'final', 'ingroup', 'inheritdoc', 'inheritDoc', 'magic', 'name', 'toc', 'tutorial', 'private', 'static', 'staticvar', 'staticVar', 'throw', 'api', 'author', 'category', 'copyright', 'deprecated', 'example', 'filesource', 'global', 'ignore', 'internal', 'license', 'link', 'method', 'package', 'param', 'property', 'property-read', 'property-write', 'return', 'see', 'since', 'source', 'subpackage', 'throws', 'todo', 'TODO', 'usedBy', 'uses', 'var', 'version', 'after', 'afterClass', 'backupGlobals', 'backupStaticAttributes', 'before', 'beforeClass', 'codeCoverageIgnore', 'codeCoverageIgnoreStart', 'codeCoverageIgnoreEnd', 'covers', 'coversDefaultClass', 'coversNothing', 'dataProvider', 'depends', 'expectedException', 'expectedExceptionCode', 'expectedExceptionMessage', 'expectedExceptionMessageRegExp', 'group', 'large', 'medium', 'preserveGlobalState', 'requires', 'runTestsInSeparateProcesses', 'runInSeparateProcess', 'small', 'test', 'testdox', 'ticket', 'uses', 'SuppressWarnings', 'noinspection', 'package_version', 'enduml', 'startuml', 'fix', 'FIXME', 'fixme', 'override']``

``around_parentheses``
~~~~~~~~~~~~~~~~~~~~~~

Whether to fix spaces around parentheses.

Allowed types: ``bool``

Default value: ``true``

``around_commas``
~~~~~~~~~~~~~~~~~

Whether to fix spaces around commas.

Allowed types: ``bool``

Default value: ``true``

``around_argument_assignments``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. warning:: This option is deprecated and will be removed on next major version. Use options ``before_argument_assignments`` and ``after_argument_assignments``
instead.

Whether to fix spaces around argument assignment operator.

Allowed types: ``bool``

Default value: ``true``

``before_argument_assignments``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Whether to add, remove or ignore spaces before argument assignment operator.

Allowed types: ``null``, ``bool``

Default value: ``false``

``after_argument_assignments``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Whether to add, remove or ignore spaces after argument assignment operator.

Allowed types: ``null``, ``bool``

Default value: ``false``

``around_array_assignments``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. warning:: This option is deprecated and will be removed on next major version. Use options ``before_array_assignments_equals``,
``after_array_assignments_equals``, ``before_array_assignments_colon`` and
``after_array_assignments_colon`` instead.

Whether to fix spaces around array assignment operators.

Allowed types: ``bool``

Default value: ``true``

``before_array_assignments_equals``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Whether to add, remove or ignore spaces before array ``=`` assignment operator.

Allowed types: ``null``, ``bool``

Default value: ``true``

``after_array_assignments_equals``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Whether to add, remove or ignore spaces after array assignment ``=`` operator.

Allowed types: ``null``, ``bool``

Default value: ``true``

``before_array_assignments_colon``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Whether to add, remove or ignore spaces before array ``:`` assignment operator.

Allowed types: ``null``, ``bool``

Default value: ``true``

``after_array_assignments_colon``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Whether to add, remove or ignore spaces after array assignment ``:`` operator.

Allowed types: ``null``, ``bool``

Default value: ``true``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,15 +1,15 @@
    <?php
    /**
   - * @Foo ( )
   + * @Foo()
     */
    class Bar {}

    /**
   - * @Foo("bar" ,"baz")
   + * @Foo("bar", "baz")
     */
    class Bar2 {}

    /**
   - * @Foo(foo = "foo", bar = {"foo":"foo", "bar"="bar"})
   + * @Foo(foo="foo", bar={"foo" : "foo", "bar" = "bar"})
     */
    class Bar3 {}

Example #2
~~~~~~~~~~

With configuration: ``['after_array_assignments_equals' => false, 'before_array_assignments_equals' => false]``.

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,5 +1,5 @@
    <?php
    /**
   - * @Foo(foo = "foo", bar = {"foo":"foo", "bar"="bar"})
   + * @Foo(foo="foo", bar={"foo" : "foo", "bar"="bar"})
     */
    class Bar {}

Rule sets
---------

The rule is part of the following rule set:

@DoctrineAnnotation
  Using the ``@DoctrineAnnotation`` rule set will enable the ``doctrine_annotation_spaces`` rule with the config below:

  ``['before_array_assignments_colon' => false]``
