==========================
Rule ``return_assignment``
==========================

Local, dynamic and directly referenced variables should not be assigned and
directly returned by a function or method.

Warning
-------

This rule is CONFIGURABLE
~~~~~~~~~~~~~~~~~~~~~~~~~

You can configure this rule using the following option: ``skip_named_var_tags``.

Configuration
-------------

``skip_named_var_tags``
~~~~~~~~~~~~~~~~~~~~~~~

Whether to skip cases where named ``@var`` tags are used.

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
    <?php

    function a() {
   -    $a = 1;
   -    return $a;
   +    return 1;
    }

    function foo() {
        /** @var int[] */
   -    $a = doSomething();
   -
   -    return $a;
   +    return doSomething();
    }

    function bar() {
        /** @var int[] $b */
   -    $b = doSomething();
   -
   -    return $b;
   +    return doSomething();
    }

Example #2
~~~~~~~~~~

With configuration: ``['skip_named_var_tags' => true]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php

    function a() {
   -    $a = 1;
   -    return $a;
   +    return 1;
    }

    function foo() {
        /** @var int[] */
   -    $a = doSomething();
   -
   -    return $a;
   +    return doSomething();
    }

    function bar() {
        /** @var int[] $b */
        $b = doSomething();

        return $b;
    }

Rule sets
---------

The rule is part of the following rule set:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\ReturnNotation\\ReturnAssignmentFixer <./../../../src/Fixer/ReturnNotation/ReturnAssignmentFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\ReturnNotation\\ReturnAssignmentFixerTest <./../../../tests/Fixer/ReturnNotation/ReturnAssignmentFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
