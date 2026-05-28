=========================================
Rule ``general_phpdoc_annotation_remove``
=========================================

Removes configured annotations from PHPDoc.

Warning
-------

This rule is CONFIGURABLE
~~~~~~~~~~~~~~~~~~~~~~~~~

You can configure this rule using the following options: ``annotations``,
``case_sensitive``.

Configuration
-------------

``annotations``
~~~~~~~~~~~~~~~

List of annotations to remove, e.g. ``["author"]``.

Allowed types: ``list<string>``

Default value: ``[]``

``case_sensitive``
~~~~~~~~~~~~~~~~~~

Should annotations be case sensitive.

Allowed types: ``bool``

Default value: ``true``

Examples
--------

Example #1
~~~~~~~~~~

With configuration: ``['annotations' => ['author']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    /**
     * @internal
   - * @author John Doe
     * @AuThOr Jane Doe
     */
    function foo() {}

Example #2
~~~~~~~~~~

With configuration: ``['annotations' => ['author'], 'case_sensitive' => false]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    /**
     * @internal
   - * @author John Doe
   - * @AuThOr Jane Doe
     */
    function foo() {}

Example #3
~~~~~~~~~~

With configuration: ``['annotations' => ['package', 'subpackage']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    /**
     * @author John Doe
   - * @package ACME API
   - * @subpackage Authorization
     * @version 1.0
     */
    function foo() {}

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Phpdoc\\GeneralPhpdocAnnotationRemoveFixer <./../../../src/Fixer/Phpdoc/GeneralPhpdocAnnotationRemoveFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Phpdoc\\GeneralPhpdocAnnotationRemoveFixerTest <./../../../tests/Fixer/Phpdoc/GeneralPhpdocAnnotationRemoveFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
