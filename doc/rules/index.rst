=======================
List of Available Rules
=======================

Alias
-----

- `array_push <./alias/array_push.rst>`_ *(risky)*

  Converts simple usages of ``array_push($x, $y);`` to ``$x[] = $y;``.
- `backtick_to_shell_exec <./alias/backtick_to_shell_exec.rst>`_

  Converts backtick operators to ``shell_exec`` calls.
- `ereg_to_preg <./alias/ereg_to_preg.rst>`_ *(risky)*

  Replace deprecated ``ereg`` regular expression functions with ``preg``.
- `mb_str_functions <./alias/mb_str_functions.rst>`_ *(risky)*

  Replace non multibyte-safe functions with corresponding mb function.
- `modernize_strpos <./alias/modernize_strpos.rst>`_ *(risky)*

  Replace ``strpos()`` and ``stripos()`` calls with ``str_starts_with()`` or ``str_contains()`` if possible.
- `no_alias_functions <./alias/no_alias_functions.rst>`_ *(risky)*

  Master functions shall be used instead of aliases.
- `no_alias_language_construct_call <./alias/no_alias_language_construct_call.rst>`_

  Master language constructs shall be used instead of aliases.
- `no_mixed_echo_print <./alias/no_mixed_echo_print.rst>`_

  Either language construct ``print`` or ``echo`` should be used.
- `pow_to_exponentiation <./alias/pow_to_exponentiation.rst>`_ *(risky)*

  Converts ``pow`` to the ``**`` operator.
- `random_api_migration <./alias/random_api_migration.rst>`_ *(risky)*

  Replaces ``rand``, ``srand``, ``getrandmax`` functions calls with their ``mt_*`` analogs or ``random_int``.
- `set_type_to_cast <./alias/set_type_to_cast.rst>`_ *(risky)*

  Cast shall be used, not ``settype``.

Array Notation
--------------

- `array_syntax <./array_notation/array_syntax.rst>`_

  PHP arrays should be declared using the configured syntax.
- `no_multiline_whitespace_around_double_arrow <./array_notation/no_multiline_whitespace_around_double_arrow.rst>`_

  Operator ``=>`` should not be surrounded by multi-line whitespaces.
- `no_trailing_comma_in_singleline_array <./array_notation/no_trailing_comma_in_singleline_array.rst>`_ *(deprecated)*

  PHP single-line arrays should not have trailing comma.
- `no_whitespace_before_comma_in_array <./array_notation/no_whitespace_before_comma_in_array.rst>`_

  In array declaration, there MUST NOT be a whitespace before each comma.
- `normalize_index_brace <./array_notation/normalize_index_brace.rst>`_

  Array index should always be written by using square braces.
- `return_to_yield_from <./array_notation/return_to_yield_from.rst>`_

  If the function explicitly returns an array, and has the return type ``iterable``, then ``yield from`` must be used instead of ``return``.
- `trim_array_spaces <./array_notation/trim_array_spaces.rst>`_

  Arrays should be formatted like function/method arguments, without leading or trailing single line space.
- `whitespace_after_comma_in_array <./array_notation/whitespace_after_comma_in_array.rst>`_

  In array declaration, there MUST be a whitespace after each comma.
- `yield_from_array_to_yields <./array_notation/yield_from_array_to_yields.rst>`_ *(risky)*

  Yield from array must be unpacked to series of yields.

Attribute Notation
------------------

- `attribute_empty_parentheses <./attribute_notation/attribute_empty_parentheses.rst>`_

  PHP attributes declared without arguments must (not) be followed by empty parentheses.
- `general_attribute_remove <./attribute_notation/general_attribute_remove.rst>`_

  Removes configured attributes by their respective FQN.
- `ordered_attributes <./attribute_notation/ordered_attributes.rst>`_

  Sorts attributes using the configured sort algorithm.

Basic
-----

- `braces <./basic/braces.rst>`_ *(deprecated)*

  The body of each structure MUST be enclosed by braces. Braces should be properly placed. Body of braces should be properly indented.
- `braces_position <./basic/braces_position.rst>`_

  Braces must be placed as configured.
- `curly_braces_position <./basic/curly_braces_position.rst>`_ *(deprecated)*

  Curly braces must be placed as configured.
- `encoding <./basic/encoding.rst>`_

  PHP code MUST use only UTF-8 without BOM (remove BOM).
- `no_multiple_statements_per_line <./basic/no_multiple_statements_per_line.rst>`_

  There must not be more than one statement per line.
- `no_trailing_comma_in_singleline <./basic/no_trailing_comma_in_singleline.rst>`_

  If a list of values separated by a comma is contained on a single line, then the last item MUST NOT have a trailing comma.
- `non_printable_character <./basic/non_printable_character.rst>`_ *(risky)*

  Remove Zero-width space (ZWSP), Non-breaking space (NBSP) and other invisible unicode symbols.
- `numeric_literal_separator <./basic/numeric_literal_separator.rst>`_

  Adds separators to numeric literals of any kind.
- `octal_notation <./basic/octal_notation.rst>`_

  Literal octal must be in ``0o`` notation.
- `psr_autoloading <./basic/psr_autoloading.rst>`_ *(risky)*

  Classes must be in a path that matches their namespace, be at least one namespace deep and the class name should match the file name.
- `single_line_empty_body <./basic/single_line_empty_body.rst>`_

  Empty body of class, interface, trait, enum or function must be abbreviated as ``{}`` and placed on the same line as the previous symbol, separated by a single space.

Casing
------

- `class_reference_name_casing <./casing/class_reference_name_casing.rst>`_

  When referencing an internal class it must be written using the correct casing.
- `constant_case <./casing/constant_case.rst>`_

  The PHP constants ``true``, ``false``, and ``null`` MUST be written using the correct casing.
- `integer_literal_case <./casing/integer_literal_case.rst>`_

  Integer literals must be in correct case.
- `lowercase_keywords <./casing/lowercase_keywords.rst>`_

  PHP keywords MUST be in lower case.
