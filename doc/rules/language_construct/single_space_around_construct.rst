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

Allowed values: a subset of ``['abstract', 'as', 'attribute', 'break', 'case', 'catch', 'class', 'clone', 'comment', 'const', 'const_import', 'continue', 'do', 'echo', 'else', 'elseif', 'enum', 'extends', 'final', 'finally', 'for', 'foreach', 'function', 'function_import', 'global', 'goto', 'if', 'implements', 'include', 'include_once', 'instanceof', 'insteadof', 'interface', 'match', 'named_argument', 'namespace', 'new', 'open_tag_with_echo', 'php_doc', 'php_open', 'print', 'private', 'private_set', 'protected', 'protected_set', 'public', 'public_set', 'readonly', 'require', 'require_once', 'return', 'static', 'switch', 'throw', 'trait', 'try', 'type_colon', 'use', 'use_lambda', 'use_trait', 'var', 'while', 'yield', 'yield_from']``

Default value: ``['abstract', 'as', 'attribute', 'break', 'case', 'catch', 'class', 'clone', 'comment', 'const', 'const_import', 'continue', 'do', 'echo', 'else', 'elseif', 'enum', 'extends', 'final', 'finally', 'for', 'foreach', 'function', 'function_import', 'global', 'goto', 'if', 'implements', 'include', 'include_once', 'instanceof', 'insteadof', 'interface', 'match', 'named_argument', 'namespace', 'new', 'open_tag_with_echo', 'php_doc', 'php_open', 'print', 'private', 'private_set', 'protected', 'protected_set', 'public', 'public_set', 'readonly', 'require', 'require_once', 'return', 'static', 'switch', 'throw', 'trait', 'try', 'type_colon', 'use', 'use_lambda', 'use_trait', 'var', 'while', 'yield', 'yield_from']``

``constructs_preceded_by_a_single_space``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

List of constructs which must be preceded by a single space.

Allowed values: a subset of ``['as', 'else', 'elseif', 'use_lambda']``

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

- `@PER <./../../ruleSets/PER.rst>`_ with config:

  ``['constructs_followed_by_a_single_space' => ['abstract', 'as', 'case', 'catch', 'class', 'const', 'const_import', 'do', 'else', 'elseif', 'enum', 'final', 'finally', 'for', 'foreach', 'function', 'function_import', 'if', 'insteadof', 'interface', 'match', 'named_argument', 'namespace', 'new', 'private', 'protected', 'public', 'readonly', 'static', 'switch', 'trait', 'try', 'type_colon', 'use', 'use_lambda', 'while'], 'constructs_preceded_by_a_single_space' => ['as', 'else', 'elseif', 'use_lambda']]``

- `@PER-CS <./../../ruleSets/PER-CS.rst>`_ with config:

  ``['constructs_followed_by_a_single_space' => ['abstract', 'as', 'case', 'catch', 'class', 'const', 'const_import', 'do', 'else', 'elseif', 'enum', 'final', 'finally', 'for', 'foreach', 'function', 'function_import', 'if', 'insteadof', 'interface', 'match', 'named_argument', 'namespace', 'new', 'private', 'protected', 'public', 'readonly', 'static', 'switch', 'trait', 'try', 'type_colon', 'use', 'use_lambda', 'while'], 'constructs_preceded_by_a_single_space' => ['as', 'else', 'elseif', 'use_lambda']]``

- `@PER-CS1.0 <./../../ruleSets/PER-CS1.0.rst>`_ with config:

  ``['constructs_followed_by_a_single_space' => ['abstract', 'as', 'case', 'catch', 'class', 'const_import', 'do', 'else', 'elseif', 'final', 'finally', 'for', 'foreach', 'function', 'function_import', 'if', 'insteadof', 'interface', 'namespace', 'new', 'private', 'protected', 'public', 'static', 'switch', 'trait', 'try', 'use', 'use_lambda', 'while'], 'constructs_preceded_by_a_single_space' => ['as', 'else', 'elseif', 'use_lambda']]``

- `@PER-CS1x0 <./../../ruleSets/PER-CS1x0.rst>`_ with config:

  ``['constructs_followed_by_a_single_space' => ['abstract', 'as', 'case', 'catch', 'class', 'const_import', 'do', 'else', 'elseif', 'final', 'finally', 'for', 'foreach', 'function', 'function_import', 'if', 'insteadof', 'interface', 'namespace', 'new', 'private', 'protected', 'public', 'static', 'switch', 'trait', 'try', 'use', 'use_lambda', 'while'], 'constructs_preceded_by_a_single_space' => ['as', 'else', 'elseif', 'use_lambda']]``

