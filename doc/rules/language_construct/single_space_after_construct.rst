=====================================
Rule ``single_space_after_construct``
=====================================

Ensures a single space after language constructs.

Configuration
-------------

``constructs``
~~~~~~~~~~~~~~

List of constructs which must be followed by a single space.

Allowed values: a subset of ``['abstract', 'as', 'attribute', 'break', 'case', 'catch', 'class', 'clone', 'comment', 'const', 'const_import', 'continue', 'do', 'echo', 'else', 'elseif', 'extends', 'final', 'finally', 'for', 'foreach', 'function', 'function_import', 'global', 'goto', 'if', 'implements', 'include', 'include_once', 'instanceof', 'insteadof', 'interface', 'match', 'named_argument', 'new', 'open_tag_with_echo', 'php_doc', 'php_open', 'print', 'private', 'protected', 'public', 'require', 'require_once', 'return', 'static', 'throw', 'trait', 'try', 'use', 'use_lambda', 'use_trait', 'var', 'while', 'yield', 'yield_from']``

Default value: ``['abstract', 'as', 'attribute', 'break', 'case', 'catch', 'class', 'clone', 'comment', 'const', 'const_import', 'continue', 'do', 'echo', 'else', 'elseif', 'extends', 'final', 'finally', 'for', 'foreach', 'function', 'function_import', 'global', 'goto', 'if', 'implements', 'include', 'include_once', 'instanceof', 'insteadof', 'interface', 'match', 'named_argument', 'new', 'open_tag_with_echo', 'php_doc', 'php_open', 'print', 'private', 'protected', 'public', 'require', 'require_once', 'return', 'static', 'throw', 'trait', 'try', 'use', 'use_lambda', 'use_trait', 'var', 'while', 'yield', 'yield_from']``

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

Rule sets
---------

The rule is part of the following rule sets:

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``single_space_after_construct`` rule with the default config.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``single_space_after_construct`` rule with the default config.