- `lowercase_static_reference <./casing/lowercase_static_reference.rst>`_

  Class static references ``self``, ``static`` and ``parent`` MUST be in lower case.
- `magic_constant_casing <./casing/magic_constant_casing.rst>`_

  Magic constants should be referred to using the correct casing.
- `magic_method_casing <./casing/magic_method_casing.rst>`_

  Magic method definitions and calls must be using the correct casing.
- `native_function_casing <./casing/native_function_casing.rst>`_

  Function defined by PHP should be called using the correct casing.
- `native_function_type_declaration_casing <./casing/native_function_type_declaration_casing.rst>`_ *(deprecated)*

  Native type declarations for functions should use the correct case.
- `native_type_declaration_casing <./casing/native_type_declaration_casing.rst>`_

  Native type declarations should be used in the correct case.

Cast Notation
-------------

- `cast_spaces <./cast_notation/cast_spaces.rst>`_

  A single space or none should be between cast and variable.
- `lowercase_cast <./cast_notation/lowercase_cast.rst>`_

  Cast should be written in lower case.
- `modernize_types_casting <./cast_notation/modernize_types_casting.rst>`_ *(risky)*

  Replaces ``intval``, ``floatval``, ``doubleval``, ``strval`` and ``boolval`` function calls with according type casting operator.
- `no_short_bool_cast <./cast_notation/no_short_bool_cast.rst>`_

  Short cast ``bool`` using double exclamation mark should not be used.
- `no_unset_cast <./cast_notation/no_unset_cast.rst>`_

  Variables must be set ``null`` instead of using ``(unset)`` casting.
- `short_scalar_cast <./cast_notation/short_scalar_cast.rst>`_

  Cast ``(boolean)`` and ``(integer)`` should be written as ``(bool)`` and ``(int)``, ``(double)`` and ``(real)`` as ``(float)``, ``(binary)`` as ``(string)``.

Class Notation
--------------

- `class_attributes_separation <./class_notation/class_attributes_separation.rst>`_

  Class, trait and interface elements must be separated with one or none blank line.
- `class_definition <./class_notation/class_definition.rst>`_

  Whitespace around the keywords of a class, trait, enum or interfaces definition should be one space.
- `final_class <./class_notation/final_class.rst>`_ *(risky)*

  All classes must be final, except abstract ones and Doctrine entities.
- `final_internal_class <./class_notation/final_internal_class.rst>`_ *(risky)*

  Internal classes should be ``final``.
- `final_public_method_for_abstract_class <./class_notation/final_public_method_for_abstract_class.rst>`_ *(risky)*

  All ``public`` methods of ``abstract`` classes should be ``final``.
- `modifier_keywords <./class_notation/modifier_keywords.rst>`_

  Classes, constants, properties, and methods MUST have visibility declared, and keyword modifiers MUST be in the following order: inheritance modifier (``abstract`` or ``final``), visibility modifier (``public``, ``protected``, or ``private``), set-visibility modifier (``public(set)``, ``protected(set)``, or ``private(set)``), scope modifier (``static``), mutation modifier (``readonly``), type declaration, name.
- `no_blank_lines_after_class_opening <./class_notation/no_blank_lines_after_class_opening.rst>`_

  There should be no empty lines after class opening brace.
- `no_null_property_initialization <./class_notation/no_null_property_initialization.rst>`_

  Properties MUST not be explicitly initialized with ``null`` except when they have a type declaration (PHP 7.4).
- `no_php4_constructor <./class_notation/no_php4_constructor.rst>`_ *(risky)*

  Convert PHP4-style constructors to ``__construct``.
- `no_unneeded_final_method <./class_notation/no_unneeded_final_method.rst>`_ *(risky)*

  Removes ``final`` from methods where possible.
- `ordered_class_elements <./class_notation/ordered_class_elements.rst>`_

  Orders the elements of classes/interfaces/traits/enums.
- `ordered_interfaces <./class_notation/ordered_interfaces.rst>`_

  Orders the interfaces in an ``implements`` or ``interface extends`` clause.
- `ordered_traits <./class_notation/ordered_traits.rst>`_ *(risky)*

  Trait ``use`` statements must be sorted alphabetically.
- `ordered_types <./class_notation/ordered_types.rst>`_

  Sort union types and intersection types using configured order.
- `phpdoc_readonly_class_comment_to_keyword <./class_notation/phpdoc_readonly_class_comment_to_keyword.rst>`_ *(risky)*

  Converts readonly comment on classes to the readonly keyword.
- `protected_to_private <./class_notation/protected_to_private.rst>`_

  Converts ``protected`` variables and methods to ``private`` where possible.
- `self_accessor <./class_notation/self_accessor.rst>`_ *(risky)*

  Inside class or interface element ``self`` should be preferred to the class name itself.
- `self_static_accessor <./class_notation/self_static_accessor.rst>`_

  Inside an enum or ``final``/anonymous class, ``self`` should be preferred over ``static``.
- `single_class_element_per_statement <./class_notation/single_class_element_per_statement.rst>`_

  There MUST NOT be more than one property or constant declared per statement.
- `single_trait_insert_per_statement <./class_notation/single_trait_insert_per_statement.rst>`_

  Each trait ``use`` must be done as single statement.
- `static_private_method <./class_notation/static_private_method.rst>`_ *(risky)*

  Converts private methods to ``static`` where possible.
- `visibility_required <./class_notation/visibility_required.rst>`_ *(deprecated)*

  Classes, constants, properties, and methods MUST have visibility declared, and keyword modifiers MUST be in the following order: inheritance modifier (``abstract`` or ``final``), visibility modifier (``public``, ``protected``, or ``private``), set-visibility modifier (``public(set)``, ``protected(set)``, or ``private(set)``), scope modifier (``static``), mutation modifier (``readonly``), type declaration, name.

Class Usage
-----------

- `date_time_immutable <./class_usage/date_time_immutable.rst>`_ *(risky)*

  Class ``DateTimeImmutable`` should be used instead of ``DateTime``.

Comment
-------

