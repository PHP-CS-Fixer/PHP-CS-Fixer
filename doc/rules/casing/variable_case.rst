======================
Rule ``variable_case``
======================

Enforce camel or snake case for variables, following configuration.

Description
-----------

Keeps variables' names consistent across the project.

Warning
-------

Using this rule is risky
~~~~~~~~~~~~~~~~~~~~~~~~

Fixer could be risky if renamed variables are defined outside of the modified
file.

Configuration
-------------

``case``
~~~~~~~~

Apply ``camel_case`` or ``snake_case`` to variables.

Allowed values: ``'camel_case'`` and ``'snake_case'``

Default value: ``'camel_case'``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
   -<?php $my_variable = 2;
   +<?php $myVariable = 2;

Example #2
~~~~~~~~~~

With configuration: ``['case' => 'snake_case']``.

.. code-block:: diff

   --- Original
   +++ New
   -<?php $myVariable = 2;
   +<?php $my_variable = 2;
Source class
------------

`PhpCsFixer\\Fixer\\Casing\\VariableCaseFixer <./../../../src/Fixer/Casing/VariableCaseFixer.php>`_
