===========================
Rule ``phpdoc_param_order``
===========================

Orders all ``@param`` annotations in DocBlocks according to method signature.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
    /**
     * Annotations in wrong order
     *
     * @param int   $a
   + * @param array $b
     * @param Foo   $c
   - * @param array $b
     */
    function m($a, array $b, Foo $c) {}
Source class
------------

`PhpCsFixer\\Fixer\\Phpdoc\\PhpdocParamOrderFixer <./../src/Fixer/Phpdoc/PhpdocParamOrderFixer.php>`_