- `comment_to_phpdoc <./comment/comment_to_phpdoc.rst>`_ *(risky)*

  Comments with annotation should be docblock when used on structural elements.
- `header_comment <./comment/header_comment.rst>`_

  Add, replace or remove header comment.
- `multiline_comment_opening_closing <./comment/multiline_comment_opening_closing.rst>`_

  DocBlocks must start with two asterisks, multiline comments must start with a single asterisk, after the opening slash. Both must end with a single asterisk before the closing slash.
- `no_empty_comment <./comment/no_empty_comment.rst>`_

  There should not be any empty comments.
- `no_trailing_whitespace_in_comment <./comment/no_trailing_whitespace_in_comment.rst>`_

  There MUST be no trailing spaces inside comment or PHPDoc.
- `single_line_comment_spacing <./comment/single_line_comment_spacing.rst>`_

  Single-line comments must have proper spacing.
- `single_line_comment_style <./comment/single_line_comment_style.rst>`_

  Single-line comments and multi-line comments with only one line of actual content should use the ``//`` syntax.

Constant Notation
-----------------

- `native_constant_invocation <./constant_notation/native_constant_invocation.rst>`_ *(risky)*

  Add leading ``\`` before constant invocation of internal constant to speed up resolving. Constant name match is case-sensitive, except for ``null``, ``false`` and ``true``.

Control Structure
-----------------

- `control_structure_braces <./control_structure/control_structure_braces.rst>`_

  The body of each control structure MUST be enclosed within braces.
- `control_structure_continuation_position <./control_structure/control_structure_continuation_position.rst>`_

  Control structure continuation keyword must be on the configured line.
- `elseif <./control_structure/elseif.rst>`_

  The keyword ``elseif`` should be used instead of ``else if`` so that all control keywords look like single words.
- `empty_loop_body <./control_structure/empty_loop_body.rst>`_

  Empty loop-body must be in configured style.
- `empty_loop_condition <./control_structure/empty_loop_condition.rst>`_

  Empty loop-condition must be in configured style.
- `include <./control_structure/include.rst>`_

  Include/Require and file path should be divided with a single space. File path should not be placed within parentheses.
- `no_alternative_syntax <./control_structure/no_alternative_syntax.rst>`_

  Replace control structure alternative syntax to use braces.
- `no_break_comment <./control_structure/no_break_comment.rst>`_

  There must be a comment when fall-through is intentional in a non-empty case body.
- `no_superfluous_elseif <./control_structure/no_superfluous_elseif.rst>`_

  Replaces superfluous ``elseif`` with ``if``.
- `no_trailing_comma_in_list_call <./control_structure/no_trailing_comma_in_list_call.rst>`_ *(deprecated)*

  Remove trailing commas in list function calls.
- `no_unneeded_braces <./control_structure/no_unneeded_braces.rst>`_

  Removes unneeded braces that are superfluous and aren't part of a control structure's body.
- `no_unneeded_control_parentheses <./control_structure/no_unneeded_control_parentheses.rst>`_

  Removes unneeded parentheses around control statements.
- `no_unneeded_curly_braces <./control_structure/no_unneeded_curly_braces.rst>`_ *(deprecated)*

  Removes unneeded curly braces that are superfluous and aren't part of a control structure's body.
- `no_useless_else <./control_structure/no_useless_else.rst>`_

  There should not be useless ``else`` cases.
- `simplified_if_return <./control_structure/simplified_if_return.rst>`_

  Simplify ``if`` control structures that return the boolean result of their condition.
- `switch_case_semicolon_to_colon <./control_structure/switch_case_semicolon_to_colon.rst>`_

  A case should be followed by a colon and not a semicolon.
- `switch_case_space <./control_structure/switch_case_space.rst>`_

  Removes extra spaces between colon and case value.
- `switch_continue_to_break <./control_structure/switch_continue_to_break.rst>`_

  Switch case must not be ended with ``continue`` but with ``break``.
- `trailing_comma_in_multiline <./control_structure/trailing_comma_in_multiline.rst>`_

  Arguments lists, array destructuring lists, arrays that are multi-line, ``match``-lines and parameters lists must have a trailing comma.
- `yoda_style <./control_structure/yoda_style.rst>`_

  Write conditions in Yoda style (``true``), non-Yoda style (``['equal' => false, 'identical' => false, 'less_and_greater' => false]``) or ignore those conditions (``null``) based on configuration.

Doctrine Annotation
-------------------

- `doctrine_annotation_array_assignment <./doctrine_annotation/doctrine_annotation_array_assignment.rst>`_

  Doctrine annotations must use configured operator for assignment in arrays.
- `doctrine_annotation_braces <./doctrine_annotation/doctrine_annotation_braces.rst>`_

  Doctrine annotations without arguments must use the configured syntax.
- `doctrine_annotation_indentation <./doctrine_annotation/doctrine_annotation_indentation.rst>`_

  Doctrine annotations must be indented with four spaces.
- `doctrine_annotation_spaces <./doctrine_annotation/doctrine_annotation_spaces.rst>`_

  Fixes spaces in Doctrine annotations.

Function Notation
-----------------

- `combine_nested_dirname <./function_notation/combine_nested_dirname.rst>`_ *(risky)*

  Replace multiple nested calls of ``dirname`` by only one call with second ``$level`` parameter.
- `date_time_create_from_format_call <./function_notation/date_time_create_from_format_call.rst>`_ *(risky)*

  The first argument of ``DateTime::createFromFormat`` method must start with ``!``.
- `fopen_flag_order <./function_notation/fopen_flag_order.rst>`_ *(risky)*

  Order the flags in ``fopen`` calls, ``b`` and ``t`` must be last.
- `fopen_flags <./function_notation/fopen_flags.rst>`_ *(risky)*

  The flags in ``fopen`` calls must omit ``t``, and ``b`` must be omitted or included consistently.
- `function_declaration <./function_notation/function_declaration.rst>`_

  Spaces should be properly placed in a function declaration.
- `function_typehint_space <./function_notation/function_typehint_space.rst>`_ *(deprecated)*

  Ensure single space between function's argument and its typehint.
