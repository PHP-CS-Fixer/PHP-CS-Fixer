=====================================
Rule ``single_space_after_construct``
=====================================

Ensures a single space after language constructs.

Warning
-------

This rule is deprecated and will be removed in the next major version
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

You should use ``single_space_around_construct`` instead.

Configuration
-------------

``constructs``
~~~~~~~~~~~~~~

List of constructs which must be followed by a single space.

Allowed values: a subset of ``['abstract', 'as', 'attribute', 'break', 'case', 'catch', 'class', 'clone', 'comment', 'const', 'const_import', 'continue', 'do', 'echo', 'else', 'elseif', 'enum', 'extends', 'final', 'finally', 'for', 'foreach', 'function', 'function_import', 'global', 'goto', 'if', 'implements', 'include', 'include_once', 'instanceof', 'insteadof', 'interface', 'match', 'named_argument', 'namespace', 'new', 'open_tag_with_echo', 'php_doc', 'php_open', 'print', 'private', 'protected', 'public', 'readonly', 'require', 'require_once', 'return', 'static', 'switch', 'throw', 'trait', 'try', 'type_colon', 'use', 'use_lambda', 'use_trait', 'var', 'while', 'yield', 'yield_from']``

Default value: ``['abstract', 'as', 'attribute', 'break', 'case', 'catch', 'class', 'clone', 'comment', 'const', 'const_import', 'continue', 'do', 'echo', 'else', 'elseif', 'enum', 'extends', 'final', 'finally', 'for', 'foreach', 'function', 'function_import', 'global', 'goto', 'if', 'implements', 'include', 'include_once', 'instanceof', 'insteadof', 'interface', 'match', 'named_argument', 'namespace', 'new', 'open_tag_with_echo', 'php_doc', 'php_open', 'print', 'private', 'protected', 'public', 'readonly', 'require', 'require_once', 'return', 'static', 'switch', 'throw', 'trait', 'try', 'use', 'use_lambda', 'use_trait', 'var', 'while', 'yield', 'yield_from']``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php

   -throw  new  \Exception();
   +throw new \Exception();

Example #2
~~~~~~~~~~

With configuration: ``['constructs' => ['echo']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php

   -echo  "Hello!";
   +echo "Hello!";

Example #3
~~~~~~~~~~

With configuration: ``['constructs' => ['yield_from']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php

   -yield  from  baz();
   +yield from baz();
References
----------

- Fixer class: `PhpCsFixer\\Fixer\\LanguageConstruct\\SingleSpaceAfterConstructFixer <./../../../src/Fixer/LanguageConstruct/SingleSpaceAfterConstructFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\LanguageConstruct\\SingleSpaceAfterConstructFixerTest <./../../../tests/Fixer/LanguageConstruct/SingleSpaceAfterConstructFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
