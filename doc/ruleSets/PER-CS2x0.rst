=======================
Rule set ``@PER-CS2x0``
=======================

Rules that follow `PER Coding Style 2.0 <https://www.php-fig.org/per/coding-style/>`_.

Rules
-----

- `@PER-CS1x0 <./PER-CS1x0.rst>`_
- `array_indentation <./../rules/whitespace/array_indentation.rst>`_
- `array_syntax <./../rules/array_notation/array_syntax.rst>`_
- `cast_spaces <./../rules/cast_notation/cast_spaces.rst>`_
- `concat_space <./../rules/operator/concat_space.rst>`_ with config:

  ``['spacing' => 'one']``

- `function_declaration <./../rules/function_notation/function_declaration.rst>`_ with config:

  ``['closure_fn_spacing' => 'none']``

- `method_argument_space <./../rules/function_notation/method_argument_space.rst>`_
- `new_with_parentheses <./../rules/operator/new_with_parentheses.rst>`_ with config:

  ``['anonymous_class' => false]``

- `single_line_empty_body <./../rules/basic/single_line_empty_body.rst>`_
- `single_space_around_construct <./../rules/language_construct/single_space_around_construct.rst>`_ with config:

  ``['constructs_followed_by_a_single_space' => ['abstract', 'as', 'case', 'catch', 'class', 'const', 'const_import', 'do', 'else', 'elseif', 'enum', 'final', 'finally', 'for', 'foreach', 'function', 'function_import', 'if', 'insteadof', 'interface', 'match', 'named_argument', 'namespace', 'new', 'private', 'protected', 'public', 'readonly', 'static', 'switch', 'trait', 'try', 'type_colon', 'use', 'use_lambda', 'while'], 'constructs_preceded_by_a_single_space' => ['as', 'else', 'elseif', 'use_lambda']]``

- `trailing_comma_in_multiline <./../rules/control_structure/trailing_comma_in_multiline.rst>`_ with config:

  ``['after_heredoc' => true, 'elements' => ['arguments', 'array_destructuring', 'arrays', 'match', 'parameters']]``

