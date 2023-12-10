=========================================
Rule ``general_phpdoc_annotation_remove``
=========================================

Configured annotations should be omitted from PHPDoc.

Configuration
-------------

``annotations``
~~~~~~~~~~~~~~~

List of annotations to remove, e.g. ``["author"]``.

Allowed types: ``array``

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
Source class
------------

`PhpCsFixer\\Fixer\\Phpdoc\\GeneralPhpdocAnnotationRemoveFixer <./../../../src/Fixer/Phpdoc/GeneralPhpdocAnnotationRemoveFixer.php>`_