- `@PER-CS2.0 <./../../ruleSets/PER-CS2.0.rst>`_ with config:

  ``['constructs_followed_by_a_single_space' => ['abstract', 'as', 'case', 'catch', 'class', 'const', 'const_import', 'do', 'else', 'elseif', 'enum', 'final', 'finally', 'for', 'foreach', 'function', 'function_import', 'if', 'insteadof', 'interface', 'match', 'named_argument', 'namespace', 'new', 'private', 'protected', 'public', 'readonly', 'static', 'switch', 'trait', 'try', 'type_colon', 'use', 'use_lambda', 'while'], 'constructs_preceded_by_a_single_space' => ['as', 'else', 'elseif', 'use_lambda']]``

- `@PER-CS2x0 <./../../ruleSets/PER-CS2x0.rst>`_ with config:

  ``['constructs_followed_by_a_single_space' => ['abstract', 'as', 'case', 'catch', 'class', 'const', 'const_import', 'do', 'else', 'elseif', 'enum', 'final', 'finally', 'for', 'foreach', 'function', 'function_import', 'if', 'insteadof', 'interface', 'match', 'named_argument', 'namespace', 'new', 'private', 'protected', 'public', 'readonly', 'static', 'switch', 'trait', 'try', 'type_colon', 'use', 'use_lambda', 'while'], 'constructs_preceded_by_a_single_space' => ['as', 'else', 'elseif', 'use_lambda']]``

- `@PER-CS3.0 <./../../ruleSets/PER-CS3.0.rst>`_ with config:

  ``['constructs_followed_by_a_single_space' => ['abstract', 'as', 'case', 'catch', 'class', 'const', 'const_import', 'do', 'else', 'elseif', 'enum', 'final', 'finally', 'for', 'foreach', 'function', 'function_import', 'if', 'insteadof', 'interface', 'match', 'named_argument', 'namespace', 'new', 'private', 'protected', 'public', 'readonly', 'static', 'switch', 'trait', 'try', 'type_colon', 'use', 'use_lambda', 'while'], 'constructs_preceded_by_a_single_space' => ['as', 'else', 'elseif', 'use_lambda']]``

- `@PER-CS3x0 <./../../ruleSets/PER-CS3x0.rst>`_ with config:

  ``['constructs_followed_by_a_single_space' => ['abstract', 'as', 'case', 'catch', 'class', 'const', 'const_import', 'do', 'else', 'elseif', 'enum', 'final', 'finally', 'for', 'foreach', 'function', 'function_import', 'if', 'insteadof', 'interface', 'match', 'named_argument', 'namespace', 'new', 'private', 'protected', 'public', 'readonly', 'static', 'switch', 'trait', 'try', 'type_colon', 'use', 'use_lambda', 'while'], 'constructs_preceded_by_a_single_space' => ['as', 'else', 'elseif', 'use_lambda']]``

- `@PSR2 <./../../ruleSets/PSR2.rst>`_ with config:

  ``['constructs_followed_by_a_single_space' => ['abstract', 'as', 'case', 'catch', 'class', 'do', 'else', 'elseif', 'final', 'for', 'foreach', 'function', 'if', 'interface', 'namespace', 'private', 'protected', 'public', 'static', 'switch', 'trait', 'try', 'use_lambda', 'while'], 'constructs_preceded_by_a_single_space' => ['as', 'else', 'elseif', 'use_lambda']]``

- `@PSR12 <./../../ruleSets/PSR12.rst>`_ with config:

  ``['constructs_followed_by_a_single_space' => ['abstract', 'as', 'case', 'catch', 'class', 'const_import', 'do', 'else', 'elseif', 'final', 'finally', 'for', 'foreach', 'function', 'function_import', 'if', 'insteadof', 'interface', 'namespace', 'new', 'private', 'protected', 'public', 'static', 'switch', 'trait', 'try', 'use', 'use_lambda', 'while'], 'constructs_preceded_by_a_single_space' => ['as', 'else', 'elseif', 'use_lambda']]``

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\LanguageConstruct\\SingleSpaceAroundConstructFixer <./../../../src/Fixer/LanguageConstruct/SingleSpaceAroundConstructFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\LanguageConstruct\\SingleSpaceAroundConstructFixerTest <./../../../tests/Fixer/LanguageConstruct/SingleSpaceAroundConstructFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
