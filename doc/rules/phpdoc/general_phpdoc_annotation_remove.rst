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
     */
    function foo() {}

Example #2
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