- `implode_call <./function_notation/implode_call.rst>`_ *(risky)*

  Function ``implode`` must be called with 2 arguments in the documented order.
- `lambda_not_used_import <./function_notation/lambda_not_used_import.rst>`_

  Lambda must not import variables it doesn't use.
- `method_argument_space <./function_notation/method_argument_space.rst>`_

  In method arguments and method call, there MUST NOT be a space before each comma and there MUST be one space after each comma. Argument lists MAY be split across multiple lines, where each subsequent line is indented once. When doing so, the first item in the list MUST be on the next line, and there MUST be only one argument per line.
- `multiline_promoted_properties <./function_notation/multiline_promoted_properties.rst>`_ *(experimental)*

  Promoted properties must be on separate lines.
- `native_function_invocation <./function_notation/native_function_invocation.rst>`_ *(risky)*

  Add leading ``\`` before function invocation to speed up resolving.
- `no_spaces_after_function_name <./function_notation/no_spaces_after_function_name.rst>`_

  When making a method or function call, there MUST NOT be a space between the method or function name and the opening parenthesis.
- `no_trailing_comma_in_singleline_function_call <./function_notation/no_trailing_comma_in_singleline_function_call.rst>`_ *(deprecated)*

  When making a method or function call on a single line there MUST NOT be a trailing comma after the last argument.
- `no_unreachable_default_argument_value <./function_notation/no_unreachable_default_argument_value.rst>`_ *(risky)*

  In function arguments there must not be arguments with default values before non-default ones.
- `no_useless_printf <./function_notation/no_useless_printf.rst>`_ *(risky)*

  There must be no ``printf`` calls with only the first argument.
- `no_useless_sprintf <./function_notation/no_useless_sprintf.rst>`_ *(risky)*

  There must be no ``sprintf`` calls with only the first argument.
- `nullable_type_declaration_for_default_null_value <./function_notation/nullable_type_declaration_for_default_null_value.rst>`_

  Adds or removes ``?`` before single type declarations or ``|null`` at the end of union types when parameters have a default ``null`` value.
- `phpdoc_to_param_type <./function_notation/phpdoc_to_param_type.rst>`_ *(experimental, risky)*

  Takes ``@param`` annotations of non-mixed types and adjusts accordingly the function signature.
- `phpdoc_to_property_type <./function_notation/phpdoc_to_property_type.rst>`_ *(experimental, risky)*

  Takes ``@var`` annotation of non-mixed types and adjusts accordingly the property signature..
- `phpdoc_to_return_type <./function_notation/phpdoc_to_return_type.rst>`_ *(experimental, risky)*

  Takes ``@return`` annotation of non-mixed types and adjusts accordingly the function signature.
- `regular_callable_call <./function_notation/regular_callable_call.rst>`_ *(risky)*

  Callables must be called without using ``call_user_func*`` when possible.
- `return_type_declaration <./function_notation/return_type_declaration.rst>`_

  Adjust spacing around colon in return type declarations and backed enum types.
- `single_line_throw <./function_notation/single_line_throw.rst>`_

  Throwing exception must be done in single line.
- `static_lambda <./function_notation/static_lambda.rst>`_ *(risky)*

  Lambdas not (indirectly) referencing ``$this`` must be declared ``static``.
- `use_arrow_functions <./function_notation/use_arrow_functions.rst>`_ *(risky)*

  Anonymous functions with return as the only statement must use arrow functions.
- `void_return <./function_notation/void_return.rst>`_ *(risky)*

  Add ``void`` return type to functions with missing or empty return statements, but priority is given to ``@return`` annotations.

Import
------

- `fully_qualified_strict_types <./import/fully_qualified_strict_types.rst>`_

  Removes the leading part of fully qualified symbol references if a given symbol is imported or belongs to the current namespace.
- `global_namespace_import <./import/global_namespace_import.rst>`_

  Imports or fully qualifies global classes/functions/constants.
- `group_import <./import/group_import.rst>`_

  There MUST be group use for the same namespaces.
- `no_leading_import_slash <./import/no_leading_import_slash.rst>`_

  Remove leading slashes in ``use`` clauses.
- `no_unneeded_import_alias <./import/no_unneeded_import_alias.rst>`_

  Imports should not be aliased as the same name.
- `no_unused_imports <./import/no_unused_imports.rst>`_

  Unused ``use`` statements must be removed.
- `ordered_imports <./import/ordered_imports.rst>`_

  Ordering ``use`` statements.
- `single_import_per_statement <./import/single_import_per_statement.rst>`_

  There MUST be one use keyword per declaration.
- `single_line_after_imports <./import/single_line_after_imports.rst>`_

  Each namespace use MUST go on its own line and there MUST be one blank line after the use statements block.

Language Construct
------------------

- `class_keyword <./language_construct/class_keyword.rst>`_ *(experimental, risky)*

  Converts FQCN strings to ``*::class`` keywords.
- `class_keyword_remove <./language_construct/class_keyword_remove.rst>`_ *(deprecated)*

  Converts ``::class`` keywords to FQCN strings.
- `combine_consecutive_issets <./language_construct/combine_consecutive_issets.rst>`_

  Using ``isset($var) &&`` multiple times should be done in one call.
- `combine_consecutive_unsets <./language_construct/combine_consecutive_unsets.rst>`_

  Calling ``unset`` on multiple items should be done in one call.
- `declare_equal_normalize <./language_construct/declare_equal_normalize.rst>`_

  Equal sign in declare statement should be surrounded by spaces or not following configuration.
- `declare_parentheses <./language_construct/declare_parentheses.rst>`_

  There must not be spaces around ``declare`` statement parentheses.
- `dir_constant <./language_construct/dir_constant.rst>`_ *(risky)*

  Replaces ``dirname(__FILE__)`` expression with equivalent ``__DIR__`` constant.
- `error_suppression <./language_construct/error_suppression.rst>`_ *(risky)*

  Error control operator should be added to deprecation notices and/or removed from other cases.
