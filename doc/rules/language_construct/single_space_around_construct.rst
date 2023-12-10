======================================
Rule ``single_space_around_construct``
======================================

Ensures a single space after language constructs.

Configuration
-------------

``constructs_contain_a_single_space``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

List of constructs which must contain a single space.

Allowed values: a subset of ``['yield_from']``

Default value: ``['yield_from']``

``constructs_followed_by_a_single_space``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

List of constructs which must be followed by a single space.

Allowed values: a subset of ``['abstract', 'as', 'attribute', 'break', 'case', 'catch', 'class', 'clone', 'comment', 'const', 'const_import', 'continue', 'do', 'echo', 'else', 'elseif', 'enum', 'extends', 'final', 'finally', 'for', 'foreach', 'function', 'function_import', 'global', 'goto', 'if', 'implements', 'include', 'include_once', 'instanceof', 'insteadof', 'interface', 'match', 'named_argument', 'namespace', 'new', 'open_tag_with_echo', 'php_doc', 'php_open', 'print', 'private', 'protected', 'public', 'readonly', 'require', 'require_once', 'return', 'static', 'switch', 'throw', 'trait', 'try', 'type_colon', 'use', 'use_lambda', 'use_trait', 'var', 'while', 'yield', 'yield_from']``

Default value: ``['abstract', 'as', 'attribute', 'break', 'case', 'catch', 'class', 'clone', 'comment', 'const', 'const_import', 'continue', 'do', 'echo', 'else', 'elseif', 'enum', 'extends', 'final', 'finally', 'for', 'foreach', 'function', 'function_import', 'global', 'goto', 'if', 'implements', 'include', 'include_once', 'instanceof', 'insteadof', 'interface', 'match', 'named_argument', 'namespace', 'new', 'open_tag_with_echo', 'php_doc', 'php_open', 'print', 'private', 'protected', 'public', 'readonly', 'require', 'require_once', 'return', 'static', 'switch', 'throw', 'trait', 'try', 'type_colon', 'use', 'use_lambda', 'use_trait', 'var', 'while', 'yield', 'yield_from']``

``constructs_preceded_by_a_single_space``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

List of constructs which must be preceded by a single space.

Allowed values: a subset of ``['as', 'use_lambda']``

Default value: ``['as', 'use_lambda']``

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

With configuration: ``['constructs_contain_a_single_space' => ['yield_from'], 'constructs_followed_by_a_single_space' => ['yield_from']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php

   -function foo() { yield  from  baz(); }
   +function foo() { yield from baz(); }

Example #3
~~~~~~~~~~

With configuration: ``['constructs_preceded_by_a_single_space' => ['use_lambda'], 'constructs_followed_by_a_single_space' => ['use_lambda']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php

   -$foo = function& ()use($bar) {
   +$foo = function& () use ($bar) {
    };

Example #4
~~~~~~~~~~

With configuration: ``['constructs_followed_by_a_single_space' => ['echo']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php

   -echo  "Hello!";
   +echo "Hello!";

Example #5
~~~~~~~~~~

With configuration: ``['constructs_followed_by_a_single_space' => ['yield_from']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php

   -yield  from  baz();
   +yield from baz();

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

Source class
------------

`PhpCsFixer\\Fixer\\LanguageConstruct\\SingleSpaceAroundConstructFixer <./../../../src/Fixer/LanguageConstruct/SingleSpaceAroundConstructFixer.php>`_