- `explicit_indirect_variable <./language_construct/explicit_indirect_variable.rst>`_

  Add curly braces to indirect variables to make them clear to understand.
- `function_to_constant <./language_construct/function_to_constant.rst>`_ *(risky)*

  Replace core functions calls returning constants with the constants.
- `get_class_to_class_keyword <./language_construct/get_class_to_class_keyword.rst>`_ *(risky)*

  Replace ``get_class`` calls on object variables with class keyword syntax.
- `is_null <./language_construct/is_null.rst>`_ *(risky)*

  Replaces ``is_null($var)`` expression with ``null === $var``.
- `no_unset_on_property <./language_construct/no_unset_on_property.rst>`_ *(risky)*

  Properties should be set to ``null`` instead of using ``unset``.
- `nullable_type_declaration <./language_construct/nullable_type_declaration.rst>`_

  Nullable single type declaration should be standardised using configured syntax.
- `single_space_after_construct <./language_construct/single_space_after_construct.rst>`_ *(deprecated)*

  Ensures a single space after language constructs.
- `single_space_around_construct <./language_construct/single_space_around_construct.rst>`_

  Ensures a single space after language constructs.

List Notation
-------------

- `list_syntax <./list_notation/list_syntax.rst>`_

  List (``array`` destructuring) assignment should be declared using the configured syntax.

Namespace Notation
------------------

- `blank_line_after_namespace <./namespace_notation/blank_line_after_namespace.rst>`_

  There MUST be one blank line after the namespace declaration.
- `blank_lines_before_namespace <./namespace_notation/blank_lines_before_namespace.rst>`_

  Controls blank lines before a namespace declaration.
- `clean_namespace <./namespace_notation/clean_namespace.rst>`_

  Namespace must not contain spacing, comments or PHPDoc.
- `no_blank_lines_before_namespace <./namespace_notation/no_blank_lines_before_namespace.rst>`_ *(deprecated)*

  There should be no blank lines before a namespace declaration.
- `no_leading_namespace_whitespace <./namespace_notation/no_leading_namespace_whitespace.rst>`_

  The namespace declaration line shouldn't contain leading whitespace.
- `single_blank_line_before_namespace <./namespace_notation/single_blank_line_before_namespace.rst>`_ *(deprecated)*

  There should be exactly one blank line before a namespace declaration.

Naming
------

- `no_homoglyph_names <./naming/no_homoglyph_names.rst>`_ *(risky)*

  Replace accidental usage of homoglyphs (non ascii characters) in names.

Operator
--------

- `assign_null_coalescing_to_coalesce_equal <./operator/assign_null_coalescing_to_coalesce_equal.rst>`_

  Use the null coalescing assignment operator ``??=`` where possible.
- `binary_operator_spaces <./operator/binary_operator_spaces.rst>`_

  Binary operators should be surrounded by space as configured.
- `concat_space <./operator/concat_space.rst>`_

  Concatenation should be spaced according to configuration.
- `increment_style <./operator/increment_style.rst>`_

  Pre- or post-increment and decrement operators should be used if possible.
- `logical_operators <./operator/logical_operators.rst>`_ *(risky)*

  Use ``&&`` and ``||`` logical operators instead of ``and`` and ``or``.
- `long_to_shorthand_operator <./operator/long_to_shorthand_operator.rst>`_ *(risky)*

  Shorthand notation for operators should be used if possible.
- `new_expression_parentheses <./operator/new_expression_parentheses.rst>`_

  All ``new`` expressions with a further call must (not) be wrapped in parentheses.
- `new_with_braces <./operator/new_with_braces.rst>`_ *(deprecated)*

  All instances created with ``new`` keyword must (not) be followed by braces.
- `new_with_parentheses <./operator/new_with_parentheses.rst>`_

  All instances created with ``new`` keyword must (not) be followed by parentheses.
- `no_space_around_double_colon <./operator/no_space_around_double_colon.rst>`_

  There must be no space around double colons (also called Scope Resolution Operator or Paamayim Nekudotayim).
- `no_useless_concat_operator <./operator/no_useless_concat_operator.rst>`_

  There should not be useless concat operations.
- `no_useless_nullsafe_operator <./operator/no_useless_nullsafe_operator.rst>`_

  There should not be useless Null-safe operator ``?->`` used.
- `not_operator_with_space <./operator/not_operator_with_space.rst>`_

  Logical NOT operators (``!``) should have leading and trailing whitespaces.
- `not_operator_with_successor_space <./operator/not_operator_with_successor_space.rst>`_

  Logical NOT operators (``!``) should have one trailing whitespace.
- `object_operator_without_whitespace <./operator/object_operator_without_whitespace.rst>`_

  There should not be space before or after object operators ``->`` and ``?->``.
- `operator_linebreak <./operator/operator_linebreak.rst>`_

  Operators - when multiline - must always be at the beginning or at the end of the line.
- `standardize_increment <./operator/standardize_increment.rst>`_

  Increment and decrement operators should be used if possible.
- `standardize_not_equals <./operator/standardize_not_equals.rst>`_

  Replace all ``<>`` with ``!=``.
- `ternary_operator_spaces <./operator/ternary_operator_spaces.rst>`_

  Standardize spaces around ternary operator.
- `ternary_to_elvis_operator <./operator/ternary_to_elvis_operator.rst>`_ *(risky)*

  Use the Elvis operator ``?:`` where possible.
- `ternary_to_null_coalescing <./operator/ternary_to_null_coalescing.rst>`_

  Use ``null`` coalescing operator ``??`` where possible.
- `unary_operator_spaces <./operator/unary_operator_spaces.rst>`_

  Unary operators should be placed adjacent to their operands.

PHP Tag
-------

- `blank_line_after_opening_tag <./php_tag/blank_line_after_opening_tag.rst>`_

  Ensure there is no code on the same line as the PHP open tag and it is followed by a blank line.
- `echo_tag_syntax <./php_tag/echo_tag_syntax.rst>`_

  Replaces short-echo ``<?=`` with long format ``<?php echo``/``<?php print`` syntax, or vice-versa.
- `full_opening_tag <./php_tag/full_opening_tag.rst>`_

  PHP code must use the long ``<?php`` tags or short-echo ``<?=`` tags and not other tag variations.
- `linebreak_after_opening_tag <./php_tag/linebreak_after_opening_tag.rst>`_

  Ensure there is no code on the same line as the PHP open tag.
- `no_closing_tag <./php_tag/no_closing_tag.rst>`_

  The closing ``?>`` tag MUST be omitted from files containing only PHP.

PHPUnit
-------

- `php_unit_assert_new_names <./php_unit/php_unit_assert_new_names.rst>`_ *(risky)*

  Rename deprecated PHPUnit assertions like ``assertFileNotExists`` to new methods like ``assertFileDoesNotExist``.
- `php_unit_attributes <./php_unit/php_unit_attributes.rst>`_

  PHPUnit attributes must be used over their respective PHPDoc-based annotations.
- `php_unit_construct <./php_unit/php_unit_construct.rst>`_ *(risky)*

  PHPUnit assertion method calls like ``->assertSame(true, $foo)`` should be written with dedicated method like ``->assertTrue($foo)``.
- `php_unit_data_provider_method_order <./php_unit/php_unit_data_provider_method_order.rst>`_

  Data provider method must be placed after/before the last/first test where used.
- `php_unit_data_provider_name <./php_unit/php_unit_data_provider_name.rst>`_ *(risky)*

  Data provider names must match the name of the test.
- `php_unit_data_provider_return_type <./php_unit/php_unit_data_provider_return_type.rst>`_ *(risky)*

  The return type of PHPUnit data provider must be ``iterable``.
- `php_unit_data_provider_static <./php_unit/php_unit_data_provider_static.rst>`_ *(risky)*

  Data providers must be static.
- `php_unit_dedicate_assert <./php_unit/php_unit_dedicate_assert.rst>`_ *(risky)*

  PHPUnit assertions like ``assertInternalType``, ``assertFileExists``, should be used over ``assertTrue``.
- `php_unit_dedicate_assert_internal_type <./php_unit/php_unit_dedicate_assert_internal_type.rst>`_ *(risky)*

  PHPUnit assertions like ``assertIsArray`` should be used over ``assertInternalType``.
- `php_unit_expectation <./php_unit/php_unit_expectation.rst>`_ *(risky)*

  Usages of ``->setExpectedException*`` methods MUST be replaced by ``->expectException*`` methods.
- `php_unit_fqcn_annotation <./php_unit/php_unit_fqcn_annotation.rst>`_

  PHPUnit annotations should be a FQCNs including a root namespace.
- `php_unit_internal_class <./php_unit/php_unit_internal_class.rst>`_

  All PHPUnit test classes should be marked as internal.
- `php_unit_method_casing <./php_unit/php_unit_method_casing.rst>`_

  Enforce camel (or snake) case for PHPUnit test methods, following configuration.
- `php_unit_mock <./php_unit/php_unit_mock.rst>`_ *(risky)*

  Usages of ``->getMock`` and ``->getMockWithoutInvokingTheOriginalConstructor`` methods MUST be replaced by ``->createMock`` or ``->createPartialMock`` methods.
- `php_unit_mock_short_will_return <./php_unit/php_unit_mock_short_will_return.rst>`_ *(risky)*

  Usage of PHPUnit's mock e.g. ``->will($this->returnValue(..))`` must be replaced by its shorter equivalent such as ``->willReturn(...)``.
- `php_unit_namespaced <./php_unit/php_unit_namespaced.rst>`_ *(risky)*

  PHPUnit classes MUST be used in namespaced version, e.g. ``\PHPUnit\Framework\TestCase`` instead of ``\PHPUnit_Framework_TestCase``.
- `php_unit_no_expectation_annotation <./php_unit/php_unit_no_expectation_annotation.rst>`_ *(risky)*

  Usages of ``@expectedException*`` annotations MUST be replaced by ``->setExpectedException*`` methods.
- `php_unit_set_up_tear_down_visibility <./php_unit/php_unit_set_up_tear_down_visibility.rst>`_ *(risky)*

  Changes the visibility of the ``setUp()`` and ``tearDown()`` functions of PHPUnit to ``protected``, to match the PHPUnit TestCase.
- `php_unit_size_class <./php_unit/php_unit_size_class.rst>`_

  All PHPUnit test cases should have ``@small``, ``@medium`` or ``@large`` annotation to enable run time limits.
- `php_unit_strict <./php_unit/php_unit_strict.rst>`_ *(risky)*

  PHPUnit methods like ``assertSame`` should be used instead of ``assertEquals``.
- `php_unit_test_annotation <./php_unit/php_unit_test_annotation.rst>`_ *(risky)*

  Adds or removes @test annotations from tests, following configuration.
- `php_unit_test_case_static_method_calls <./php_unit/php_unit_test_case_static_method_calls.rst>`_ *(risky)*

  Calls to ``PHPUnit\Framework\TestCase`` static methods must all be of the same type, either ``$this->``, ``self::`` or ``static::``.
- `php_unit_test_class_requires_covers <./php_unit/php_unit_test_class_requires_covers.rst>`_

  Adds a default ``@coversNothing`` annotation to PHPUnit test classes that have no ``@covers*`` annotation.

PHPDoc
------

- `align_multiline_comment <./phpdoc/align_multiline_comment.rst>`_

  Each line of multi-line DocComments must have an asterisk [PSR-5] and must be aligned with the first one.
- `general_phpdoc_annotation_remove <./phpdoc/general_phpdoc_annotation_remove.rst>`_

  Removes configured annotations from PHPDoc.
- `general_phpdoc_tag_rename <./phpdoc/general_phpdoc_tag_rename.rst>`_

  Renames PHPDoc tags.
- `no_blank_lines_after_phpdoc <./phpdoc/no_blank_lines_after_phpdoc.rst>`_

  There should not be blank lines between docblock and the documented element.
- `no_empty_phpdoc <./phpdoc/no_empty_phpdoc.rst>`_

  There should not be empty PHPDoc blocks.
- `no_superfluous_phpdoc_tags <./phpdoc/no_superfluous_phpdoc_tags.rst>`_

  Removes ``@param``, ``@return`` and ``@var`` tags that don't provide any useful information.
- `phpdoc_add_missing_param_annotation <./phpdoc/phpdoc_add_missing_param_annotation.rst>`_

  PHPDoc should contain ``@param`` for all params.
- `phpdoc_align <./phpdoc/phpdoc_align.rst>`_

  All items of the given PHPDoc tags must be either left-aligned or (by default) aligned vertically.
- `phpdoc_annotation_without_dot <./phpdoc/phpdoc_annotation_without_dot.rst>`_

  PHPDoc annotation descriptions should not be a sentence.
- `phpdoc_array_type <./phpdoc/phpdoc_array_type.rst>`_ *(risky)*

  PHPDoc ``array<T>`` type must be used instead of ``T[]``.
- `phpdoc_indent <./phpdoc/phpdoc_indent.rst>`_

  Docblocks should have the same indentation as the documented subject.
- `phpdoc_inline_tag_normalizer <./phpdoc/phpdoc_inline_tag_normalizer.rst>`_

  Fixes PHPDoc inline tags.
- `phpdoc_line_span <./phpdoc/phpdoc_line_span.rst>`_

  Changes doc blocks from single to multi line, or reversed. Works for class constants, properties and methods only.
- `phpdoc_list_type <./phpdoc/phpdoc_list_type.rst>`_ *(risky)*

  PHPDoc ``list`` type must be used instead of ``array`` without a key.
- `phpdoc_no_access <./phpdoc/phpdoc_no_access.rst>`_

  ``@access`` annotations must be removed from PHPDoc.
- `phpdoc_no_alias_tag <./phpdoc/phpdoc_no_alias_tag.rst>`_

  No alias PHPDoc tags should be used.
- `phpdoc_no_empty_return <./phpdoc/phpdoc_no_empty_return.rst>`_

  ``@return void`` and ``@return null`` annotations must be removed from PHPDoc.
- `phpdoc_no_package <./phpdoc/phpdoc_no_package.rst>`_

  ``@package`` and ``@subpackage`` annotations must be removed from PHPDoc.
- `phpdoc_no_useless_inheritdoc <./phpdoc/phpdoc_no_useless_inheritdoc.rst>`_

  Classy that does not inherit must not have ``@inheritdoc`` tags.
- `phpdoc_order_by_value <./phpdoc/phpdoc_order_by_value.rst>`_

  Order PHPDoc tags by value.
- `phpdoc_order <./phpdoc/phpdoc_order.rst>`_

  Annotations in PHPDoc should be ordered in defined sequence.
- `phpdoc_param_order <./phpdoc/phpdoc_param_order.rst>`_

  Orders all ``@param`` annotations in DocBlocks according to method signature.
- `phpdoc_return_self_reference <./phpdoc/phpdoc_return_self_reference.rst>`_

  The type of ``@return`` annotations of methods returning a reference to itself must the configured one.
- `phpdoc_scalar <./phpdoc/phpdoc_scalar.rst>`_

  Scalar types should always be written in the same form. ``int`` not ``integer``, ``bool`` not ``boolean``, ``float`` not ``real`` or ``double``.
- `phpdoc_separation <./phpdoc/phpdoc_separation.rst>`_

  Annotations in PHPDoc should be grouped together so that annotations of the same type immediately follow each other. Annotations of a different type are separated by a single blank line.
- `phpdoc_single_line_var_spacing <./phpdoc/phpdoc_single_line_var_spacing.rst>`_

  Single line ``@var`` PHPDoc should have proper spacing.
- `phpdoc_summary <./phpdoc/phpdoc_summary.rst>`_

  PHPDoc summary should end in either a full stop, exclamation mark, or question mark.
- `phpdoc_tag_casing <./phpdoc/phpdoc_tag_casing.rst>`_

  Fixes casing of PHPDoc tags.
- `phpdoc_tag_no_named_arguments <./phpdoc/phpdoc_tag_no_named_arguments.rst>`_

  There must be ``@no-named-arguments`` tag in PHPDoc of a class/enum/interface/trait.
- `phpdoc_tag_type <./phpdoc/phpdoc_tag_type.rst>`_

  Forces PHPDoc tags to be either regular annotations or inline.
- `phpdoc_to_comment <./phpdoc/phpdoc_to_comment.rst>`_

  Docblocks should only be used on structural elements.
- `phpdoc_trim_consecutive_blank_line_separation <./phpdoc/phpdoc_trim_consecutive_blank_line_separation.rst>`_

  Removes extra blank lines after summary and after description in PHPDoc.
- `phpdoc_trim <./phpdoc/phpdoc_trim.rst>`_

  PHPDoc should start and end with content, excluding the very first and last line of the docblocks.
- `phpdoc_types <./phpdoc/phpdoc_types.rst>`_

  The correct case must be used for standard PHP types in PHPDoc.
- `phpdoc_types_order <./phpdoc/phpdoc_types_order.rst>`_

  Sorts PHPDoc types.
- `phpdoc_var_annotation_correct_order <./phpdoc/phpdoc_var_annotation_correct_order.rst>`_

  ``@var`` and ``@type`` annotations must have type and name in the correct order.
- `phpdoc_var_without_name <./phpdoc/phpdoc_var_without_name.rst>`_

  ``@var`` and ``@type`` annotations of classy properties should not contain the name.

Return Notation
---------------

- `no_useless_return <./return_notation/no_useless_return.rst>`_

  There should not be an empty ``return`` statement at the end of a function.
- `return_assignment <./return_notation/return_assignment.rst>`_

  Local, dynamic and directly referenced variables should not be assigned and directly returned by a function or method.
- `simplified_null_return <./return_notation/simplified_null_return.rst>`_

  A return statement wishing to return ``void`` should not return ``null``.

Semicolon
---------

- `multiline_whitespace_before_semicolons <./semicolon/multiline_whitespace_before_semicolons.rst>`_

  Forbid multi-line whitespace before the closing semicolon or move the semicolon to the new line for chained calls.
- `no_empty_statement <./semicolon/no_empty_statement.rst>`_

  Remove useless (semicolon) statements.
- `no_singleline_whitespace_before_semicolons <./semicolon/no_singleline_whitespace_before_semicolons.rst>`_

  Single-line whitespace before closing semicolon are prohibited.
- `semicolon_after_instruction <./semicolon/semicolon_after_instruction.rst>`_

  Instructions must be terminated with a semicolon.
- `space_after_semicolon <./semicolon/space_after_semicolon.rst>`_

  Fix whitespace after a semicolon.

Strict
------

- `declare_strict_types <./strict/declare_strict_types.rst>`_ *(risky)*

  Force strict types declaration in all files.
- `strict_comparison <./strict/strict_comparison.rst>`_ *(risky)*

  Comparisons should be strict.
- `strict_param <./strict/strict_param.rst>`_ *(risky)*

  Functions should be used with ``$strict`` param set to ``true``.

String Notation
---------------

- `escape_implicit_backslashes <./string_notation/escape_implicit_backslashes.rst>`_ *(deprecated)*

  Escape implicit backslashes in strings and heredocs to ease the understanding of which are special chars interpreted by PHP and which not.
- `explicit_string_variable <./string_notation/explicit_string_variable.rst>`_

  Converts implicit variables into explicit ones in double-quoted strings or heredoc syntax.
- `heredoc_closing_marker <./string_notation/heredoc_closing_marker.rst>`_

  Unify ``heredoc`` or ``nowdoc`` closing marker.
- `heredoc_to_nowdoc <./string_notation/heredoc_to_nowdoc.rst>`_

  Convert ``heredoc`` to ``nowdoc`` where possible.
- `multiline_string_to_heredoc <./string_notation/multiline_string_to_heredoc.rst>`_

  Convert multiline string to ``heredoc`` or ``nowdoc``.
- `no_binary_string <./string_notation/no_binary_string.rst>`_

  There should not be a binary flag before strings.
- `no_trailing_whitespace_in_string <./string_notation/no_trailing_whitespace_in_string.rst>`_ *(risky)*

  There must be no trailing whitespace in strings.
- `simple_to_complex_string_variable <./string_notation/simple_to_complex_string_variable.rst>`_

  Converts explicit variables in double-quoted strings and heredoc syntax from simple to complex format (``${`` to ``{$``).
- `single_quote <./string_notation/single_quote.rst>`_

  Convert double quotes to single quotes for simple strings.
- `string_implicit_backslashes <./string_notation/string_implicit_backslashes.rst>`_

  Handles implicit backslashes in strings and heredocs. Depending on the chosen strategy, it can escape implicit backslashes to ease the understanding of which are special chars interpreted by PHP and which not (``escape``), or it can remove these additional backslashes if you find them superfluous (``unescape``). You can also leave them as-is using ``ignore`` strategy.
- `string_length_to_empty <./string_notation/string_length_to_empty.rst>`_ *(risky)*

  String tests for empty must be done against ``''``, not with ``strlen``.
- `string_line_ending <./string_notation/string_line_ending.rst>`_ *(risky)*

  All multi-line strings must use correct line ending.

Whitespace
----------

- `array_indentation <./whitespace/array_indentation.rst>`_

  Each element of an array must be indented exactly once.
- `blank_line_before_statement <./whitespace/blank_line_before_statement.rst>`_

  An empty line feed must precede any configured statement.
- `blank_line_between_import_groups <./whitespace/blank_line_between_import_groups.rst>`_

  Putting blank lines between ``use`` statement groups.
- `compact_nullable_type_declaration <./whitespace/compact_nullable_type_declaration.rst>`_

  Remove extra spaces in a nullable type declaration.
- `compact_nullable_typehint <./whitespace/compact_nullable_typehint.rst>`_ *(deprecated)*

  Remove extra spaces in a nullable typehint.
- `heredoc_indentation <./whitespace/heredoc_indentation.rst>`_

  Heredoc/nowdoc content must be properly indented.
- `indentation_type <./whitespace/indentation_type.rst>`_

  Code MUST use configured indentation type.
- `line_ending <./whitespace/line_ending.rst>`_

  All PHP files must use same line ending.
- `method_chaining_indentation <./whitespace/method_chaining_indentation.rst>`_

  Method chaining MUST be properly indented. Method chaining with different levels of indentation is not supported.
- `no_extra_blank_lines <./whitespace/no_extra_blank_lines.rst>`_

  Removes extra blank lines and/or blank lines following configuration.
- `no_spaces_around_offset <./whitespace/no_spaces_around_offset.rst>`_

  There MUST NOT be spaces around offset braces.
- `no_spaces_inside_parenthesis <./whitespace/no_spaces_inside_parenthesis.rst>`_ *(deprecated)*

  There MUST NOT be a space after the opening parenthesis. There MUST NOT be a space before the closing parenthesis.
- `no_trailing_whitespace <./whitespace/no_trailing_whitespace.rst>`_

  Remove trailing whitespace at the end of non-blank lines.
- `no_whitespace_in_blank_line <./whitespace/no_whitespace_in_blank_line.rst>`_

  Remove trailing whitespace at the end of blank lines.
- `single_blank_line_at_eof <./whitespace/single_blank_line_at_eof.rst>`_

  A PHP file without end tag must always end with a single empty line feed.
- `spaces_inside_parentheses <./whitespace/spaces_inside_parentheses.rst>`_

  Parentheses must be declared using the configured whitespace.
- `statement_indentation <./whitespace/statement_indentation.rst>`_

  Each statement must be indented.
- `type_declaration_spaces <./whitespace/type_declaration_spaces.rst>`_

  Ensure single space between a variable and its type declaration in function arguments and properties.
- `types_spaces <./whitespace/types_spaces.rst>`_

  A single space or none should be around union type and intersection type operators.
