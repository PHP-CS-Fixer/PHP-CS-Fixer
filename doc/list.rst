=======================
List of Available Rules
=======================

-  `align_multiline_comment <./rules/phpdoc/align_multiline_comment.rst>`_

   Each line of multi-line DocComments must have an asterisk [PSR-5] and must be aligned with the first one.

   Configuration options:

   - | ``comment_type``
     | Whether to fix PHPDoc comments only (`phpdocs_only`), any multi-line comment whose lines all start with an asterisk (`phpdocs_like`) or any multi-line comment (`all_multiline`).
     | Allowed values: ``'all_multiline'``, ``'phpdocs_like'``, ``'phpdocs_only'``
     | Default value: ``'phpdocs_only'``


   Part of rule set `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_

   `Source PhpCsFixer\\Fixer\\Phpdoc\\AlignMultilineCommentFixer <./../src/Fixer/Phpdoc/AlignMultilineCommentFixer.php>`_
-  `array_indentation <./rules/whitespace/array_indentation.rst>`_

   Each element of an array must be indented exactly once.

   Part of rule set `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_

   `Source PhpCsFixer\\Fixer\\Whitespace\\ArrayIndentationFixer <./../src/Fixer/Whitespace/ArrayIndentationFixer.php>`_
-  `array_push <./rules/alias/array_push.rst>`_

   Converts simple usages of ``array_push($x, $y);`` to ``$x[] = $y;``.

   *warning risky* Risky when the function ``array_push`` is overridden.

   Part of rule sets `@PhpCsFixer:risky <./ruleSets/PhpCsFixerRisky.rst>`_ `@Symfony:risky <./ruleSets/SymfonyRisky.rst>`_

   `Source PhpCsFixer\\Fixer\\Alias\\ArrayPushFixer <./../src/Fixer/Alias/ArrayPushFixer.php>`_
-  `array_syntax <./rules/array_notation/array_syntax.rst>`_

   PHP arrays should be declared using the configured syntax.

   Configuration options:

   - | ``syntax``
     | Whether to use the `long` or `short` array syntax.
     | Allowed values: ``'long'``, ``'short'``
     | Default value: ``'short'``


   Part of rule sets `@PHP54Migration <./ruleSets/PHP54Migration.rst>`_ `@PHP70Migration <./ruleSets/PHP70Migration.rst>`_ `@PHP71Migration <./ruleSets/PHP71Migration.rst>`_ `@PHP73Migration <./ruleSets/PHP73Migration.rst>`_ `@PHP74Migration <./ruleSets/PHP74Migration.rst>`_ `@PHP80Migration <./ruleSets/PHP80Migration.rst>`_ `@PHP81Migration <./ruleSets/PHP81Migration.rst>`_ `@PHP82Migration <./ruleSets/PHP82Migration.rst>`_ `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\ArrayNotation\\ArraySyntaxFixer <./../src/Fixer/ArrayNotation/ArraySyntaxFixer.php>`_
-  `assign_null_coalescing_to_coalesce_equal <./rules/operator/assign_null_coalescing_to_coalesce_equal.rst>`_

   Use the null coalescing assignment operator ``??=`` where possible.

   Part of rule sets `@PHP74Migration <./ruleSets/PHP74Migration.rst>`_ `@PHP80Migration <./ruleSets/PHP80Migration.rst>`_ `@PHP81Migration <./ruleSets/PHP81Migration.rst>`_ `@PHP82Migration <./ruleSets/PHP82Migration.rst>`_

   `Source PhpCsFixer\\Fixer\\Operator\\AssignNullCoalescingToCoalesceEqualFixer <./../src/Fixer/Operator/AssignNullCoalescingToCoalesceEqualFixer.php>`_
-  `backtick_to_shell_exec <./rules/alias/backtick_to_shell_exec.rst>`_

   Converts backtick operators to ``shell_exec`` calls.

   Conversion is done only when it is non risky, so when special chars like
   single-quotes, double-quotes and backticks are not used inside the command.

   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\Alias\\BacktickToShellExecFixer <./../src/Fixer/Alias/BacktickToShellExecFixer.php>`_
-  `binary_operator_spaces <./rules/operator/binary_operator_spaces.rst>`_

   Binary operators should be surrounded by space as configured.

   Configuration options:

   - | ``default``
     | Default fix strategy.
     | Allowed values: ``'align'``, ``'align_single_space'``, ``'align_single_space_minimal'``, ``'no_space'``, ``'single_space'``, ``null``
     | Default value: ``'single_space'``
   - | ``operators``
     | Dictionary of `binary operator` => `fix strategy` values that differ from the default strategy. Supported are: `=`, `*`, `/`, `%`, `<`, `>`, `|`, `^`, `+`, `-`, `&`, `&=`, `&&`, `||`, `.=`, `/=`, `=>`, `==`, `>=`, `===`, `!=`, `<>`, `!==`, `<=`, `and`, `or`, `xor`, `-=`, `%=`, `*=`, `|=`, `+=`, `<<`, `<<=`, `>>`, `>>=`, `^=`, `**`, `**=`, `<=>`, `??`, `??=`
     | Allowed types: ``array``
     | Default value: ``[]``


   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\Operator\\BinaryOperatorSpacesFixer <./../src/Fixer/Operator/BinaryOperatorSpacesFixer.php>`_
-  `blank_line_after_namespace <./rules/namespace_notation/blank_line_after_namespace.rst>`_

   There MUST be one blank line after the namespace declaration.

   Part of rule sets `@PER <./ruleSets/PER.rst>`_ `@PSR12 <./ruleSets/PSR12.rst>`_ `@PSR2 <./ruleSets/PSR2.rst>`_ `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\NamespaceNotation\\BlankLineAfterNamespaceFixer <./../src/Fixer/NamespaceNotation/BlankLineAfterNamespaceFixer.php>`_
-  `blank_line_after_opening_tag <./rules/php_tag/blank_line_after_opening_tag.rst>`_

   Ensure there is no code on the same line as the PHP open tag and it is followed by a blank line.

   Part of rule sets `@PER <./ruleSets/PER.rst>`_ `@PSR12 <./ruleSets/PSR12.rst>`_ `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\PhpTag\\BlankLineAfterOpeningTagFixer <./../src/Fixer/PhpTag/BlankLineAfterOpeningTagFixer.php>`_
-  `blank_line_before_statement <./rules/whitespace/blank_line_before_statement.rst>`_

   An empty line feed must precede any configured statement.

   Configuration options:

   - | ``statements``
     | List of statements which must be preceded by an empty line.
     | Allowed values: a subset of ``['break', 'case', 'continue', 'declare', 'default', 'do', 'exit', 'for', 'foreach', 'goto', 'if', 'include', 'include_once', 'phpdoc', 'require', 'require_once', 'return', 'switch', 'throw', 'try', 'while', 'yield', 'yield_from']``
     | Default value: ``['break', 'continue', 'declare', 'return', 'throw', 'try']``


   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\Whitespace\\BlankLineBeforeStatementFixer <./../src/Fixer/Whitespace/BlankLineBeforeStatementFixer.php>`_
-  `blank_line_between_import_groups <./rules/whitespace/blank_line_between_import_groups.rst>`_

   Putting blank lines between ``use`` statement groups.

   Part of rule sets `@PER <./ruleSets/PER.rst>`_ `@PSR12 <./ruleSets/PSR12.rst>`_ `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\Whitespace\\BlankLineBetweenImportGroupsFixer <./../src/Fixer/Whitespace/BlankLineBetweenImportGroupsFixer.php>`_
-  `braces <./rules/basic/braces.rst>`_

   The body of each structure MUST be enclosed by braces. Braces should be properly placed. Body of braces should be properly indented.

   Configuration options:

   - | ``allow_single_line_anonymous_class_with_empty_body``
     | Whether single line anonymous class with empty body notation should be allowed.
     | Allowed types: ``bool``
     | Default value: ``false``
   - | ``allow_single_line_closure``
     | Whether single line lambda notation should be allowed.
     | Allowed types: ``bool``
     | Default value: ``false``
   - | ``position_after_functions_and_oop_constructs``
     | whether the opening brace should be placed on "next" or "same" line after classy constructs (non-anonymous classes, interfaces, traits, methods and non-lambda functions).
     | Allowed values: ``'next'``, ``'same'``
     | Default value: ``'next'``
   - | ``position_after_control_structures``
     | whether the opening brace should be placed on "next" or "same" line after control structures.
     | Allowed values: ``'next'``, ``'same'``
     | Default value: ``'same'``
   - | ``position_after_anonymous_constructs``
     | whether the opening brace should be placed on "next" or "same" line after anonymous constructs (anonymous classes and lambda functions).
     | Allowed values: ``'next'``, ``'same'``
     | Default value: ``'same'``


   Part of rule sets `@PER <./ruleSets/PER.rst>`_ `@PSR12 <./ruleSets/PSR12.rst>`_ `@PSR2 <./ruleSets/PSR2.rst>`_ `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\Basic\\BracesFixer <./../src/Fixer/Basic/BracesFixer.php>`_
-  `cast_spaces <./rules/cast_notation/cast_spaces.rst>`_

   A single space or none should be between cast and variable.

   Configuration options:

   - | ``space``
     | spacing to apply between cast and variable.
     | Allowed values: ``'none'``, ``'single'``
     | Default value: ``'single'``


   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\CastNotation\\CastSpacesFixer <./../src/Fixer/CastNotation/CastSpacesFixer.php>`_
-  `class_attributes_separation <./rules/class_notation/class_attributes_separation.rst>`_

   Class, trait and interface elements must be separated with one or none blank line.

   Configuration options:

   - | ``elements``
     | Dictionary of `const|method|property|trait_import|case` => `none|one|only_if_meta` values.
     | Allowed types: ``array``
     | Default value: ``['const' => 'one', 'method' => 'one', 'property' => 'one', 'trait_import' => 'none', 'case' => 'none']``


   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\ClassNotation\\ClassAttributesSeparationFixer <./../src/Fixer/ClassNotation/ClassAttributesSeparationFixer.php>`_
-  `class_definition <./rules/class_notation/class_definition.rst>`_

   Whitespace around the keywords of a class, trait, enum or interfaces definition should be one space.

   Configuration options:

   - | ``multi_line_extends_each_single_line``
     | Whether definitions should be multiline.
     | Allowed types: ``bool``
     | Default value: ``false``
   - | ``single_item_single_line``
     | Whether definitions should be single line when including a single item.
     | Allowed types: ``bool``
     | Default value: ``false``
   - | ``single_line``
     | Whether definitions should be single line.
     | Allowed types: ``bool``
     | Default value: ``false``
   - | ``space_before_parenthesis``
     | Whether there should be a single space after the parenthesis of anonymous class (PSR12) or not.
     | Allowed types: ``bool``
     | Default value: ``false``
   - | ``inline_constructor_arguments``
     | Whether constructor argument list in anonymous classes should be single line.
     | Allowed types: ``bool``
     | Default value: ``true``


   Part of rule sets `@PER <./ruleSets/PER.rst>`_ `@PSR12 <./ruleSets/PSR12.rst>`_ `@PSR2 <./ruleSets/PSR2.rst>`_ `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\ClassNotation\\ClassDefinitionFixer <./../src/Fixer/ClassNotation/ClassDefinitionFixer.php>`_
-  `class_keyword_remove <./rules/language_construct/class_keyword_remove.rst>`_

   Converts ``::class`` keywords to FQCN strings.

   *warning deprecated*

   `Source PhpCsFixer\\Fixer\\LanguageConstruct\\ClassKeywordRemoveFixer <./../src/Fixer/LanguageConstruct/ClassKeywordRemoveFixer.php>`_
-  `class_reference_name_casing <./rules/casing/class_reference_name_casing.rst>`_

   When referencing an internal class it must be written using the correct casing.

   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\Casing\\ClassReferenceNameCasingFixer <./../src/Fixer/Casing/ClassReferenceNameCasingFixer.php>`_
-  `clean_namespace <./rules/namespace_notation/clean_namespace.rst>`_

   Namespace must not contain spacing, comments or PHPDoc.

   Part of rule sets `@PHP80Migration <./ruleSets/PHP80Migration.rst>`_ `@PHP81Migration <./ruleSets/PHP81Migration.rst>`_ `@PHP82Migration <./ruleSets/PHP82Migration.rst>`_ `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\NamespaceNotation\\CleanNamespaceFixer <./../src/Fixer/NamespaceNotation/CleanNamespaceFixer.php>`_
-  `combine_consecutive_issets <./rules/language_construct/combine_consecutive_issets.rst>`_

   Using ``isset($var) &&`` multiple times should be done in one call.

   Part of rule set `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_

   `Source PhpCsFixer\\Fixer\\LanguageConstruct\\CombineConsecutiveIssetsFixer <./../src/Fixer/LanguageConstruct/CombineConsecutiveIssetsFixer.php>`_
-  `combine_consecutive_unsets <./rules/language_construct/combine_consecutive_unsets.rst>`_

   Calling ``unset`` on multiple items should be done in one call.

   Part of rule set `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_

   `Source PhpCsFixer\\Fixer\\LanguageConstruct\\CombineConsecutiveUnsetsFixer <./../src/Fixer/LanguageConstruct/CombineConsecutiveUnsetsFixer.php>`_
-  `combine_nested_dirname <./rules/function_notation/combine_nested_dirname.rst>`_

   Replace multiple nested calls of ``dirname`` by only one call with second ``$level`` parameter. Requires PHP >= 7.0.

   *warning risky* Risky when the function ``dirname`` is overridden.

   Part of rule sets `@PHP70Migration:risky <./ruleSets/PHP70MigrationRisky.rst>`_ `@PHP71Migration:risky <./ruleSets/PHP71MigrationRisky.rst>`_ `@PHP74Migration:risky <./ruleSets/PHP74MigrationRisky.rst>`_ `@PHP80Migration:risky <./ruleSets/PHP80MigrationRisky.rst>`_ `@PhpCsFixer:risky <./ruleSets/PhpCsFixerRisky.rst>`_ `@Symfony:risky <./ruleSets/SymfonyRisky.rst>`_

   `Source PhpCsFixer\\Fixer\\FunctionNotation\\CombineNestedDirnameFixer <./../src/Fixer/FunctionNotation/CombineNestedDirnameFixer.php>`_
-  `comment_to_phpdoc <./rules/comment/comment_to_phpdoc.rst>`_

   Comments with annotation should be docblock when used on structural elements.

   *warning risky* Risky as new docblocks might mean more, e.g. a Doctrine entity might have a
   new column in database.

   Configuration options:

   - | ``ignored_tags``
     | List of ignored tags
     | Allowed types: ``array``
     | Default value: ``[]``


   Part of rule set `@PhpCsFixer:risky <./ruleSets/PhpCsFixerRisky.rst>`_

   `Source PhpCsFixer\\Fixer\\Comment\\CommentToPhpdocFixer <./../src/Fixer/Comment/CommentToPhpdocFixer.php>`_
-  `compact_nullable_typehint <./rules/whitespace/compact_nullable_typehint.rst>`_

   Remove extra spaces in a nullable typehint.

   Rule is applied only in a PHP 7.1+ environment.

   Part of rule sets `@PER <./ruleSets/PER.rst>`_ `@PSR12 <./ruleSets/PSR12.rst>`_ `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\Whitespace\\CompactNullableTypehintFixer <./../src/Fixer/Whitespace/CompactNullableTypehintFixer.php>`_
-  `concat_space <./rules/operator/concat_space.rst>`_

   Concatenation should be spaced according configuration.

   Configuration options:

   - | ``spacing``
     | Spacing to apply around concatenation operator.
     | Allowed values: ``'none'``, ``'one'``
     | Default value: ``'none'``


   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\Operator\\ConcatSpaceFixer <./../src/Fixer/Operator/ConcatSpaceFixer.php>`_
-  `constant_case <./rules/casing/constant_case.rst>`_

   The PHP constants ``true``, ``false``, and ``null`` MUST be written using the correct casing.

   Configuration options:

   - | ``case``
     | Whether to use the `upper` or `lower` case syntax.
     | Allowed values: ``'lower'``, ``'upper'``
     | Default value: ``'lower'``


   Part of rule sets `@PER <./ruleSets/PER.rst>`_ `@PSR12 <./ruleSets/PSR12.rst>`_ `@PSR2 <./ruleSets/PSR2.rst>`_ `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\Casing\\ConstantCaseFixer <./../src/Fixer/Casing/ConstantCaseFixer.php>`_
-  `control_structure_braces <./rules/control_structure/control_structure_braces.rst>`_

   The body of each control structure MUST be enclosed within braces.

   `Source PhpCsFixer\\Fixer\\ControlStructure\\ControlStructureBracesFixer <./../src/Fixer/ControlStructure/ControlStructureBracesFixer.php>`_
-  `control_structure_continuation_position <./rules/control_structure/control_structure_continuation_position.rst>`_

   Control structure continuation keyword must be on the configured line.

   Configuration options:

   - | ``position``
     | the position of the keyword that continues the control structure.
     | Allowed values: ``'next_line'``, ``'same_line'``
     | Default value: ``'same_line'``


   `Source PhpCsFixer\\Fixer\\ControlStructure\\ControlStructureContinuationPositionFixer <./../src/Fixer/ControlStructure/ControlStructureContinuationPositionFixer.php>`_
-  `curly_braces_position <./rules/basic/curly_braces_position.rst>`_

   Curly braces must be placed as configured.

   Configuration options:

   - | ``control_structures_opening_brace``
     | the position of the opening brace of control structures body.
     | Allowed values: ``'next_line_unless_newline_at_signature_end'``, ``'same_line'``
     | Default value: ``'same_line'``
   - | ``functions_opening_brace``
     | the position of the opening brace of functions body.
     | Allowed values: ``'next_line_unless_newline_at_signature_end'``, ``'same_line'``
     | Default value: ``'next_line_unless_newline_at_signature_end'``
   - | ``anonymous_functions_opening_brace``
     | the position of the opening brace of anonymous functions body.
     | Allowed values: ``'next_line_unless_newline_at_signature_end'``, ``'same_line'``
     | Default value: ``'same_line'``
   - | ``classes_opening_brace``
     | the position of the opening brace of classes body.
     | Allowed values: ``'next_line_unless_newline_at_signature_end'``, ``'same_line'``
     | Default value: ``'next_line_unless_newline_at_signature_end'``
   - | ``anonymous_classes_opening_brace``
     | the position of the opening brace of anonymous classes body.
     | Allowed values: ``'next_line_unless_newline_at_signature_end'``, ``'same_line'``
     | Default value: ``'same_line'``
   - | ``allow_single_line_empty_anonymous_classes``
     | allow anonymous classes to have opening and closing braces on the same line.
     | Allowed types: ``bool``
     | Default value: ``true``
   - | ``allow_single_line_anonymous_functions``
     | allow anonymous functions to have opening and closing braces on the same line.
     | Allowed types: ``bool``
     | Default value: ``true``


   `Source PhpCsFixer\\Fixer\\Basic\\CurlyBracesPositionFixer <./../src/Fixer/Basic/CurlyBracesPositionFixer.php>`_
-  `date_time_create_from_format_call <./rules/function_notation/date_time_create_from_format_call.rst>`_

   The first argument of ``DateTime::createFromFormat`` method must start with ``!``.

   Consider this code:
       ``DateTime::createFromFormat('Y-m-d', '2022-02-11')``.
       What value will be returned? '2022-02-11 00:00:00.0'? No, actual return
   value has 'H:i:s' section like '2022-02-11 16:55:37.0'.
       Change 'Y-m-d' to '!Y-m-d', return value will be '2022-02-11 00:00:00.0'.
       So, adding ``!`` to format string will make return value more intuitive.

   *warning risky* Risky when depending on the actual timings being used even when not explicit
   set in format.

   `Source PhpCsFixer\\Fixer\\FunctionNotation\\DateTimeCreateFromFormatCallFixer <./../src/Fixer/FunctionNotation/DateTimeCreateFromFormatCallFixer.php>`_
-  `date_time_immutable <./rules/class_usage/date_time_immutable.rst>`_

   Class ``DateTimeImmutable`` should be used instead of ``DateTime``.

   *warning risky* Risky when the code relies on modifying ``DateTime`` objects or if any of the
   ``date_create*`` functions are overridden.

   `Source PhpCsFixer\\Fixer\\ClassUsage\\DateTimeImmutableFixer <./../src/Fixer/ClassUsage/DateTimeImmutableFixer.php>`_
-  `declare_equal_normalize <./rules/language_construct/declare_equal_normalize.rst>`_

   Equal sign in declare statement should be surrounded by spaces or not following configuration.

   Configuration options:

   - | ``space``
     | Spacing to apply around the equal sign.
     | Allowed values: ``'none'``, ``'single'``
     | Default value: ``'none'``


   Part of rule sets `@PER <./ruleSets/PER.rst>`_ `@PSR12 <./ruleSets/PSR12.rst>`_ `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\LanguageConstruct\\DeclareEqualNormalizeFixer <./../src/Fixer/LanguageConstruct/DeclareEqualNormalizeFixer.php>`_
-  `declare_parentheses <./rules/language_construct/declare_parentheses.rst>`_

   There must not be spaces around ``declare`` statement parentheses.

   `Source PhpCsFixer\\Fixer\\LanguageConstruct\\DeclareParenthesesFixer <./../src/Fixer/LanguageConstruct/DeclareParenthesesFixer.php>`_
-  `declare_strict_types <./rules/strict/declare_strict_types.rst>`_

   Force strict types declaration in all files. Requires PHP >= 7.0.

   *warning risky* Forcing strict types will stop non strict code from working.

   Part of rule sets `@PHP70Migration:risky <./ruleSets/PHP70MigrationRisky.rst>`_ `@PHP71Migration:risky <./ruleSets/PHP71MigrationRisky.rst>`_ `@PHP74Migration:risky <./ruleSets/PHP74MigrationRisky.rst>`_ `@PHP80Migration:risky <./ruleSets/PHP80MigrationRisky.rst>`_

   `Source PhpCsFixer\\Fixer\\Strict\\DeclareStrictTypesFixer <./../src/Fixer/Strict/DeclareStrictTypesFixer.php>`_
-  `dir_constant <./rules/language_construct/dir_constant.rst>`_

   Replaces ``dirname(__FILE__)`` expression with equivalent ``__DIR__`` constant.

   *warning risky* Risky when the function ``dirname`` is overridden.

   Part of rule sets `@PhpCsFixer:risky <./ruleSets/PhpCsFixerRisky.rst>`_ `@Symfony:risky <./ruleSets/SymfonyRisky.rst>`_

   `Source PhpCsFixer\\Fixer\\LanguageConstruct\\DirConstantFixer <./../src/Fixer/LanguageConstruct/DirConstantFixer.php>`_
-  `doctrine_annotation_array_assignment <./rules/doctrine_annotation/doctrine_annotation_array_assignment.rst>`_

   Doctrine annotations must use configured operator for assignment in arrays.

   Configuration options:

   - | ``ignored_tags``
     | List of tags that must not be treated as Doctrine Annotations.
     | Allowed types: ``array``
     | Default value: ``['abstract', 'access', 'code', 'deprec', 'encode', 'exception', 'final', 'ingroup', 'inheritdoc', 'inheritDoc', 'magic', 'name', 'toc', 'tutorial', 'private', 'static', 'staticvar', 'staticVar', 'throw', 'api', 'author', 'category', 'copyright', 'deprecated', 'example', 'filesource', 'global', 'ignore', 'internal', 'license', 'link', 'method', 'package', 'param', 'property', 'property-read', 'property-write', 'return', 'see', 'since', 'source', 'subpackage', 'throws', 'todo', 'TODO', 'usedBy', 'uses', 'var', 'version', 'after', 'afterClass', 'backupGlobals', 'backupStaticAttributes', 'before', 'beforeClass', 'codeCoverageIgnore', 'codeCoverageIgnoreStart', 'codeCoverageIgnoreEnd', 'covers', 'coversDefaultClass', 'coversNothing', 'dataProvider', 'depends', 'expectedException', 'expectedExceptionCode', 'expectedExceptionMessage', 'expectedExceptionMessageRegExp', 'group', 'large', 'medium', 'preserveGlobalState', 'requires', 'runTestsInSeparateProcesses', 'runInSeparateProcess', 'small', 'test', 'testdox', 'ticket', 'uses', 'SuppressWarnings', 'noinspection', 'package_version', 'enduml', 'startuml', 'psalm', 'phpstan', 'template', 'fix', 'FIXME', 'fixme', 'override']``
   - | ``operator``
     | The operator to use.
     | Allowed values: ``':'``, ``'='``
     | Default value: ``'='``


   Part of rule set `@DoctrineAnnotation <./ruleSets/DoctrineAnnotation.rst>`_

   `Source PhpCsFixer\\Fixer\\DoctrineAnnotation\\DoctrineAnnotationArrayAssignmentFixer <./../src/Fixer/DoctrineAnnotation/DoctrineAnnotationArrayAssignmentFixer.php>`_
-  `doctrine_annotation_braces <./rules/doctrine_annotation/doctrine_annotation_braces.rst>`_

   Doctrine annotations without arguments must use the configured syntax.

   Configuration options:

   - | ``ignored_tags``
     | List of tags that must not be treated as Doctrine Annotations.
     | Allowed types: ``array``
     | Default value: ``['abstract', 'access', 'code', 'deprec', 'encode', 'exception', 'final', 'ingroup', 'inheritdoc', 'inheritDoc', 'magic', 'name', 'toc', 'tutorial', 'private', 'static', 'staticvar', 'staticVar', 'throw', 'api', 'author', 'category', 'copyright', 'deprecated', 'example', 'filesource', 'global', 'ignore', 'internal', 'license', 'link', 'method', 'package', 'param', 'property', 'property-read', 'property-write', 'return', 'see', 'since', 'source', 'subpackage', 'throws', 'todo', 'TODO', 'usedBy', 'uses', 'var', 'version', 'after', 'afterClass', 'backupGlobals', 'backupStaticAttributes', 'before', 'beforeClass', 'codeCoverageIgnore', 'codeCoverageIgnoreStart', 'codeCoverageIgnoreEnd', 'covers', 'coversDefaultClass', 'coversNothing', 'dataProvider', 'depends', 'expectedException', 'expectedExceptionCode', 'expectedExceptionMessage', 'expectedExceptionMessageRegExp', 'group', 'large', 'medium', 'preserveGlobalState', 'requires', 'runTestsInSeparateProcesses', 'runInSeparateProcess', 'small', 'test', 'testdox', 'ticket', 'uses', 'SuppressWarnings', 'noinspection', 'package_version', 'enduml', 'startuml', 'psalm', 'phpstan', 'template', 'fix', 'FIXME', 'fixme', 'override']``
   - | ``syntax``
     | Whether to add or remove braces.
     | Allowed values: ``'with_braces'``, ``'without_braces'``
     | Default value: ``'without_braces'``


   Part of rule set `@DoctrineAnnotation <./ruleSets/DoctrineAnnotation.rst>`_

   `Source PhpCsFixer\\Fixer\\DoctrineAnnotation\\DoctrineAnnotationBracesFixer <./../src/Fixer/DoctrineAnnotation/DoctrineAnnotationBracesFixer.php>`_
-  `doctrine_annotation_indentation <./rules/doctrine_annotation/doctrine_annotation_indentation.rst>`_

   Doctrine annotations must be indented with four spaces.

   Configuration options:

   - | ``ignored_tags``
     | List of tags that must not be treated as Doctrine Annotations.
     | Allowed types: ``array``
     | Default value: ``['abstract', 'access', 'code', 'deprec', 'encode', 'exception', 'final', 'ingroup', 'inheritdoc', 'inheritDoc', 'magic', 'name', 'toc', 'tutorial', 'private', 'static', 'staticvar', 'staticVar', 'throw', 'api', 'author', 'category', 'copyright', 'deprecated', 'example', 'filesource', 'global', 'ignore', 'internal', 'license', 'link', 'method', 'package', 'param', 'property', 'property-read', 'property-write', 'return', 'see', 'since', 'source', 'subpackage', 'throws', 'todo', 'TODO', 'usedBy', 'uses', 'var', 'version', 'after', 'afterClass', 'backupGlobals', 'backupStaticAttributes', 'before', 'beforeClass', 'codeCoverageIgnore', 'codeCoverageIgnoreStart', 'codeCoverageIgnoreEnd', 'covers', 'coversDefaultClass', 'coversNothing', 'dataProvider', 'depends', 'expectedException', 'expectedExceptionCode', 'expectedExceptionMessage', 'expectedExceptionMessageRegExp', 'group', 'large', 'medium', 'preserveGlobalState', 'requires', 'runTestsInSeparateProcesses', 'runInSeparateProcess', 'small', 'test', 'testdox', 'ticket', 'uses', 'SuppressWarnings', 'noinspection', 'package_version', 'enduml', 'startuml', 'psalm', 'phpstan', 'template', 'fix', 'FIXME', 'fixme', 'override']``
   - | ``indent_mixed_lines``
     | Whether to indent lines that have content before closing parenthesis.
     | Allowed types: ``bool``
     | Default value: ``false``


   Part of rule set `@DoctrineAnnotation <./ruleSets/DoctrineAnnotation.rst>`_

   `Source PhpCsFixer\\Fixer\\DoctrineAnnotation\\DoctrineAnnotationIndentationFixer <./../src/Fixer/DoctrineAnnotation/DoctrineAnnotationIndentationFixer.php>`_
-  `doctrine_annotation_spaces <./rules/doctrine_annotation/doctrine_annotation_spaces.rst>`_

   Fixes spaces in Doctrine annotations.

   There must not be any space around parentheses; commas must be preceded by no
   space and followed by one space; there must be no space around named
   arguments assignment operator; there must be one space around array
   assignment operator.

   Configuration options:

   - | ``ignored_tags``
     | List of tags that must not be treated as Doctrine Annotations.
     | Allowed types: ``array``
     | Default value: ``['abstract', 'access', 'code', 'deprec', 'encode', 'exception', 'final', 'ingroup', 'inheritdoc', 'inheritDoc', 'magic', 'name', 'toc', 'tutorial', 'private', 'static', 'staticvar', 'staticVar', 'throw', 'api', 'author', 'category', 'copyright', 'deprecated', 'example', 'filesource', 'global', 'ignore', 'internal', 'license', 'link', 'method', 'package', 'param', 'property', 'property-read', 'property-write', 'return', 'see', 'since', 'source', 'subpackage', 'throws', 'todo', 'TODO', 'usedBy', 'uses', 'var', 'version', 'after', 'afterClass', 'backupGlobals', 'backupStaticAttributes', 'before', 'beforeClass', 'codeCoverageIgnore', 'codeCoverageIgnoreStart', 'codeCoverageIgnoreEnd', 'covers', 'coversDefaultClass', 'coversNothing', 'dataProvider', 'depends', 'expectedException', 'expectedExceptionCode', 'expectedExceptionMessage', 'expectedExceptionMessageRegExp', 'group', 'large', 'medium', 'preserveGlobalState', 'requires', 'runTestsInSeparateProcesses', 'runInSeparateProcess', 'small', 'test', 'testdox', 'ticket', 'uses', 'SuppressWarnings', 'noinspection', 'package_version', 'enduml', 'startuml', 'psalm', 'phpstan', 'template', 'fix', 'FIXME', 'fixme', 'override']``
   - | ``around_parentheses``
     | Whether to fix spaces around parentheses.
     | Allowed types: ``bool``
     | Default value: ``true``
   - | ``around_commas``
     | Whether to fix spaces around commas.
     | Allowed types: ``bool``
     | Default value: ``true``
   - | ``before_argument_assignments``
     | Whether to add, remove or ignore spaces before argument assignment operator.
     | Allowed types: ``null``, ``bool``
     | Default value: ``false``
   - | ``after_argument_assignments``
     | Whether to add, remove or ignore spaces after argument assignment operator.
     | Allowed types: ``null``, ``bool``
     | Default value: ``false``
   - | ``before_array_assignments_equals``
     | Whether to add, remove or ignore spaces before array `=` assignment operator.
     | Allowed types: ``null``, ``bool``
     | Default value: ``true``
   - | ``after_array_assignments_equals``
     | Whether to add, remove or ignore spaces after array assignment `=` operator.
     | Allowed types: ``null``, ``bool``
     | Default value: ``true``
   - | ``before_array_assignments_colon``
     | Whether to add, remove or ignore spaces before array `:` assignment operator.
     | Allowed types: ``null``, ``bool``
     | Default value: ``true``
   - | ``after_array_assignments_colon``
     | Whether to add, remove or ignore spaces after array assignment `:` operator.
     | Allowed types: ``null``, ``bool``
     | Default value: ``true``


   Part of rule set `@DoctrineAnnotation <./ruleSets/DoctrineAnnotation.rst>`_

   `Source PhpCsFixer\\Fixer\\DoctrineAnnotation\\DoctrineAnnotationSpacesFixer <./../src/Fixer/DoctrineAnnotation/DoctrineAnnotationSpacesFixer.php>`_
-  `echo_tag_syntax <./rules/php_tag/echo_tag_syntax.rst>`_

   Replaces short-echo ``<?=`` with long format ``<?php echo``/``<?php print`` syntax, or vice-versa.

   Configuration options:

   - | ``format``
     | The desired language construct.
     | Allowed values: ``'long'``, ``'short'``
     | Default value: ``'long'``
   - | ``long_function``
     | The function to be used to expand the short echo tags
     | Allowed values: ``'echo'``, ``'print'``
     | Default value: ``'echo'``
   - | ``shorten_simple_statements_only``
     | Render short-echo tags only in case of simple code
     | Allowed types: ``bool``
     | Default value: ``true``


   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\PhpTag\\EchoTagSyntaxFixer <./../src/Fixer/PhpTag/EchoTagSyntaxFixer.php>`_
-  `elseif <./rules/control_structure/elseif.rst>`_

   The keyword ``elseif`` should be used instead of ``else if`` so that all control keywords look like single words.

   Part of rule sets `@PER <./ruleSets/PER.rst>`_ `@PSR12 <./ruleSets/PSR12.rst>`_ `@PSR2 <./ruleSets/PSR2.rst>`_ `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\ControlStructure\\ElseifFixer <./../src/Fixer/ControlStructure/ElseifFixer.php>`_
-  `empty_loop_body <./rules/control_structure/empty_loop_body.rst>`_

   Empty loop-body must be in configured style.

   Configuration options:

   - | ``style``
     | Style of empty loop-bodies.
     | Allowed values: ``'braces'``, ``'semicolon'``
     | Default value: ``'semicolon'``


   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\ControlStructure\\EmptyLoopBodyFixer <./../src/Fixer/ControlStructure/EmptyLoopBodyFixer.php>`_
-  `empty_loop_condition <./rules/control_structure/empty_loop_condition.rst>`_

   Empty loop-condition must be in configured style.

   Configuration options:

   - | ``style``
     | Style of empty loop-condition.
     | Allowed values: ``'for'``, ``'while'``
     | Default value: ``'while'``


   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\ControlStructure\\EmptyLoopConditionFixer <./../src/Fixer/ControlStructure/EmptyLoopConditionFixer.php>`_
-  `encoding <./rules/basic/encoding.rst>`_

   PHP code MUST use only UTF-8 without BOM (remove BOM).

   Part of rule sets `@PER <./ruleSets/PER.rst>`_ `@PSR1 <./ruleSets/PSR1.rst>`_ `@PSR12 <./ruleSets/PSR12.rst>`_ `@PSR2 <./ruleSets/PSR2.rst>`_ `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\Basic\\EncodingFixer <./../src/Fixer/Basic/EncodingFixer.php>`_
-  `ereg_to_preg <./rules/alias/ereg_to_preg.rst>`_

   Replace deprecated ``ereg`` regular expression functions with ``preg``.

   *warning risky* Risky if the ``ereg`` function is overridden.

   Part of rule sets `@PhpCsFixer:risky <./ruleSets/PhpCsFixerRisky.rst>`_ `@Symfony:risky <./ruleSets/SymfonyRisky.rst>`_

   `Source PhpCsFixer\\Fixer\\Alias\\EregToPregFixer <./../src/Fixer/Alias/EregToPregFixer.php>`_
-  `error_suppression <./rules/language_construct/error_suppression.rst>`_

   Error control operator should be added to deprecation notices and/or removed from other cases.

   *warning risky* Risky because adding/removing ``@`` might cause changes to code behaviour or
   if ``trigger_error`` function is overridden.

   Configuration options:

   - | ``mute_deprecation_error``
     | Whether to add `@` in deprecation notices.
     | Allowed types: ``bool``
     | Default value: ``true``
   - | ``noise_remaining_usages``
     | Whether to remove `@` in remaining usages.
     | Allowed types: ``bool``
     | Default value: ``false``
   - | ``noise_remaining_usages_exclude``
     | List of global functions to exclude from removing `@`
     | Allowed types: ``array``
     | Default value: ``[]``


   Part of rule sets `@PhpCsFixer:risky <./ruleSets/PhpCsFixerRisky.rst>`_ `@Symfony:risky <./ruleSets/SymfonyRisky.rst>`_

   `Source PhpCsFixer\\Fixer\\LanguageConstruct\\ErrorSuppressionFixer <./../src/Fixer/LanguageConstruct/ErrorSuppressionFixer.php>`_
-  `escape_implicit_backslashes <./rules/string_notation/escape_implicit_backslashes.rst>`_

   Escape implicit backslashes in strings and heredocs to ease the understanding of which are special chars interpreted by PHP and which not.

   In PHP double-quoted strings and heredocs some chars like ``n``, ``$`` or
   ``u`` have special meanings if preceded by a backslash (and some are special
   only if followed by other special chars), while a backslash preceding other
   chars are interpreted like a plain backslash. The precise list of those
   special chars is hard to remember and to identify quickly: this fixer escapes
   backslashes that do not start a special interpretation with the char after
   them.
   It is possible to fix also single-quoted strings: in this case there is no
   special chars apart from single-quote and backslash itself, so the fixer
   simply ensure that all backslashes are escaped. Both single and double
   backslashes are allowed in single-quoted strings, so the purpose in this
   context is mainly to have a uniformed way to have them written all over the
   codebase.

   Configuration options:

   - | ``single_quoted``
     | Whether to fix single-quoted strings.
     | Allowed types: ``bool``
     | Default value: ``false``
   - | ``double_quoted``
     | Whether to fix double-quoted strings.
     | Allowed types: ``bool``
     | Default value: ``true``
   - | ``heredoc_syntax``
     | Whether to fix heredoc syntax.
     | Allowed types: ``bool``
     | Default value: ``true``


   Part of rule set `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_

   `Source PhpCsFixer\\Fixer\\StringNotation\\EscapeImplicitBackslashesFixer <./../src/Fixer/StringNotation/EscapeImplicitBackslashesFixer.php>`_
-  `explicit_indirect_variable <./rules/language_construct/explicit_indirect_variable.rst>`_

   Add curly braces to indirect variables to make them clear to understand. Requires PHP >= 7.0.

   Part of rule set `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_

   `Source PhpCsFixer\\Fixer\\LanguageConstruct\\ExplicitIndirectVariableFixer <./../src/Fixer/LanguageConstruct/ExplicitIndirectVariableFixer.php>`_
-  `explicit_string_variable <./rules/string_notation/explicit_string_variable.rst>`_

   Converts implicit variables into explicit ones in double-quoted strings or heredoc syntax.

   The reasoning behind this rule is the following:
   - When there are two valid ways of doing the same thing, using both is
   confusing, there should be a coding standard to follow
   - PHP manual marks ``"$var"`` syntax as implicit and ``"${var}"`` syntax as
   explicit: explicit code should always be preferred
   - Explicit syntax allows word concatenation inside strings, e.g.
   ``"${var}IsAVar"``, implicit doesn't
   - Explicit syntax is easier to detect for IDE/editors and therefore has
   colors/highlight with higher contrast, which is easier to read
   Backtick operator is skipped because it is harder to handle; you can use
   ``backtick_to_shell_exec`` fixer to normalize backticks to strings

   Part of rule set `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_

   `Source PhpCsFixer\\Fixer\\StringNotation\\ExplicitStringVariableFixer <./../src/Fixer/StringNotation/ExplicitStringVariableFixer.php>`_
-  `final_class <./rules/class_notation/final_class.rst>`_

   All classes must be final, except abstract ones and Doctrine entities.

   No exception and no configuration are intentional. Beside Doctrine entities
   and of course abstract classes, there is no single reason not to declare all
   classes final. If you want to subclass a class, mark the parent class as
   abstract and create two child classes, one empty if necessary: you'll gain
   much more fine grained type-hinting. If you need to mock a standalone class,
   create an interface, or maybe it's a value-object that shouldn't be mocked at
   all. If you need to extend a standalone class, create an interface and use
   the Composite pattern. If you aren't ready yet for serious OOP, go with
   FinalInternalClassFixer, it's fine.

   *warning risky* Risky when subclassing non-abstract classes.

   `Source PhpCsFixer\\Fixer\\ClassNotation\\FinalClassFixer <./../src/Fixer/ClassNotation/FinalClassFixer.php>`_
-  `final_internal_class <./rules/class_notation/final_internal_class.rst>`_

   Internal classes should be ``final``.

   *warning risky* Changing classes to ``final`` might cause code execution to break.

   Configuration options:

   - | ``annotation_include``
     | Class level annotations tags that must be set in order to fix the class. (case insensitive)
     | Allowed types: ``array``
     | Default value: ``['@internal']``
   - | ``annotation_exclude``
     | Class level annotations tags that must be omitted to fix the class, even if all of the white list ones are used as well. (case insensitive)
     | Allowed types: ``array``
     | Default value: ``['@final', '@Entity', '@ORM\\Entity', '@ORM\\Mapping\\Entity', '@Mapping\\Entity', '@Document', '@ODM\\Document']``
   - | ``consider_absent_docblock_as_internal_class``
     | Should classes without any DocBlock be fixed to final?
     | Allowed types: ``bool``
     | Default value: ``false``


   Part of rule set `@PhpCsFixer:risky <./ruleSets/PhpCsFixerRisky.rst>`_

   `Source PhpCsFixer\\Fixer\\ClassNotation\\FinalInternalClassFixer <./../src/Fixer/ClassNotation/FinalInternalClassFixer.php>`_
-  `final_public_method_for_abstract_class <./rules/class_notation/final_public_method_for_abstract_class.rst>`_

   All ``public`` methods of ``abstract`` classes should be ``final``.

   Enforce API encapsulation in an inheritance architecture. If you want to
   override a method, use the Template method pattern.

   *warning risky* Risky when overriding ``public`` methods of ``abstract`` classes.

   `Source PhpCsFixer\\Fixer\\ClassNotation\\FinalPublicMethodForAbstractClassFixer <./../src/Fixer/ClassNotation/FinalPublicMethodForAbstractClassFixer.php>`_
-  `fopen_flags <./rules/function_notation/fopen_flags.rst>`_

   The flags in ``fopen`` calls must omit ``t``, and ``b`` must be omitted or included consistently.

   *warning risky* Risky when the function ``fopen`` is overridden.

   Configuration options:

   - | ``b_mode``
     | The `b` flag must be used (`true`) or omitted (`false`).
     | Allowed types: ``bool``
     | Default value: ``true``


   Part of rule sets `@PhpCsFixer:risky <./ruleSets/PhpCsFixerRisky.rst>`_ `@Symfony:risky <./ruleSets/SymfonyRisky.rst>`_

   `Source PhpCsFixer\\Fixer\\FunctionNotation\\FopenFlagsFixer <./../src/Fixer/FunctionNotation/FopenFlagsFixer.php>`_
-  `fopen_flag_order <./rules/function_notation/fopen_flag_order.rst>`_

   Order the flags in ``fopen`` calls, ``b`` and ``t`` must be last.

   *warning risky* Risky when the function ``fopen`` is overridden.

   Part of rule sets `@PhpCsFixer:risky <./ruleSets/PhpCsFixerRisky.rst>`_ `@Symfony:risky <./ruleSets/SymfonyRisky.rst>`_

   `Source PhpCsFixer\\Fixer\\FunctionNotation\\FopenFlagOrderFixer <./../src/Fixer/FunctionNotation/FopenFlagOrderFixer.php>`_
-  `fully_qualified_strict_types <./rules/import/fully_qualified_strict_types.rst>`_

   Transforms imported FQCN parameters and return types in function arguments to short version.

   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\Import\\FullyQualifiedStrictTypesFixer <./../src/Fixer/Import/FullyQualifiedStrictTypesFixer.php>`_
-  `full_opening_tag <./rules/php_tag/full_opening_tag.rst>`_

   PHP code must use the long ``<?php`` tags or short-echo ``<?=`` tags and not other tag variations.

   Part of rule sets `@PER <./ruleSets/PER.rst>`_ `@PSR1 <./ruleSets/PSR1.rst>`_ `@PSR12 <./ruleSets/PSR12.rst>`_ `@PSR2 <./ruleSets/PSR2.rst>`_ `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\PhpTag\\FullOpeningTagFixer <./../src/Fixer/PhpTag/FullOpeningTagFixer.php>`_
-  `function_declaration <./rules/function_notation/function_declaration.rst>`_

   Spaces should be properly placed in a function declaration.

   Configuration options:

   - | ``closure_function_spacing``
     | Spacing to use before open parenthesis for closures.
     | Allowed values: ``'none'``, ``'one'``
     | Default value: ``'one'``
   - | ``closure_fn_spacing``
     | Spacing to use before open parenthesis for short arrow functions.
     | Allowed values: ``'none'``, ``'one'``
     | Default value: ``'one'``
   - | ``trailing_comma_single_line``
     | Whether trailing commas are allowed in single line signatures.
     | Allowed types: ``bool``
     | Default value: ``false``


   Part of rule sets `@PER <./ruleSets/PER.rst>`_ `@PSR12 <./ruleSets/PSR12.rst>`_ `@PSR2 <./ruleSets/PSR2.rst>`_ `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\FunctionNotation\\FunctionDeclarationFixer <./../src/Fixer/FunctionNotation/FunctionDeclarationFixer.php>`_
-  `function_to_constant <./rules/language_construct/function_to_constant.rst>`_

   Replace core functions calls returning constants with the constants.

   *warning risky* Risky when any of the configured functions to replace are overridden.

   Configuration options:

   - | ``functions``
     | List of function names to fix.
     | Allowed values: a subset of ``['get_called_class', 'get_class', 'get_class_this', 'php_sapi_name', 'phpversion', 'pi']``
     | Default value: ``['get_called_class', 'get_class', 'get_class_this', 'php_sapi_name', 'phpversion', 'pi']``


   Part of rule sets `@PhpCsFixer:risky <./ruleSets/PhpCsFixerRisky.rst>`_ `@Symfony:risky <./ruleSets/SymfonyRisky.rst>`_

   `Source PhpCsFixer\\Fixer\\LanguageConstruct\\FunctionToConstantFixer <./../src/Fixer/LanguageConstruct/FunctionToConstantFixer.php>`_
-  `function_typehint_space <./rules/function_notation/function_typehint_space.rst>`_

   Ensure single space between function's argument and its typehint.

   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\FunctionNotation\\FunctionTypehintSpaceFixer <./../src/Fixer/FunctionNotation/FunctionTypehintSpaceFixer.php>`_
-  `general_phpdoc_annotation_remove <./rules/phpdoc/general_phpdoc_annotation_remove.rst>`_

   Configured annotations should be omitted from PHPDoc.

   Configuration options:

   - | ``annotations``
     | List of annotations to remove, e.g. `["author"]`.
     | Allowed types: ``array``
     | Default value: ``[]``
   - | ``case_sensitive``
     | Should annotations be case sensitive.
     | Allowed types: ``bool``
     | Default value: ``true``


   `Source PhpCsFixer\\Fixer\\Phpdoc\\GeneralPhpdocAnnotationRemoveFixer <./../src/Fixer/Phpdoc/GeneralPhpdocAnnotationRemoveFixer.php>`_
-  `general_phpdoc_tag_rename <./rules/phpdoc/general_phpdoc_tag_rename.rst>`_

   Renames PHPDoc tags.

   Configuration options:

   - | ``fix_annotation``
     | Whether annotation tags should be fixed.
     | Allowed types: ``bool``
     | Default value: ``true``
   - | ``fix_inline``
     | Whether inline tags should be fixed.
     | Allowed types: ``bool``
     | Default value: ``true``
   - | ``replacements``
     | A map of tags to replace.
     | Allowed types: ``array``
     | Default value: ``[]``
   - | ``case_sensitive``
     | Whether tags should be replaced only if they have exact same casing.
     | Allowed types: ``bool``
     | Default value: ``false``


   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\Phpdoc\\GeneralPhpdocTagRenameFixer <./../src/Fixer/Phpdoc/GeneralPhpdocTagRenameFixer.php>`_
-  `get_class_to_class_keyword <./rules/language_construct/get_class_to_class_keyword.rst>`_

   Replace ``get_class`` calls on object variables with class keyword syntax.

   *warning risky* Risky if the ``get_class`` function is overridden.

   Part of rule set `@PHP80Migration:risky <./ruleSets/PHP80MigrationRisky.rst>`_

   `Source PhpCsFixer\\Fixer\\LanguageConstruct\\GetClassToClassKeywordFixer <./../src/Fixer/LanguageConstruct/GetClassToClassKeywordFixer.php>`_
-  `global_namespace_import <./rules/import/global_namespace_import.rst>`_

   Imports or fully qualifies global classes/functions/constants.

   Configuration options:

   - | ``import_constants``
     | Whether to import, not import or ignore global constants.
     | Allowed values: ``false``, ``null``, ``true``
     | Default value: ``null``
   - | ``import_functions``
     | Whether to import, not import or ignore global functions.
     | Allowed values: ``false``, ``null``, ``true``
     | Default value: ``null``
   - | ``import_classes``
     | Whether to import, not import or ignore global classes.
     | Allowed values: ``false``, ``null``, ``true``
     | Default value: ``true``


   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\Import\\GlobalNamespaceImportFixer <./../src/Fixer/Import/GlobalNamespaceImportFixer.php>`_
-  `group_import <./rules/import/group_import.rst>`_

   There MUST be group use for the same namespaces.

   `Source PhpCsFixer\\Fixer\\Import\\GroupImportFixer <./../src/Fixer/Import/GroupImportFixer.php>`_
-  `header_comment <./rules/comment/header_comment.rst>`_

   Add, replace or remove header comment.

   Configuration options:

   - | ``header``
     | Proper header content.
     | Allowed types: ``string``
     | This option is required.
   - | ``comment_type``
     | Comment syntax type.
     | Allowed values: ``'comment'``, ``'PHPDoc'``
     | Default value: ``'comment'``
   - | ``location``
     | The location of the inserted header.
     | Allowed values: ``'after_declare_strict'``, ``'after_open'``
     | Default value: ``'after_declare_strict'``
   - | ``separate``
     | Whether the header should be separated from the file content with a new line.
     | Allowed values: ``'both'``, ``'bottom'``, ``'none'``, ``'top'``
     | Default value: ``'both'``


   `Source PhpCsFixer\\Fixer\\Comment\\HeaderCommentFixer <./../src/Fixer/Comment/HeaderCommentFixer.php>`_
-  `heredoc_indentation <./rules/whitespace/heredoc_indentation.rst>`_

   Heredoc/nowdoc content must be properly indented. Requires PHP >= 7.3.

   Configuration options:

   - | ``indentation``
     | Whether the indentation should be the same as in the start token line or one level more.
     | Allowed values: ``'same_as_start'``, ``'start_plus_one'``
     | Default value: ``'start_plus_one'``


   Part of rule sets `@PHP73Migration <./ruleSets/PHP73Migration.rst>`_ `@PHP74Migration <./ruleSets/PHP74Migration.rst>`_ `@PHP80Migration <./ruleSets/PHP80Migration.rst>`_ `@PHP81Migration <./ruleSets/PHP81Migration.rst>`_ `@PHP82Migration <./ruleSets/PHP82Migration.rst>`_

   `Source PhpCsFixer\\Fixer\\Whitespace\\HeredocIndentationFixer <./../src/Fixer/Whitespace/HeredocIndentationFixer.php>`_
-  `heredoc_to_nowdoc <./rules/string_notation/heredoc_to_nowdoc.rst>`_

   Convert ``heredoc`` to ``nowdoc`` where possible.

   Part of rule set `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_

   `Source PhpCsFixer\\Fixer\\StringNotation\\HeredocToNowdocFixer <./../src/Fixer/StringNotation/HeredocToNowdocFixer.php>`_
-  `implode_call <./rules/function_notation/implode_call.rst>`_

   Function ``implode`` must be called with 2 arguments in the documented order.

   *warning risky* Risky when the function ``implode`` is overridden.

   Part of rule sets `@PHP74Migration:risky <./ruleSets/PHP74MigrationRisky.rst>`_ `@PHP80Migration:risky <./ruleSets/PHP80MigrationRisky.rst>`_ `@PhpCsFixer:risky <./ruleSets/PhpCsFixerRisky.rst>`_ `@Symfony:risky <./ruleSets/SymfonyRisky.rst>`_

   `Source PhpCsFixer\\Fixer\\FunctionNotation\\ImplodeCallFixer <./../src/Fixer/FunctionNotation/ImplodeCallFixer.php>`_
-  `include <./rules/control_structure/include.rst>`_

   Include/Require and file path should be divided with a single space. File path should not be placed under brackets.

   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\ControlStructure\\IncludeFixer <./../src/Fixer/ControlStructure/IncludeFixer.php>`_
-  `increment_style <./rules/operator/increment_style.rst>`_

   Pre- or post-increment and decrement operators should be used if possible.

   Configuration options:

   - | ``style``
     | Whether to use pre- or post-increment and decrement operators.
     | Allowed values: ``'post'``, ``'pre'``
     | Default value: ``'pre'``


   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\Operator\\IncrementStyleFixer <./../src/Fixer/Operator/IncrementStyleFixer.php>`_
-  `indentation_type <./rules/whitespace/indentation_type.rst>`_

   Code MUST use configured indentation type.

   Part of rule sets `@PER <./ruleSets/PER.rst>`_ `@PSR12 <./ruleSets/PSR12.rst>`_ `@PSR2 <./ruleSets/PSR2.rst>`_ `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\Whitespace\\IndentationTypeFixer <./../src/Fixer/Whitespace/IndentationTypeFixer.php>`_
-  `integer_literal_case <./rules/casing/integer_literal_case.rst>`_

   Integer literals must be in correct case.

   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\Casing\\IntegerLiteralCaseFixer <./../src/Fixer/Casing/IntegerLiteralCaseFixer.php>`_
-  `is_null <./rules/language_construct/is_null.rst>`_

   Replaces ``is_null($var)`` expression with ``null === $var``.

   *warning risky* Risky when the function ``is_null`` is overridden.

   Part of rule sets `@PhpCsFixer:risky <./ruleSets/PhpCsFixerRisky.rst>`_ `@Symfony:risky <./ruleSets/SymfonyRisky.rst>`_

   `Source PhpCsFixer\\Fixer\\LanguageConstruct\\IsNullFixer <./../src/Fixer/LanguageConstruct/IsNullFixer.php>`_
-  `lambda_not_used_import <./rules/function_notation/lambda_not_used_import.rst>`_

   Lambda must not import variables it doesn't use.

   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\FunctionNotation\\LambdaNotUsedImportFixer <./../src/Fixer/FunctionNotation/LambdaNotUsedImportFixer.php>`_
-  `linebreak_after_opening_tag <./rules/php_tag/linebreak_after_opening_tag.rst>`_

   Ensure there is no code on the same line as the PHP open tag.

   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\PhpTag\\LinebreakAfterOpeningTagFixer <./../src/Fixer/PhpTag/LinebreakAfterOpeningTagFixer.php>`_
-  `line_ending <./rules/whitespace/line_ending.rst>`_

   All PHP files must use same line ending.

   Part of rule sets `@PER <./ruleSets/PER.rst>`_ `@PSR12 <./ruleSets/PSR12.rst>`_ `@PSR2 <./ruleSets/PSR2.rst>`_ `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\Whitespace\\LineEndingFixer <./../src/Fixer/Whitespace/LineEndingFixer.php>`_
-  `list_syntax <./rules/list_notation/list_syntax.rst>`_

   List (``array`` destructuring) assignment should be declared using the configured syntax. Requires PHP >= 7.1.

   Configuration options:

   - | ``syntax``
     | Whether to use the `long` or `short` `list` syntax.
     | Allowed values: ``'long'``, ``'short'``
     | Default value: ``'short'``


   Part of rule sets `@PHP71Migration <./ruleSets/PHP71Migration.rst>`_ `@PHP73Migration <./ruleSets/PHP73Migration.rst>`_ `@PHP74Migration <./ruleSets/PHP74Migration.rst>`_ `@PHP80Migration <./ruleSets/PHP80Migration.rst>`_ `@PHP81Migration <./ruleSets/PHP81Migration.rst>`_ `@PHP82Migration <./ruleSets/PHP82Migration.rst>`_

   `Source PhpCsFixer\\Fixer\\ListNotation\\ListSyntaxFixer <./../src/Fixer/ListNotation/ListSyntaxFixer.php>`_
-  `logical_operators <./rules/operator/logical_operators.rst>`_

   Use ``&&`` and ``||`` logical operators instead of ``and`` and ``or``.

   *warning risky* Risky, because you must double-check if using and/or with lower precedence
   was intentional.

   Part of rule sets `@PhpCsFixer:risky <./ruleSets/PhpCsFixerRisky.rst>`_ `@Symfony:risky <./ruleSets/SymfonyRisky.rst>`_

   `Source PhpCsFixer\\Fixer\\Operator\\LogicalOperatorsFixer <./../src/Fixer/Operator/LogicalOperatorsFixer.php>`_
-  `lowercase_cast <./rules/cast_notation/lowercase_cast.rst>`_

   Cast should be written in lower case.

   Part of rule sets `@PER <./ruleSets/PER.rst>`_ `@PSR12 <./ruleSets/PSR12.rst>`_ `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\CastNotation\\LowercaseCastFixer <./../src/Fixer/CastNotation/LowercaseCastFixer.php>`_
-  `lowercase_keywords <./rules/casing/lowercase_keywords.rst>`_

   PHP keywords MUST be in lower case.

   Part of rule sets `@PER <./ruleSets/PER.rst>`_ `@PSR12 <./ruleSets/PSR12.rst>`_ `@PSR2 <./ruleSets/PSR2.rst>`_ `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\Casing\\LowercaseKeywordsFixer <./../src/Fixer/Casing/LowercaseKeywordsFixer.php>`_
-  `lowercase_static_reference <./rules/casing/lowercase_static_reference.rst>`_

   Class static references ``self``, ``static`` and ``parent`` MUST be in lower case.

   Part of rule sets `@PER <./ruleSets/PER.rst>`_ `@PSR12 <./ruleSets/PSR12.rst>`_ `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\Casing\\LowercaseStaticReferenceFixer <./../src/Fixer/Casing/LowercaseStaticReferenceFixer.php>`_
-  `magic_constant_casing <./rules/casing/magic_constant_casing.rst>`_

   Magic constants should be referred to using the correct casing.

   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\Casing\\MagicConstantCasingFixer <./../src/Fixer/Casing/MagicConstantCasingFixer.php>`_
-  `magic_method_casing <./rules/casing/magic_method_casing.rst>`_

   Magic method definitions and calls must be using the correct casing.

   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\Casing\\MagicMethodCasingFixer <./../src/Fixer/Casing/MagicMethodCasingFixer.php>`_
-  `mb_str_functions <./rules/alias/mb_str_functions.rst>`_

   Replace non multibyte-safe functions with corresponding mb function.

   *warning risky* Risky when any of the functions are overridden, or when relying on the string
   byte size rather than its length in characters.

   `Source PhpCsFixer\\Fixer\\Alias\\MbStrFunctionsFixer <./../src/Fixer/Alias/MbStrFunctionsFixer.php>`_
-  `method_argument_space <./rules/function_notation/method_argument_space.rst>`_

   In method arguments and method call, there MUST NOT be a space before each comma and there MUST be one space after each comma. Argument lists MAY be split across multiple lines, where each subsequent line is indented once. When doing so, the first item in the list MUST be on the next line, and there MUST be only one argument per line.

   Configuration options:

   - | ``keep_multiple_spaces_after_comma``
     | Whether keep multiple spaces after comma.
     | Allowed types: ``bool``
     | Default value: ``false``
   - | ``on_multiline``
     | Defines how to handle function arguments lists that contain newlines.
     | Allowed values: ``'ensure_fully_multiline'``, ``'ensure_single_line'``, ``'ignore'``
     | Default value: ``'ensure_fully_multiline'``
   - | ``after_heredoc``
     | Whether the whitespace between heredoc end and comma should be removed.
     | Allowed types: ``bool``
     | Default value: ``false``


   Part of rule sets `@PER <./ruleSets/PER.rst>`_ `@PHP73Migration <./ruleSets/PHP73Migration.rst>`_ `@PHP74Migration <./ruleSets/PHP74Migration.rst>`_ `@PHP80Migration <./ruleSets/PHP80Migration.rst>`_ `@PHP81Migration <./ruleSets/PHP81Migration.rst>`_ `@PHP82Migration <./ruleSets/PHP82Migration.rst>`_ `@PSR12 <./ruleSets/PSR12.rst>`_ `@PSR2 <./ruleSets/PSR2.rst>`_ `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\FunctionNotation\\MethodArgumentSpaceFixer <./../src/Fixer/FunctionNotation/MethodArgumentSpaceFixer.php>`_
-  `method_chaining_indentation <./rules/whitespace/method_chaining_indentation.rst>`_

   Method chaining MUST be properly indented. Method chaining with different levels of indentation is not supported.

   Part of rule set `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_

   `Source PhpCsFixer\\Fixer\\Whitespace\\MethodChainingIndentationFixer <./../src/Fixer/Whitespace/MethodChainingIndentationFixer.php>`_
-  `modernize_strpos <./rules/alias/modernize_strpos.rst>`_

   Replace ``strpos()`` calls with ``str_starts_with()`` or ``str_contains()`` if possible.

   *warning risky* Risky if ``strpos``, ``str_starts_with`` or ``str_contains`` functions are
   overridden.

   Part of rule set `@PHP80Migration:risky <./ruleSets/PHP80MigrationRisky.rst>`_

   `Source PhpCsFixer\\Fixer\\Alias\\ModernizeStrposFixer <./../src/Fixer/Alias/ModernizeStrposFixer.php>`_
-  `modernize_types_casting <./rules/cast_notation/modernize_types_casting.rst>`_

   Replaces ``intval``, ``floatval``, ``doubleval``, ``strval`` and ``boolval`` function calls with according type casting operator.

   *warning risky* Risky if any of the functions ``intval``, ``floatval``, ``doubleval``,
   ``strval`` or ``boolval`` are overridden.

   Part of rule sets `@PhpCsFixer:risky <./ruleSets/PhpCsFixerRisky.rst>`_ `@Symfony:risky <./ruleSets/SymfonyRisky.rst>`_

   `Source PhpCsFixer\\Fixer\\CastNotation\\ModernizeTypesCastingFixer <./../src/Fixer/CastNotation/ModernizeTypesCastingFixer.php>`_
-  `multiline_comment_opening_closing <./rules/comment/multiline_comment_opening_closing.rst>`_

   DocBlocks must start with two asterisks, multiline comments must start with a single asterisk, after the opening slash. Both must end with a single asterisk before the closing slash.

   Part of rule set `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_

   `Source PhpCsFixer\\Fixer\\Comment\\MultilineCommentOpeningClosingFixer <./../src/Fixer/Comment/MultilineCommentOpeningClosingFixer.php>`_
-  `multiline_whitespace_before_semicolons <./rules/semicolon/multiline_whitespace_before_semicolons.rst>`_

   Forbid multi-line whitespace before the closing semicolon or move the semicolon to the new line for chained calls.

   Configuration options:

   - | ``strategy``
     | Forbid multi-line whitespace or move the semicolon to the new line for chained calls.
     | Allowed values: ``'new_line_for_chained_calls'``, ``'no_multi_line'``
     | Default value: ``'no_multi_line'``


   Part of rule set `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_

   `Source PhpCsFixer\\Fixer\\Semicolon\\MultilineWhitespaceBeforeSemicolonsFixer <./../src/Fixer/Semicolon/MultilineWhitespaceBeforeSemicolonsFixer.php>`_
-  `native_constant_invocation <./rules/constant_notation/native_constant_invocation.rst>`_

   Add leading ``\`` before constant invocation of internal constant to speed up resolving. Constant name match is case-sensitive, except for ``null``, ``false`` and ``true``.

   *warning risky* Risky when any of the constants are namespaced or overridden.

   Configuration options:

   - | ``fix_built_in``
     | Whether to fix constants returned by `get_defined_constants`. User constants are not accounted in this list and must be specified in the include one.
     | Allowed types: ``bool``
     | Default value: ``true``
   - | ``include``
     | List of additional constants to fix.
     | Allowed types: ``array``
     | Default value: ``[]``
   - | ``exclude``
     | List of constants to ignore.
     | Allowed types: ``array``
     | Default value: ``['null', 'false', 'true']``
   - | ``scope``
     | Only fix constant invocations that are made within a namespace or fix all.
     | Allowed values: ``'all'``, ``'namespaced'``
     | Default value: ``'all'``
   - | ``strict``
     | Whether leading `\` of constant invocation not meant to have it should be removed.
     | Allowed types: ``bool``
     | Default value: ``true``


   Part of rule sets `@PhpCsFixer:risky <./ruleSets/PhpCsFixerRisky.rst>`_ `@Symfony:risky <./ruleSets/SymfonyRisky.rst>`_

   `Source PhpCsFixer\\Fixer\\ConstantNotation\\NativeConstantInvocationFixer <./../src/Fixer/ConstantNotation/NativeConstantInvocationFixer.php>`_
-  `native_function_casing <./rules/casing/native_function_casing.rst>`_

   Function defined by PHP should be called using the correct casing.

   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\Casing\\NativeFunctionCasingFixer <./../src/Fixer/Casing/NativeFunctionCasingFixer.php>`_
-  `native_function_invocation <./rules/function_notation/native_function_invocation.rst>`_

   Add leading ``\`` before function invocation to speed up resolving.

   *warning risky* Risky when any of the functions are overridden.

   Configuration options:

   - | ``exclude``
     | List of functions to ignore.
     | Allowed types: ``array``
     | Default value: ``[]``
   - | ``include``
     | List of function names or sets to fix. Defined sets are `@internal` (all native functions), `@all` (all global functions) and `@compiler_optimized` (functions that are specially optimized by Zend).
     | Allowed types: ``array``
     | Default value: ``['@compiler_optimized']``
   - | ``scope``
     | Only fix function calls that are made within a namespace or fix all.
     | Allowed values: ``'all'``, ``'namespaced'``
     | Default value: ``'all'``
   - | ``strict``
     | Whether leading `\` of function call not meant to have it should be removed.
     | Allowed types: ``bool``
     | Default value: ``true``


   Part of rule sets `@PhpCsFixer:risky <./ruleSets/PhpCsFixerRisky.rst>`_ `@Symfony:risky <./ruleSets/SymfonyRisky.rst>`_

   `Source PhpCsFixer\\Fixer\\FunctionNotation\\NativeFunctionInvocationFixer <./../src/Fixer/FunctionNotation/NativeFunctionInvocationFixer.php>`_
-  `native_function_type_declaration_casing <./rules/casing/native_function_type_declaration_casing.rst>`_

   Native type hints for functions should use the correct case.

   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\Casing\\NativeFunctionTypeDeclarationCasingFixer <./../src/Fixer/Casing/NativeFunctionTypeDeclarationCasingFixer.php>`_
-  `new_with_braces <./rules/operator/new_with_braces.rst>`_

   All instances created with ``new`` keyword must (not) be followed by braces.

   Configuration options:

   - | ``named_class``
     | Whether named classes should be followed by parentheses.
     | Allowed types: ``bool``
     | Default value: ``true``
   - | ``anonymous_class``
     | Whether anonymous classes should be followed by parentheses.
     | Allowed types: ``bool``
     | Default value: ``true``


   Part of rule sets `@PER <./ruleSets/PER.rst>`_ `@PSR12 <./ruleSets/PSR12.rst>`_ `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\Operator\\NewWithBracesFixer <./../src/Fixer/Operator/NewWithBracesFixer.php>`_
-  `non_printable_character <./rules/basic/non_printable_character.rst>`_

   Remove Zero-width space (ZWSP), Non-breaking space (NBSP) and other invisible unicode symbols.

   *warning risky* Risky when strings contain intended invisible characters.

   Configuration options:

   - | ``use_escape_sequences_in_strings``
     | Whether characters should be replaced with escape sequences in strings.
     | Allowed types: ``bool``
     | Default value: ``true``


   Part of rule sets `@PHP70Migration:risky <./ruleSets/PHP70MigrationRisky.rst>`_ `@PHP71Migration:risky <./ruleSets/PHP71MigrationRisky.rst>`_ `@PHP74Migration:risky <./ruleSets/PHP74MigrationRisky.rst>`_ `@PHP80Migration:risky <./ruleSets/PHP80MigrationRisky.rst>`_ `@PhpCsFixer:risky <./ruleSets/PhpCsFixerRisky.rst>`_ `@Symfony:risky <./ruleSets/SymfonyRisky.rst>`_

   `Source PhpCsFixer\\Fixer\\Basic\\NonPrintableCharacterFixer <./../src/Fixer/Basic/NonPrintableCharacterFixer.php>`_
-  `normalize_index_brace <./rules/array_notation/normalize_index_brace.rst>`_

   Array index should always be written by using square braces.

   Part of rule sets `@PHP74Migration <./ruleSets/PHP74Migration.rst>`_ `@PHP80Migration <./ruleSets/PHP80Migration.rst>`_ `@PHP81Migration <./ruleSets/PHP81Migration.rst>`_ `@PHP82Migration <./ruleSets/PHP82Migration.rst>`_ `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\ArrayNotation\\NormalizeIndexBraceFixer <./../src/Fixer/ArrayNotation/NormalizeIndexBraceFixer.php>`_
-  `not_operator_with_space <./rules/operator/not_operator_with_space.rst>`_

   Logical NOT operators (``!``) should have leading and trailing whitespaces.

   `Source PhpCsFixer\\Fixer\\Operator\\NotOperatorWithSpaceFixer <./../src/Fixer/Operator/NotOperatorWithSpaceFixer.php>`_
-  `not_operator_with_successor_space <./rules/operator/not_operator_with_successor_space.rst>`_

   Logical NOT operators (``!``) should have one trailing whitespace.

   `Source PhpCsFixer\\Fixer\\Operator\\NotOperatorWithSuccessorSpaceFixer <./../src/Fixer/Operator/NotOperatorWithSuccessorSpaceFixer.php>`_
-  `no_alias_functions <./rules/alias/no_alias_functions.rst>`_

   Master functions shall be used instead of aliases.

   *warning risky* Risky when any of the alias functions are overridden.

   Configuration options:

   - | ``sets``
     | List of sets to fix. Defined sets are:

* `@all` (all listed sets)
* `@internal` (native functions)
* `@exif` (EXIF functions)
* `@ftp` (FTP functions)
* `@IMAP` (IMAP functions)
* `@ldap` (LDAP functions)
* `@mbreg` (from `ext-mbstring`)
* `@mysqli` (mysqli functions)
* `@oci` (oci functions)
* `@odbc` (odbc functions)
* `@openssl` (openssl functions)
* `@pcntl` (PCNTL functions)
* `@pg` (pg functions)
* `@posix` (POSIX functions)
* `@snmp` (SNMP functions)
* `@sodium` (libsodium functions)
* `@time` (time functions)

     | Allowed values: a subset of ``['@all', '@exif', '@ftp', '@IMAP', '@internal', '@ldap', '@mbreg', '@mysqli', '@oci', '@odbc', '@openssl', '@pcntl', '@pg', '@posix', '@snmp', '@sodium', '@time']``
     | Default value: ``['@internal', '@IMAP', '@pg']``


   Part of rule sets `@PHP74Migration:risky <./ruleSets/PHP74MigrationRisky.rst>`_ `@PHP80Migration:risky <./ruleSets/PHP80MigrationRisky.rst>`_ `@PhpCsFixer:risky <./ruleSets/PhpCsFixerRisky.rst>`_ `@Symfony:risky <./ruleSets/SymfonyRisky.rst>`_

   `Source PhpCsFixer\\Fixer\\Alias\\NoAliasFunctionsFixer <./../src/Fixer/Alias/NoAliasFunctionsFixer.php>`_
-  `no_alias_language_construct_call <./rules/alias/no_alias_language_construct_call.rst>`_

   Master language constructs shall be used instead of aliases.

   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\Alias\\NoAliasLanguageConstructCallFixer <./../src/Fixer/Alias/NoAliasLanguageConstructCallFixer.php>`_
-  `no_alternative_syntax <./rules/control_structure/no_alternative_syntax.rst>`_

   Replace control structure alternative syntax to use braces.

   Configuration options:

   - | ``fix_non_monolithic_code``
     | Whether to also fix code with inline HTML.
     | Allowed types: ``bool``
     | Default value: ``true``


   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\ControlStructure\\NoAlternativeSyntaxFixer <./../src/Fixer/ControlStructure/NoAlternativeSyntaxFixer.php>`_
-  `no_binary_string <./rules/string_notation/no_binary_string.rst>`_

   There should not be a binary flag before strings.

   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\StringNotation\\NoBinaryStringFixer <./../src/Fixer/StringNotation/NoBinaryStringFixer.php>`_
-  `no_blank_lines_after_class_opening <./rules/class_notation/no_blank_lines_after_class_opening.rst>`_

   There should be no empty lines after class opening brace.

   Part of rule sets `@PER <./ruleSets/PER.rst>`_ `@PSR12 <./ruleSets/PSR12.rst>`_ `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\ClassNotation\\NoBlankLinesAfterClassOpeningFixer <./../src/Fixer/ClassNotation/NoBlankLinesAfterClassOpeningFixer.php>`_
-  `no_blank_lines_after_phpdoc <./rules/phpdoc/no_blank_lines_after_phpdoc.rst>`_

   There should not be blank lines between docblock and the documented element.

   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\Phpdoc\\NoBlankLinesAfterPhpdocFixer <./../src/Fixer/Phpdoc/NoBlankLinesAfterPhpdocFixer.php>`_
-  `no_blank_lines_before_namespace <./rules/namespace_notation/no_blank_lines_before_namespace.rst>`_

   There should be no blank lines before a namespace declaration.

   `Source PhpCsFixer\\Fixer\\NamespaceNotation\\NoBlankLinesBeforeNamespaceFixer <./../src/Fixer/NamespaceNotation/NoBlankLinesBeforeNamespaceFixer.php>`_
-  `no_break_comment <./rules/control_structure/no_break_comment.rst>`_

   There must be a comment when fall-through is intentional in a non-empty case body.

   Adds a "no break" comment before fall-through cases, and removes it if there
   is no fall-through.

   Configuration options:

   - | ``comment_text``
     | The text to use in the added comment and to detect it.
     | Allowed types: ``string``
     | Default value: ``'no break'``


   Part of rule sets `@PER <./ruleSets/PER.rst>`_ `@PSR12 <./ruleSets/PSR12.rst>`_ `@PSR2 <./ruleSets/PSR2.rst>`_ `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\ControlStructure\\NoBreakCommentFixer <./../src/Fixer/ControlStructure/NoBreakCommentFixer.php>`_
-  `no_closing_tag <./rules/php_tag/no_closing_tag.rst>`_

   The closing ``?>`` tag MUST be omitted from files containing only PHP.

   Part of rule sets `@PER <./ruleSets/PER.rst>`_ `@PSR12 <./ruleSets/PSR12.rst>`_ `@PSR2 <./ruleSets/PSR2.rst>`_ `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\PhpTag\\NoClosingTagFixer <./../src/Fixer/PhpTag/NoClosingTagFixer.php>`_
-  `no_empty_comment <./rules/comment/no_empty_comment.rst>`_

   There should not be any empty comments.

   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\Comment\\NoEmptyCommentFixer <./../src/Fixer/Comment/NoEmptyCommentFixer.php>`_
-  `no_empty_phpdoc <./rules/phpdoc/no_empty_phpdoc.rst>`_

   There should not be empty PHPDoc blocks.

   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\Phpdoc\\NoEmptyPhpdocFixer <./../src/Fixer/Phpdoc/NoEmptyPhpdocFixer.php>`_
-  `no_empty_statement <./rules/semicolon/no_empty_statement.rst>`_

   Remove useless (semicolon) statements.

   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\Semicolon\\NoEmptyStatementFixer <./../src/Fixer/Semicolon/NoEmptyStatementFixer.php>`_
-  `no_extra_blank_lines <./rules/whitespace/no_extra_blank_lines.rst>`_

   Removes extra blank lines and/or blank lines following configuration.

   Configuration options:

   - | ``tokens``
     | List of tokens to fix.
     | Allowed values: a subset of ``['attribute', 'break', 'case', 'continue', 'curly_brace_block', 'default', 'extra', 'parenthesis_brace_block', 'return', 'square_brace_block', 'switch', 'throw', 'use', 'use_trait']``
     | Default value: ``['extra']``


   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\Whitespace\\NoExtraBlankLinesFixer <./../src/Fixer/Whitespace/NoExtraBlankLinesFixer.php>`_
-  `no_homoglyph_names <./rules/naming/no_homoglyph_names.rst>`_

   Replace accidental usage of homoglyphs (non ascii characters) in names.

   *warning risky* Renames classes and cannot rename the files. You might have string references
   to renamed code (``$$name``).

   Part of rule sets `@PhpCsFixer:risky <./ruleSets/PhpCsFixerRisky.rst>`_ `@Symfony:risky <./ruleSets/SymfonyRisky.rst>`_

   `Source PhpCsFixer\\Fixer\\Naming\\NoHomoglyphNamesFixer <./../src/Fixer/Naming/NoHomoglyphNamesFixer.php>`_
-  `no_leading_import_slash <./rules/import/no_leading_import_slash.rst>`_

   Remove leading slashes in ``use`` clauses.

   Part of rule sets `@PER <./ruleSets/PER.rst>`_ `@PSR12 <./ruleSets/PSR12.rst>`_ `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\Import\\NoLeadingImportSlashFixer <./../src/Fixer/Import/NoLeadingImportSlashFixer.php>`_
-  `no_leading_namespace_whitespace <./rules/namespace_notation/no_leading_namespace_whitespace.rst>`_

   The namespace declaration line shouldn't contain leading whitespace.

   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\NamespaceNotation\\NoLeadingNamespaceWhitespaceFixer <./../src/Fixer/NamespaceNotation/NoLeadingNamespaceWhitespaceFixer.php>`_
-  `no_mixed_echo_print <./rules/alias/no_mixed_echo_print.rst>`_

   Either language construct ``print`` or ``echo`` should be used.

   Configuration options:

   - | ``use``
     | The desired language construct.
     | Allowed values: ``'echo'``, ``'print'``
     | Default value: ``'echo'``


   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\Alias\\NoMixedEchoPrintFixer <./../src/Fixer/Alias/NoMixedEchoPrintFixer.php>`_
-  `no_multiline_whitespace_around_double_arrow <./rules/array_notation/no_multiline_whitespace_around_double_arrow.rst>`_

   Operator ``=>`` should not be surrounded by multi-line whitespaces.

   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\ArrayNotation\\NoMultilineWhitespaceAroundDoubleArrowFixer <./../src/Fixer/ArrayNotation/NoMultilineWhitespaceAroundDoubleArrowFixer.php>`_
-  `no_multiple_statements_per_line <./rules/basic/no_multiple_statements_per_line.rst>`_

   There must not be more than one statement per line.

   `Source PhpCsFixer\\Fixer\\Basic\\NoMultipleStatementsPerLineFixer <./../src/Fixer/Basic/NoMultipleStatementsPerLineFixer.php>`_
-  `no_null_property_initialization <./rules/class_notation/no_null_property_initialization.rst>`_

   Properties MUST not be explicitly initialized with ``null`` except when they have a type declaration (PHP 7.4).

   Part of rule set `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_

   `Source PhpCsFixer\\Fixer\\ClassNotation\\NoNullPropertyInitializationFixer <./../src/Fixer/ClassNotation/NoNullPropertyInitializationFixer.php>`_
-  `no_php4_constructor <./rules/class_notation/no_php4_constructor.rst>`_

   Convert PHP4-style constructors to ``__construct``.

   *warning risky* Risky when old style constructor being fixed is overridden or overrides
   parent one.

   Part of rule sets `@PHP80Migration:risky <./ruleSets/PHP80MigrationRisky.rst>`_ `@PhpCsFixer:risky <./ruleSets/PhpCsFixerRisky.rst>`_ `@Symfony:risky <./ruleSets/SymfonyRisky.rst>`_

   `Source PhpCsFixer\\Fixer\\ClassNotation\\NoPhp4ConstructorFixer <./../src/Fixer/ClassNotation/NoPhp4ConstructorFixer.php>`_
-  `no_short_bool_cast <./rules/cast_notation/no_short_bool_cast.rst>`_

   Short cast ``bool`` using double exclamation mark should not be used.

   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\CastNotation\\NoShortBoolCastFixer <./../src/Fixer/CastNotation/NoShortBoolCastFixer.php>`_
-  `no_singleline_whitespace_before_semicolons <./rules/semicolon/no_singleline_whitespace_before_semicolons.rst>`_

   Single-line whitespace before closing semicolon are prohibited.

   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\Semicolon\\NoSinglelineWhitespaceBeforeSemicolonsFixer <./../src/Fixer/Semicolon/NoSinglelineWhitespaceBeforeSemicolonsFixer.php>`_
-  `no_spaces_after_function_name <./rules/function_notation/no_spaces_after_function_name.rst>`_

   When making a method or function call, there MUST NOT be a space between the method or function name and the opening parenthesis.

   Part of rule sets `@PER <./ruleSets/PER.rst>`_ `@PSR12 <./ruleSets/PSR12.rst>`_ `@PSR2 <./ruleSets/PSR2.rst>`_ `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\FunctionNotation\\NoSpacesAfterFunctionNameFixer <./../src/Fixer/FunctionNotation/NoSpacesAfterFunctionNameFixer.php>`_
-  `no_spaces_around_offset <./rules/whitespace/no_spaces_around_offset.rst>`_

   There MUST NOT be spaces around offset braces.

   Configuration options:

   - | ``positions``
     | Whether spacing should be fixed inside and/or outside the offset braces.
     | Allowed values: a subset of ``['inside', 'outside']``
     | Default value: ``['inside', 'outside']``


   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\Whitespace\\NoSpacesAroundOffsetFixer <./../src/Fixer/Whitespace/NoSpacesAroundOffsetFixer.php>`_
-  `no_spaces_inside_parenthesis <./rules/whitespace/no_spaces_inside_parenthesis.rst>`_

   There MUST NOT be a space after the opening parenthesis. There MUST NOT be a space before the closing parenthesis.

   Part of rule sets `@PER <./ruleSets/PER.rst>`_ `@PSR12 <./ruleSets/PSR12.rst>`_ `@PSR2 <./ruleSets/PSR2.rst>`_ `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\Whitespace\\NoSpacesInsideParenthesisFixer <./../src/Fixer/Whitespace/NoSpacesInsideParenthesisFixer.php>`_
-  `no_space_around_double_colon <./rules/operator/no_space_around_double_colon.rst>`_

   There must be no space around double colons (also called Scope Resolution Operator or Paamayim Nekudotayim).

   Part of rule sets `@PER <./ruleSets/PER.rst>`_ `@PSR12 <./ruleSets/PSR12.rst>`_ `@PSR2 <./ruleSets/PSR2.rst>`_ `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\Operator\\NoSpaceAroundDoubleColonFixer <./../src/Fixer/Operator/NoSpaceAroundDoubleColonFixer.php>`_
-  `no_superfluous_elseif <./rules/control_structure/no_superfluous_elseif.rst>`_

   Replaces superfluous ``elseif`` with ``if``.

   Part of rule set `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_

   `Source PhpCsFixer\\Fixer\\ControlStructure\\NoSuperfluousElseifFixer <./../src/Fixer/ControlStructure/NoSuperfluousElseifFixer.php>`_
-  `no_superfluous_phpdoc_tags <./rules/phpdoc/no_superfluous_phpdoc_tags.rst>`_

   Removes ``@param``, ``@return`` and ``@var`` tags that don't provide any useful information.

   Configuration options:

   - | ``allow_mixed``
     | Whether type `mixed` without description is allowed (`true`) or considered superfluous (`false`)
     | Allowed types: ``bool``
     | Default value: ``false``
   - | ``remove_inheritdoc``
     | Remove `@inheritDoc` tags
     | Allowed types: ``bool``
     | Default value: ``false``
   - | ``allow_unused_params``
     | Whether `param` annotation without actual signature is allowed (`true`) or considered superfluous (`false`)
     | Allowed types: ``bool``
     | Default value: ``false``


   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\Phpdoc\\NoSuperfluousPhpdocTagsFixer <./../src/Fixer/Phpdoc/NoSuperfluousPhpdocTagsFixer.php>`_
-  `no_trailing_comma_in_list_call <./rules/control_structure/no_trailing_comma_in_list_call.rst>`_

   Remove trailing commas in list function calls.

   *warning deprecated*   Use ``no_trailing_comma_in_singleline`` instead.

   `Source PhpCsFixer\\Fixer\\ControlStructure\\NoTrailingCommaInListCallFixer <./../src/Fixer/ControlStructure/NoTrailingCommaInListCallFixer.php>`_
-  `no_trailing_comma_in_singleline <./rules/basic/no_trailing_comma_in_singleline.rst>`_

   If a list of values separated by a comma is contained on a single line, then the last item MUST NOT have a trailing comma.

   Configuration options:

   - | ``elements``
     | Which elements to fix.
     | Allowed values: a subset of ``['arguments', 'array', 'array_destructuring', 'group_import']``
     | Default value: ``['arguments', 'array_destructuring', 'array', 'group_import']``


   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\Basic\\NoTrailingCommaInSinglelineFixer <./../src/Fixer/Basic/NoTrailingCommaInSinglelineFixer.php>`_
-  `no_trailing_comma_in_singleline_array <./rules/array_notation/no_trailing_comma_in_singleline_array.rst>`_

   PHP single-line arrays should not have trailing comma.

   *warning deprecated*   Use ``no_trailing_comma_in_singleline`` instead.

   `Source PhpCsFixer\\Fixer\\ArrayNotation\\NoTrailingCommaInSinglelineArrayFixer <./../src/Fixer/ArrayNotation/NoTrailingCommaInSinglelineArrayFixer.php>`_
-  `no_trailing_comma_in_singleline_function_call <./rules/function_notation/no_trailing_comma_in_singleline_function_call.rst>`_

   When making a method or function call on a single line there MUST NOT be a trailing comma after the last argument.

   *warning deprecated*   Use ``no_trailing_comma_in_singleline`` instead.

   `Source PhpCsFixer\\Fixer\\FunctionNotation\\NoTrailingCommaInSinglelineFunctionCallFixer <./../src/Fixer/FunctionNotation/NoTrailingCommaInSinglelineFunctionCallFixer.php>`_
-  `no_trailing_whitespace <./rules/whitespace/no_trailing_whitespace.rst>`_

   Remove trailing whitespace at the end of non-blank lines.

   Part of rule sets `@PER <./ruleSets/PER.rst>`_ `@PSR12 <./ruleSets/PSR12.rst>`_ `@PSR2 <./ruleSets/PSR2.rst>`_ `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\Whitespace\\NoTrailingWhitespaceFixer <./../src/Fixer/Whitespace/NoTrailingWhitespaceFixer.php>`_
-  `no_trailing_whitespace_in_comment <./rules/comment/no_trailing_whitespace_in_comment.rst>`_

   There MUST be no trailing spaces inside comment or PHPDoc.

   Part of rule sets `@PER <./ruleSets/PER.rst>`_ `@PSR12 <./ruleSets/PSR12.rst>`_ `@PSR2 <./ruleSets/PSR2.rst>`_ `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\Comment\\NoTrailingWhitespaceInCommentFixer <./../src/Fixer/Comment/NoTrailingWhitespaceInCommentFixer.php>`_
-  `no_trailing_whitespace_in_string <./rules/string_notation/no_trailing_whitespace_in_string.rst>`_

   There must be no trailing whitespace in strings.

   *warning risky* Changing the whitespaces in strings might affect string comparisons and
   outputs.

   Part of rule sets `@PER:risky <./ruleSets/PERRisky.rst>`_ `@PSR12:risky <./ruleSets/PSR12Risky.rst>`_ `@PhpCsFixer:risky <./ruleSets/PhpCsFixerRisky.rst>`_ `@Symfony:risky <./ruleSets/SymfonyRisky.rst>`_

   `Source PhpCsFixer\\Fixer\\StringNotation\\NoTrailingWhitespaceInStringFixer <./../src/Fixer/StringNotation/NoTrailingWhitespaceInStringFixer.php>`_
-  `no_unneeded_control_parentheses <./rules/control_structure/no_unneeded_control_parentheses.rst>`_

   Removes unneeded parentheses around control statements.

   Configuration options:

   - | ``statements``
     | List of control statements to fix.
     | Allowed values: a subset of ``['break', 'clone', 'continue', 'echo_print', 'negative_instanceof', 'others', 'return', 'switch_case', 'yield', 'yield_from']``
     | Default value: ``['break', 'clone', 'continue', 'echo_print', 'return', 'switch_case', 'yield']``


   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\ControlStructure\\NoUnneededControlParenthesesFixer <./../src/Fixer/ControlStructure/NoUnneededControlParenthesesFixer.php>`_
-  `no_unneeded_curly_braces <./rules/control_structure/no_unneeded_curly_braces.rst>`_

   Removes unneeded curly braces that are superfluous and aren't part of a control structure's body.

   Configuration options:

   - | ``namespaces``
     | Remove unneeded curly braces from bracketed namespaces.
     | Allowed types: ``bool``
     | Default value: ``false``


   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\ControlStructure\\NoUnneededCurlyBracesFixer <./../src/Fixer/ControlStructure/NoUnneededCurlyBracesFixer.php>`_
-  `no_unneeded_final_method <./rules/class_notation/no_unneeded_final_method.rst>`_

   Removes ``final`` from methods where possible.

   *warning risky* Risky when child class overrides a ``private`` method.

   Configuration options:

   - | ``private_methods``
     | Private methods of non-`final` classes must not be declared `final`.
     | Allowed types: ``bool``
     | Default value: ``true``


   Part of rule sets `@PHP80Migration:risky <./ruleSets/PHP80MigrationRisky.rst>`_ `@PhpCsFixer:risky <./ruleSets/PhpCsFixerRisky.rst>`_ `@Symfony:risky <./ruleSets/SymfonyRisky.rst>`_

   `Source PhpCsFixer\\Fixer\\ClassNotation\\NoUnneededFinalMethodFixer <./../src/Fixer/ClassNotation/NoUnneededFinalMethodFixer.php>`_
-  `no_unneeded_import_alias <./rules/import/no_unneeded_import_alias.rst>`_

   Imports should not be aliased as the same name.

   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\Import\\NoUnneededImportAliasFixer <./../src/Fixer/Import/NoUnneededImportAliasFixer.php>`_
-  `no_unreachable_default_argument_value <./rules/function_notation/no_unreachable_default_argument_value.rst>`_

   In function arguments there must not be arguments with default values before non-default ones.

   *warning risky* Modifies the signature of functions; therefore risky when using systems (such
   as some Symfony components) that rely on those (for example through
   reflection).

   Part of rule sets `@PER:risky <./ruleSets/PERRisky.rst>`_ `@PHP80Migration:risky <./ruleSets/PHP80MigrationRisky.rst>`_ `@PSR12:risky <./ruleSets/PSR12Risky.rst>`_ `@PhpCsFixer:risky <./ruleSets/PhpCsFixerRisky.rst>`_

   `Source PhpCsFixer\\Fixer\\FunctionNotation\\NoUnreachableDefaultArgumentValueFixer <./../src/Fixer/FunctionNotation/NoUnreachableDefaultArgumentValueFixer.php>`_
-  `no_unset_cast <./rules/cast_notation/no_unset_cast.rst>`_

   Variables must be set ``null`` instead of using ``(unset)`` casting.

   Part of rule sets `@PHP80Migration <./ruleSets/PHP80Migration.rst>`_ `@PHP81Migration <./ruleSets/PHP81Migration.rst>`_ `@PHP82Migration <./ruleSets/PHP82Migration.rst>`_ `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\CastNotation\\NoUnsetCastFixer <./../src/Fixer/CastNotation/NoUnsetCastFixer.php>`_
-  `no_unset_on_property <./rules/language_construct/no_unset_on_property.rst>`_

   Properties should be set to ``null`` instead of using ``unset``.

   *warning risky* Risky when relying on attributes to be removed using ``unset`` rather than be
   set to ``null``. Changing variables to ``null`` instead of unsetting means
   these still show up when looping over class variables and reference
   properties remain unbroken. With PHP 7.4, this rule might introduce ``null``
   assignments to properties whose type declaration does not allow it.

   Part of rule set `@PhpCsFixer:risky <./ruleSets/PhpCsFixerRisky.rst>`_

   `Source PhpCsFixer\\Fixer\\LanguageConstruct\\NoUnsetOnPropertyFixer <./../src/Fixer/LanguageConstruct/NoUnsetOnPropertyFixer.php>`_
-  `no_unused_imports <./rules/import/no_unused_imports.rst>`_

   Unused ``use`` statements must be removed.

   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\Import\\NoUnusedImportsFixer <./../src/Fixer/Import/NoUnusedImportsFixer.php>`_
-  `no_useless_concat_operator <./rules/operator/no_useless_concat_operator.rst>`_

   There should not be useless concat operations.

   Configuration options:

   - | ``juggle_simple_strings``
     | Allow for simple string quote juggling if it results in more concat-operations merges.
     | Allowed types: ``bool``
     | Default value: ``false``


   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\Operator\\NoUselessConcatOperatorFixer <./../src/Fixer/Operator/NoUselessConcatOperatorFixer.php>`_
-  `no_useless_else <./rules/control_structure/no_useless_else.rst>`_

   There should not be useless ``else`` cases.

   Part of rule set `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_

   `Source PhpCsFixer\\Fixer\\ControlStructure\\NoUselessElseFixer <./../src/Fixer/ControlStructure/NoUselessElseFixer.php>`_
-  `no_useless_nullsafe_operator <./rules/operator/no_useless_nullsafe_operator.rst>`_

   There should not be useless ``null-safe-operators`` ``?->`` used.

   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\Operator\\NoUselessNullsafeOperatorFixer <./../src/Fixer/Operator/NoUselessNullsafeOperatorFixer.php>`_
-  `no_useless_return <./rules/return_notation/no_useless_return.rst>`_

   There should not be an empty ``return`` statement at the end of a function.

   Part of rule set `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_

   `Source PhpCsFixer\\Fixer\\ReturnNotation\\NoUselessReturnFixer <./../src/Fixer/ReturnNotation/NoUselessReturnFixer.php>`_
-  `no_useless_sprintf <./rules/function_notation/no_useless_sprintf.rst>`_

   There must be no ``sprintf`` calls with only the first argument.

   *warning risky* Risky when if the ``sprintf`` function is overridden.

   Part of rule sets `@PhpCsFixer:risky <./ruleSets/PhpCsFixerRisky.rst>`_ `@Symfony:risky <./ruleSets/SymfonyRisky.rst>`_

   `Source PhpCsFixer\\Fixer\\FunctionNotation\\NoUselessSprintfFixer <./../src/Fixer/FunctionNotation/NoUselessSprintfFixer.php>`_
-  `no_whitespace_before_comma_in_array <./rules/array_notation/no_whitespace_before_comma_in_array.rst>`_

   In array declaration, there MUST NOT be a whitespace before each comma.

   Configuration options:

   - | ``after_heredoc``
     | Whether the whitespace between heredoc end and comma should be removed.
     | Allowed types: ``bool``
     | Default value: ``false``


   Part of rule sets `@PHP73Migration <./ruleSets/PHP73Migration.rst>`_ `@PHP74Migration <./ruleSets/PHP74Migration.rst>`_ `@PHP80Migration <./ruleSets/PHP80Migration.rst>`_ `@PHP81Migration <./ruleSets/PHP81Migration.rst>`_ `@PHP82Migration <./ruleSets/PHP82Migration.rst>`_ `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\ArrayNotation\\NoWhitespaceBeforeCommaInArrayFixer <./../src/Fixer/ArrayNotation/NoWhitespaceBeforeCommaInArrayFixer.php>`_
-  `no_whitespace_in_blank_line <./rules/whitespace/no_whitespace_in_blank_line.rst>`_

   Remove trailing whitespace at the end of blank lines.

   Part of rule sets `@PER <./ruleSets/PER.rst>`_ `@PSR12 <./ruleSets/PSR12.rst>`_ `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\Whitespace\\NoWhitespaceInBlankLineFixer <./../src/Fixer/Whitespace/NoWhitespaceInBlankLineFixer.php>`_
-  `nullable_type_declaration_for_default_null_value <./rules/function_notation/nullable_type_declaration_for_default_null_value.rst>`_

   Adds or removes ``?`` before type declarations for parameters with a default ``null`` value.

   Rule is applied only in a PHP 7.1+ environment.

   Configuration options:

   - | ``use_nullable_type_declaration``
     | Whether to add or remove `?` before type declarations for parameters with a default `null` value.
     | Allowed types: ``bool``
     | Default value: ``true``


   `Source PhpCsFixer\\Fixer\\FunctionNotation\\NullableTypeDeclarationForDefaultNullValueFixer <./../src/Fixer/FunctionNotation/NullableTypeDeclarationForDefaultNullValueFixer.php>`_
-  `object_operator_without_whitespace <./rules/operator/object_operator_without_whitespace.rst>`_

   There should not be space before or after object operators ``->`` and ``?->``.

   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\Operator\\ObjectOperatorWithoutWhitespaceFixer <./../src/Fixer/Operator/ObjectOperatorWithoutWhitespaceFixer.php>`_
-  `octal_notation <./rules/basic/octal_notation.rst>`_

   Literal octal must be in ``0o`` notation.

   Part of rule sets `@PHP81Migration <./ruleSets/PHP81Migration.rst>`_ `@PHP82Migration <./ruleSets/PHP82Migration.rst>`_

   `Source PhpCsFixer\\Fixer\\Basic\\OctalNotationFixer <./../src/Fixer/Basic/OctalNotationFixer.php>`_
-  `operator_linebreak <./rules/operator/operator_linebreak.rst>`_

   Operators - when multiline - must always be at the beginning or at the end of the line.

   Configuration options:

   - | ``only_booleans``
     | whether to limit operators to only boolean ones
     | Allowed types: ``bool``
     | Default value: ``false``
   - | ``position``
     | whether to place operators at the beginning or at the end of the line
     | Allowed values: ``'beginning'``, ``'end'``
     | Default value: ``'beginning'``


   Part of rule set `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_

   `Source PhpCsFixer\\Fixer\\Operator\\OperatorLinebreakFixer <./../src/Fixer/Operator/OperatorLinebreakFixer.php>`_
-  `ordered_class_elements <./rules/class_notation/ordered_class_elements.rst>`_

   Orders the elements of classes/interfaces/traits/enums.

   Configuration options:

   - | ``order``
     | List of strings defining order of elements.
     | Allowed values: a subset of ``['case', 'constant', 'constant_private', 'constant_protected', 'constant_public', 'construct', 'destruct', 'magic', 'method', 'method_abstract', 'method_private', 'method_private_abstract', 'method_private_abstract_static', 'method_private_static', 'method_protected', 'method_protected_abstract', 'method_protected_abstract_static', 'method_protected_static', 'method_public', 'method_public_abstract', 'method_public_abstract_static', 'method_public_static', 'method_static', 'phpunit', 'private', 'property', 'property_private', 'property_private_readonly', 'property_private_static', 'property_protected', 'property_protected_readonly', 'property_protected_static', 'property_public', 'property_public_readonly', 'property_public_static', 'property_static', 'protected', 'public', 'use_trait']``
     | Default value: ``['use_trait', 'case', 'constant_public', 'constant_protected', 'constant_private', 'property_public', 'property_protected', 'property_private', 'construct', 'destruct', 'magic', 'phpunit', 'method_public', 'method_protected', 'method_private']``
   - | ``sort_algorithm``
     | How multiple occurrences of same type statements should be sorted
     | Allowed values: ``'alpha'``, ``'none'``
     | Default value: ``'none'``


   Part of rule sets `@PER <./ruleSets/PER.rst>`_ `@PSR12 <./ruleSets/PSR12.rst>`_ `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\ClassNotation\\OrderedClassElementsFixer <./../src/Fixer/ClassNotation/OrderedClassElementsFixer.php>`_
-  `ordered_imports <./rules/import/ordered_imports.rst>`_

   Ordering ``use`` statements.

   Configuration options:

   - | ``sort_algorithm``
     | whether the statements should be sorted alphabetically or by length, or not sorted
     | Allowed values: ``'alpha'``, ``'length'``, ``'none'``
     | Default value: ``'alpha'``
   - | ``imports_order``
     | Defines the order of import types.
     | Allowed types: ``array``, ``null``
     | Default value: ``null``


   Part of rule sets `@PER <./ruleSets/PER.rst>`_ `@PSR12 <./ruleSets/PSR12.rst>`_ `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\Import\\OrderedImportsFixer <./../src/Fixer/Import/OrderedImportsFixer.php>`_
-  `ordered_interfaces <./rules/class_notation/ordered_interfaces.rst>`_

   Orders the interfaces in an ``implements`` or ``interface extends`` clause.

   *warning risky* Risky for ``implements`` when specifying both an interface and its parent
   interface, because PHP doesn't break on ``parent, child`` but does on
   ``child, parent``.

   Configuration options:

   - | ``order``
     | How the interfaces should be ordered
     | Allowed values: ``'alpha'``, ``'length'``
     | Default value: ``'alpha'``
   - | ``direction``
     | Which direction the interfaces should be ordered
     | Allowed values: ``'ascend'``, ``'descend'``
     | Default value: ``'ascend'``


   `Source PhpCsFixer\\Fixer\\ClassNotation\\OrderedInterfacesFixer <./../src/Fixer/ClassNotation/OrderedInterfacesFixer.php>`_
-  `ordered_traits <./rules/class_notation/ordered_traits.rst>`_

   Trait ``use`` statements must be sorted alphabetically.

   *warning risky* Risky when depending on order of the imports.

   Part of rule sets `@PhpCsFixer:risky <./ruleSets/PhpCsFixerRisky.rst>`_ `@Symfony:risky <./ruleSets/SymfonyRisky.rst>`_

   `Source PhpCsFixer\\Fixer\\ClassNotation\\OrderedTraitsFixer <./../src/Fixer/ClassNotation/OrderedTraitsFixer.php>`_
-  `phpdoc_add_missing_param_annotation <./rules/phpdoc/phpdoc_add_missing_param_annotation.rst>`_

   PHPDoc should contain ``@param`` for all params.

   Configuration options:

   - | ``only_untyped``
     | Whether to add missing `@param` annotations for untyped parameters only.
     | Allowed types: ``bool``
     | Default value: ``true``


   Part of rule set `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_

   `Source PhpCsFixer\\Fixer\\Phpdoc\\PhpdocAddMissingParamAnnotationFixer <./../src/Fixer/Phpdoc/PhpdocAddMissingParamAnnotationFixer.php>`_
-  `phpdoc_align <./rules/phpdoc/phpdoc_align.rst>`_

   All items of the given phpdoc tags must be either left-aligned or (by default) aligned vertically.

   Configuration options:

   - | ``tags``
     | The tags that should be aligned.
     | Allowed values: a subset of ``['method', 'param', 'property', 'property-read', 'property-write', 'return', 'throws', 'type', 'var']``
     | Default value: ``['method', 'param', 'property', 'return', 'throws', 'type', 'var']``
   - | ``align``
     | Align comments
     | Allowed values: ``'left'``, ``'vertical'``
     | Default value: ``'vertical'``


   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\Phpdoc\\PhpdocAlignFixer <./../src/Fixer/Phpdoc/PhpdocAlignFixer.php>`_
-  `phpdoc_annotation_without_dot <./rules/phpdoc/phpdoc_annotation_without_dot.rst>`_

   PHPDoc annotation descriptions should not be a sentence.

   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\Phpdoc\\PhpdocAnnotationWithoutDotFixer <./../src/Fixer/Phpdoc/PhpdocAnnotationWithoutDotFixer.php>`_
-  `phpdoc_indent <./rules/phpdoc/phpdoc_indent.rst>`_

   Docblocks should have the same indentation as the documented subject.

   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\Phpdoc\\PhpdocIndentFixer <./../src/Fixer/Phpdoc/PhpdocIndentFixer.php>`_
-  `phpdoc_inline_tag_normalizer <./rules/phpdoc/phpdoc_inline_tag_normalizer.rst>`_

   Fixes PHPDoc inline tags.

   Configuration options:

   - | ``tags``
     | The list of tags to normalize
     | Allowed types: ``array``
     | Default value: ``['example', 'id', 'internal', 'inheritdoc', 'inheritdocs', 'link', 'source', 'toc', 'tutorial']``


   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\Phpdoc\\PhpdocInlineTagNormalizerFixer <./../src/Fixer/Phpdoc/PhpdocInlineTagNormalizerFixer.php>`_
-  `phpdoc_line_span <./rules/phpdoc/phpdoc_line_span.rst>`_

   Changes doc blocks from single to multi line, or reversed. Works for class constants, properties and methods only.

   Configuration options:

   - | ``const``
     | Whether const blocks should be single or multi line
     | Allowed values: ``'multi'``, ``'single'``, ``null``
     | Default value: ``'multi'``
   - | ``property``
     | Whether property doc blocks should be single or multi line
     | Allowed values: ``'multi'``, ``'single'``, ``null``
     | Default value: ``'multi'``
   - | ``method``
     | Whether method doc blocks should be single or multi line
     | Allowed values: ``'multi'``, ``'single'``, ``null``
     | Default value: ``'multi'``


   `Source PhpCsFixer\\Fixer\\Phpdoc\\PhpdocLineSpanFixer <./../src/Fixer/Phpdoc/PhpdocLineSpanFixer.php>`_
-  `phpdoc_no_access <./rules/phpdoc/phpdoc_no_access.rst>`_

   ``@access`` annotations should be omitted from PHPDoc.

   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\Phpdoc\\PhpdocNoAccessFixer <./../src/Fixer/Phpdoc/PhpdocNoAccessFixer.php>`_
-  `phpdoc_no_alias_tag <./rules/phpdoc/phpdoc_no_alias_tag.rst>`_

   No alias PHPDoc tags should be used.

   Configuration options:

   - | ``replacements``
     | Mapping between replaced annotations with new ones.
     | Allowed types: ``array``
     | Default value: ``['property-read' => 'property', 'property-write' => 'property', 'type' => 'var', 'link' => 'see']``


   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\Phpdoc\\PhpdocNoAliasTagFixer <./../src/Fixer/Phpdoc/PhpdocNoAliasTagFixer.php>`_
-  `phpdoc_no_empty_return <./rules/phpdoc/phpdoc_no_empty_return.rst>`_

   ``@return void`` and ``@return null`` annotations should be omitted from PHPDoc.

   Part of rule set `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_

   `Source PhpCsFixer\\Fixer\\Phpdoc\\PhpdocNoEmptyReturnFixer <./../src/Fixer/Phpdoc/PhpdocNoEmptyReturnFixer.php>`_
-  `phpdoc_no_package <./rules/phpdoc/phpdoc_no_package.rst>`_

   ``@package`` and ``@subpackage`` annotations should be omitted from PHPDoc.

   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\Phpdoc\\PhpdocNoPackageFixer <./../src/Fixer/Phpdoc/PhpdocNoPackageFixer.php>`_
-  `phpdoc_no_useless_inheritdoc <./rules/phpdoc/phpdoc_no_useless_inheritdoc.rst>`_

   Classy that does not inherit must not have ``@inheritdoc`` tags.

   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\Phpdoc\\PhpdocNoUselessInheritdocFixer <./../src/Fixer/Phpdoc/PhpdocNoUselessInheritdocFixer.php>`_
-  `phpdoc_order <./rules/phpdoc/phpdoc_order.rst>`_

   Annotations in PHPDoc should be ordered in defined sequence.

   Configuration options:

   - | ``order``
     | Sequence in which annotations in PHPDoc should be ordered.
     | Allowed types: ``string[]``
     | Default value: ``['param', 'throws', 'return']``


   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\Phpdoc\\PhpdocOrderFixer <./../src/Fixer/Phpdoc/PhpdocOrderFixer.php>`_
-  `phpdoc_order_by_value <./rules/phpdoc/phpdoc_order_by_value.rst>`_

   Order phpdoc tags by value.

   Configuration options:

   - | ``annotations``
     | List of annotations to order, e.g. `["covers"]`.
     | Allowed values: a subset of ``['author', 'covers', 'coversNothing', 'dataProvider', 'depends', 'group', 'internal', 'method', 'mixin', 'property', 'property-read', 'property-write', 'requires', 'throws', 'uses']``
     | Default value: ``['covers']``


   Part of rule set `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_

   `Source PhpCsFixer\\Fixer\\Phpdoc\\PhpdocOrderByValueFixer <./../src/Fixer/Phpdoc/PhpdocOrderByValueFixer.php>`_
-  `phpdoc_return_self_reference <./rules/phpdoc/phpdoc_return_self_reference.rst>`_

   The type of ``@return`` annotations of methods returning a reference to itself must the configured one.

   Configuration options:

   - | ``replacements``
     | Mapping between replaced return types with new ones.
     | Allowed types: ``array``
     | Default value: ``['this' => '$this', '@this' => '$this', '$self' => 'self', '@self' => 'self', '$static' => 'static', '@static' => 'static']``


   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\Phpdoc\\PhpdocReturnSelfReferenceFixer <./../src/Fixer/Phpdoc/PhpdocReturnSelfReferenceFixer.php>`_
-  `phpdoc_scalar <./rules/phpdoc/phpdoc_scalar.rst>`_

   Scalar types should always be written in the same form. ``int`` not ``integer``, ``bool`` not ``boolean``, ``float`` not ``real`` or ``double``.

   Configuration options:

   - | ``types``
     | A list of types to fix.
     | Allowed values: a subset of ``['boolean', 'callback', 'double', 'integer', 'real', 'str']``
     | Default value: ``['boolean', 'callback', 'double', 'integer', 'real', 'str']``


   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\Phpdoc\\PhpdocScalarFixer <./../src/Fixer/Phpdoc/PhpdocScalarFixer.php>`_
-  `phpdoc_separation <./rules/phpdoc/phpdoc_separation.rst>`_

   Annotations in PHPDoc should be grouped together so that annotations of the same type immediately follow each other. Annotations of a different type are separated by a single blank line.

   Configuration options:

   - | ``groups``
     | Sets of annotation types to be grouped together.
     | Allowed types: ``string[][]``
     | Default value: ``[['deprecated', 'link', 'see', 'since'], ['author', 'copyright', 'license'], ['category', 'package', 'subpackage'], ['property', 'property-read', 'property-write']]``


   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\Phpdoc\\PhpdocSeparationFixer <./../src/Fixer/Phpdoc/PhpdocSeparationFixer.php>`_
-  `phpdoc_single_line_var_spacing <./rules/phpdoc/phpdoc_single_line_var_spacing.rst>`_

   Single line ``@var`` PHPDoc should have proper spacing.

   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\Phpdoc\\PhpdocSingleLineVarSpacingFixer <./../src/Fixer/Phpdoc/PhpdocSingleLineVarSpacingFixer.php>`_
-  `phpdoc_summary <./rules/phpdoc/phpdoc_summary.rst>`_

   PHPDoc summary should end in either a full stop, exclamation mark, or question mark.

   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\Phpdoc\\PhpdocSummaryFixer <./../src/Fixer/Phpdoc/PhpdocSummaryFixer.php>`_
-  `phpdoc_tag_casing <./rules/phpdoc/phpdoc_tag_casing.rst>`_

   Fixes casing of PHPDoc tags.

   Configuration options:

   - | ``tags``
     | List of tags to fix with their expected casing.
     | Allowed types: ``array``
     | Default value: ``['inheritDoc']``


   `Source PhpCsFixer\\Fixer\\Phpdoc\\PhpdocTagCasingFixer <./../src/Fixer/Phpdoc/PhpdocTagCasingFixer.php>`_
-  `phpdoc_tag_type <./rules/phpdoc/phpdoc_tag_type.rst>`_

   Forces PHPDoc tags to be either regular annotations or inline.

   Configuration options:

   - | ``tags``
     | The list of tags to fix
     | Allowed types: ``array``
     | Default value: ``['api' => 'annotation', 'author' => 'annotation', 'copyright' => 'annotation', 'deprecated' => 'annotation', 'example' => 'annotation', 'global' => 'annotation', 'inheritDoc' => 'annotation', 'internal' => 'annotation', 'license' => 'annotation', 'method' => 'annotation', 'package' => 'annotation', 'param' => 'annotation', 'property' => 'annotation', 'return' => 'annotation', 'see' => 'annotation', 'since' => 'annotation', 'throws' => 'annotation', 'todo' => 'annotation', 'uses' => 'annotation', 'var' => 'annotation', 'version' => 'annotation']``


   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\Phpdoc\\PhpdocTagTypeFixer <./../src/Fixer/Phpdoc/PhpdocTagTypeFixer.php>`_
-  `phpdoc_to_comment <./rules/phpdoc/phpdoc_to_comment.rst>`_

   Docblocks should only be used on structural elements.

   Configuration options:

   - | ``ignored_tags``
     | List of ignored tags (matched case insensitively)
     | Allowed types: ``array``
     | Default value: ``[]``


   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\Phpdoc\\PhpdocToCommentFixer <./../src/Fixer/Phpdoc/PhpdocToCommentFixer.php>`_
-  `phpdoc_to_param_type <./rules/function_notation/phpdoc_to_param_type.rst>`_

   EXPERIMENTAL: Takes ``@param`` annotations of non-mixed types and adjusts accordingly the function signature. Requires PHP >= 7.0.

   *warning risky* This rule is EXPERIMENTAL and [1] is not covered with backward compatibility
   promise. [2] ``@param`` annotation is mandatory for the fixer to make
   changes, signatures of methods without it (no docblock, inheritdocs) will not
   be fixed. [3] Manual actions are required if inherited signatures are not
   properly documented.

   Configuration options:

   - | ``scalar_types``
     | Fix also scalar types; may have unexpected behaviour due to PHP bad type coercion system.
     | Allowed types: ``bool``
     | Default value: ``true``


   `Source PhpCsFixer\\Fixer\\FunctionNotation\\PhpdocToParamTypeFixer <./../src/Fixer/FunctionNotation/PhpdocToParamTypeFixer.php>`_
-  `phpdoc_to_property_type <./rules/function_notation/phpdoc_to_property_type.rst>`_

   EXPERIMENTAL: Takes ``@var`` annotation of non-mixed types and adjusts accordingly the property signature. Requires PHP >= 7.4.

   *warning risky* This rule is EXPERIMENTAL and [1] is not covered with backward compatibility
   promise. [2] ``@var`` annotation is mandatory for the fixer to make changes,
   signatures of properties without it (no docblock) will not be fixed. [3]
   Manual actions might be required for newly typed properties that are read
   before initialization.

   Configuration options:

   - | ``scalar_types``
     | Fix also scalar types; may have unexpected behaviour due to PHP bad type coercion system.
     | Allowed types: ``bool``
     | Default value: ``true``


   `Source PhpCsFixer\\Fixer\\FunctionNotation\\PhpdocToPropertyTypeFixer <./../src/Fixer/FunctionNotation/PhpdocToPropertyTypeFixer.php>`_
-  `phpdoc_to_return_type <./rules/function_notation/phpdoc_to_return_type.rst>`_

   EXPERIMENTAL: Takes ``@return`` annotation of non-mixed types and adjusts accordingly the function signature. Requires PHP >= 7.0.

   *warning risky* This rule is EXPERIMENTAL and [1] is not covered with backward compatibility
   promise. [2] ``@return`` annotation is mandatory for the fixer to make
   changes, signatures of methods without it (no docblock, inheritdocs) will not
   be fixed. [3] Manual actions are required if inherited signatures are not
   properly documented.

   Configuration options:

   - | ``scalar_types``
     | Fix also scalar types; may have unexpected behaviour due to PHP bad type coercion system.
     | Allowed types: ``bool``
     | Default value: ``true``


   `Source PhpCsFixer\\Fixer\\FunctionNotation\\PhpdocToReturnTypeFixer <./../src/Fixer/FunctionNotation/PhpdocToReturnTypeFixer.php>`_
-  `phpdoc_trim <./rules/phpdoc/phpdoc_trim.rst>`_

   PHPDoc should start and end with content, excluding the very first and last line of the docblocks.

   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\Phpdoc\\PhpdocTrimFixer <./../src/Fixer/Phpdoc/PhpdocTrimFixer.php>`_
-  `phpdoc_trim_consecutive_blank_line_separation <./rules/phpdoc/phpdoc_trim_consecutive_blank_line_separation.rst>`_

   Removes extra blank lines after summary and after description in PHPDoc.

   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\Phpdoc\\PhpdocTrimConsecutiveBlankLineSeparationFixer <./../src/Fixer/Phpdoc/PhpdocTrimConsecutiveBlankLineSeparationFixer.php>`_
-  `phpdoc_types <./rules/phpdoc/phpdoc_types.rst>`_

   The correct case must be used for standard PHP types in PHPDoc.

   Configuration options:

   - | ``groups``
     | Type groups to fix.
     | Allowed values: a subset of ``['alias', 'meta', 'simple']``
     | Default value: ``['simple', 'alias', 'meta']``


   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\Phpdoc\\PhpdocTypesFixer <./../src/Fixer/Phpdoc/PhpdocTypesFixer.php>`_
-  `phpdoc_types_order <./rules/phpdoc/phpdoc_types_order.rst>`_

   Sorts PHPDoc types.

   Configuration options:

   - | ``sort_algorithm``
     | The sorting algorithm to apply.
     | Allowed values: ``'alpha'``, ``'none'``
     | Default value: ``'alpha'``
   - | ``null_adjustment``
     | Forces the position of `null` (overrides `sort_algorithm`).
     | Allowed values: ``'always_first'``, ``'always_last'``, ``'none'``
     | Default value: ``'always_first'``


   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\Phpdoc\\PhpdocTypesOrderFixer <./../src/Fixer/Phpdoc/PhpdocTypesOrderFixer.php>`_
-  `phpdoc_var_annotation_correct_order <./rules/phpdoc/phpdoc_var_annotation_correct_order.rst>`_

   ``@var`` and ``@type`` annotations must have type and name in the correct order.

   Part of rule set `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_

   `Source PhpCsFixer\\Fixer\\Phpdoc\\PhpdocVarAnnotationCorrectOrderFixer <./../src/Fixer/Phpdoc/PhpdocVarAnnotationCorrectOrderFixer.php>`_
-  `phpdoc_var_without_name <./rules/phpdoc/phpdoc_var_without_name.rst>`_

   ``@var`` and ``@type`` annotations of classy properties should not contain the name.

   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\Phpdoc\\PhpdocVarWithoutNameFixer <./../src/Fixer/Phpdoc/PhpdocVarWithoutNameFixer.php>`_
-  `php_unit_construct <./rules/php_unit/php_unit_construct.rst>`_

   PHPUnit assertion method calls like ``->assertSame(true, $foo)`` should be written with dedicated method like ``->assertTrue($foo)``.

   *warning risky* Fixer could be risky if one is overriding PHPUnit's native methods.

   Configuration options:

   - | ``assertions``
     | List of assertion methods to fix.
     | Allowed values: a subset of ``['assertEquals', 'assertNotEquals', 'assertNotSame', 'assertSame']``
     | Default value: ``['assertEquals', 'assertSame', 'assertNotEquals', 'assertNotSame']``


   Part of rule sets `@PhpCsFixer:risky <./ruleSets/PhpCsFixerRisky.rst>`_ `@Symfony:risky <./ruleSets/SymfonyRisky.rst>`_

   `Source PhpCsFixer\\Fixer\\PhpUnit\\PhpUnitConstructFixer <./../src/Fixer/PhpUnit/PhpUnitConstructFixer.php>`_
-  `php_unit_dedicate_assert <./rules/php_unit/php_unit_dedicate_assert.rst>`_

   PHPUnit assertions like ``assertInternalType``, ``assertFileExists``, should be used over ``assertTrue``.

   *warning risky* Fixer could be risky if one is overriding PHPUnit's native methods.

   Configuration options:

   - | ``target``
     | Target version of PHPUnit.
     | Allowed values: ``'3.0'``, ``'3.5'``, ``'5.0'``, ``'5.6'``, ``'newest'``
     | Default value: ``'newest'``


   Part of rule sets `@PHPUnit30Migration:risky <./ruleSets/PHPUnit30MigrationRisky.rst>`_ `@PHPUnit32Migration:risky <./ruleSets/PHPUnit32MigrationRisky.rst>`_ `@PHPUnit35Migration:risky <./ruleSets/PHPUnit35MigrationRisky.rst>`_ `@PHPUnit43Migration:risky <./ruleSets/PHPUnit43MigrationRisky.rst>`_ `@PHPUnit48Migration:risky <./ruleSets/PHPUnit48MigrationRisky.rst>`_ `@PHPUnit50Migration:risky <./ruleSets/PHPUnit50MigrationRisky.rst>`_ `@PHPUnit52Migration:risky <./ruleSets/PHPUnit52MigrationRisky.rst>`_ `@PHPUnit54Migration:risky <./ruleSets/PHPUnit54MigrationRisky.rst>`_ `@PHPUnit55Migration:risky <./ruleSets/PHPUnit55MigrationRisky.rst>`_ `@PHPUnit56Migration:risky <./ruleSets/PHPUnit56MigrationRisky.rst>`_ `@PHPUnit57Migration:risky <./ruleSets/PHPUnit57MigrationRisky.rst>`_ `@PHPUnit60Migration:risky <./ruleSets/PHPUnit60MigrationRisky.rst>`_ `@PHPUnit75Migration:risky <./ruleSets/PHPUnit75MigrationRisky.rst>`_ `@PHPUnit84Migration:risky <./ruleSets/PHPUnit84MigrationRisky.rst>`_

   `Source PhpCsFixer\\Fixer\\PhpUnit\\PhpUnitDedicateAssertFixer <./../src/Fixer/PhpUnit/PhpUnitDedicateAssertFixer.php>`_
-  `php_unit_dedicate_assert_internal_type <./rules/php_unit/php_unit_dedicate_assert_internal_type.rst>`_

   PHPUnit assertions like ``assertIsArray`` should be used over ``assertInternalType``.

   *warning risky* Risky when PHPUnit methods are overridden or when project has PHPUnit
   incompatibilities.

   Configuration options:

   - | ``target``
     | Target version of PHPUnit.
     | Allowed values: ``'7.5'``, ``'newest'``
     | Default value: ``'newest'``


   Part of rule sets `@PHPUnit75Migration:risky <./ruleSets/PHPUnit75MigrationRisky.rst>`_ `@PHPUnit84Migration:risky <./ruleSets/PHPUnit84MigrationRisky.rst>`_

   `Source PhpCsFixer\\Fixer\\PhpUnit\\PhpUnitDedicateAssertInternalTypeFixer <./../src/Fixer/PhpUnit/PhpUnitDedicateAssertInternalTypeFixer.php>`_
-  `php_unit_expectation <./rules/php_unit/php_unit_expectation.rst>`_

   Usages of ``->setExpectedException*`` methods MUST be replaced by ``->expectException*`` methods.

   *warning risky* Risky when PHPUnit classes are overridden or not accessible, or when project
   has PHPUnit incompatibilities.

   Configuration options:

   - | ``target``
     | Target version of PHPUnit.
     | Allowed values: ``'5.2'``, ``'5.6'``, ``'8.4'``, ``'newest'``
     | Default value: ``'newest'``


   Part of rule sets `@PHPUnit52Migration:risky <./ruleSets/PHPUnit52MigrationRisky.rst>`_ `@PHPUnit54Migration:risky <./ruleSets/PHPUnit54MigrationRisky.rst>`_ `@PHPUnit55Migration:risky <./ruleSets/PHPUnit55MigrationRisky.rst>`_ `@PHPUnit56Migration:risky <./ruleSets/PHPUnit56MigrationRisky.rst>`_ `@PHPUnit57Migration:risky <./ruleSets/PHPUnit57MigrationRisky.rst>`_ `@PHPUnit60Migration:risky <./ruleSets/PHPUnit60MigrationRisky.rst>`_ `@PHPUnit75Migration:risky <./ruleSets/PHPUnit75MigrationRisky.rst>`_ `@PHPUnit84Migration:risky <./ruleSets/PHPUnit84MigrationRisky.rst>`_

   `Source PhpCsFixer\\Fixer\\PhpUnit\\PhpUnitExpectationFixer <./../src/Fixer/PhpUnit/PhpUnitExpectationFixer.php>`_
-  `php_unit_fqcn_annotation <./rules/php_unit/php_unit_fqcn_annotation.rst>`_

   PHPUnit annotations should be a FQCNs including a root namespace.

   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\PhpUnit\\PhpUnitFqcnAnnotationFixer <./../src/Fixer/PhpUnit/PhpUnitFqcnAnnotationFixer.php>`_
-  `php_unit_internal_class <./rules/php_unit/php_unit_internal_class.rst>`_

   All PHPUnit test classes should be marked as internal.

   Configuration options:

   - | ``types``
     | What types of classes to mark as internal
     | Allowed values: a subset of ``['abstract', 'final', 'normal']``
     | Default value: ``['normal', 'final']``


   Part of rule set `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_

   `Source PhpCsFixer\\Fixer\\PhpUnit\\PhpUnitInternalClassFixer <./../src/Fixer/PhpUnit/PhpUnitInternalClassFixer.php>`_
-  `php_unit_method_casing <./rules/php_unit/php_unit_method_casing.rst>`_

   Enforce camel (or snake) case for PHPUnit test methods, following configuration.

   Configuration options:

   - | ``case``
     | Apply camel or snake case to test methods
     | Allowed values: ``'camel_case'``, ``'snake_case'``
     | Default value: ``'camel_case'``


   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\PhpUnit\\PhpUnitMethodCasingFixer <./../src/Fixer/PhpUnit/PhpUnitMethodCasingFixer.php>`_
-  `php_unit_mock <./rules/php_unit/php_unit_mock.rst>`_

   Usages of ``->getMock`` and ``->getMockWithoutInvokingTheOriginalConstructor`` methods MUST be replaced by ``->createMock`` or ``->createPartialMock`` methods.

   *warning risky* Risky when PHPUnit classes are overridden or not accessible, or when project
   has PHPUnit incompatibilities.

   Configuration options:

   - | ``target``
     | Target version of PHPUnit.
     | Allowed values: ``'5.4'``, ``'5.5'``, ``'newest'``
     | Default value: ``'newest'``


   Part of rule sets `@PHPUnit54Migration:risky <./ruleSets/PHPUnit54MigrationRisky.rst>`_ `@PHPUnit55Migration:risky <./ruleSets/PHPUnit55MigrationRisky.rst>`_ `@PHPUnit56Migration:risky <./ruleSets/PHPUnit56MigrationRisky.rst>`_ `@PHPUnit57Migration:risky <./ruleSets/PHPUnit57MigrationRisky.rst>`_ `@PHPUnit60Migration:risky <./ruleSets/PHPUnit60MigrationRisky.rst>`_ `@PHPUnit75Migration:risky <./ruleSets/PHPUnit75MigrationRisky.rst>`_ `@PHPUnit84Migration:risky <./ruleSets/PHPUnit84MigrationRisky.rst>`_

   `Source PhpCsFixer\\Fixer\\PhpUnit\\PhpUnitMockFixer <./../src/Fixer/PhpUnit/PhpUnitMockFixer.php>`_
-  `php_unit_mock_short_will_return <./rules/php_unit/php_unit_mock_short_will_return.rst>`_

   Usage of PHPUnit's mock e.g. ``->will($this->returnValue(..))`` must be replaced by its shorter equivalent such as ``->willReturn(...)``.

   *warning risky* Risky when PHPUnit classes are overridden or not accessible, or when project
   has PHPUnit incompatibilities.

   Part of rule sets `@PhpCsFixer:risky <./ruleSets/PhpCsFixerRisky.rst>`_ `@Symfony:risky <./ruleSets/SymfonyRisky.rst>`_

   `Source PhpCsFixer\\Fixer\\PhpUnit\\PhpUnitMockShortWillReturnFixer <./../src/Fixer/PhpUnit/PhpUnitMockShortWillReturnFixer.php>`_
-  `php_unit_namespaced <./rules/php_unit/php_unit_namespaced.rst>`_

   PHPUnit classes MUST be used in namespaced version, e.g. ``\PHPUnit\Framework\TestCase`` instead of ``\PHPUnit_Framework_TestCase``.

   PHPUnit v6 has finally fully switched to namespaces.
   You could start preparing the upgrade by switching from non-namespaced
   TestCase to namespaced one.
   Forward compatibility layer (``\PHPUnit\Framework\TestCase`` class) was
   backported to PHPUnit v4.8.35 and PHPUnit v5.4.0.
   Extended forward compatibility layer (``PHPUnit\Framework\Assert``,
   ``PHPUnit\Framework\BaseTestListener``, ``PHPUnit\Framework\TestListener``
   classes) was introduced in v5.7.0.


   *warning risky* Risky when PHPUnit classes are overridden or not accessible, or when project
   has PHPUnit incompatibilities.

   Configuration options:

   - | ``target``
     | Target version of PHPUnit.
     | Allowed values: ``'4.8'``, ``'5.7'``, ``'6.0'``, ``'newest'``
     | Default value: ``'newest'``


   Part of rule sets `@PHPUnit48Migration:risky <./ruleSets/PHPUnit48MigrationRisky.rst>`_ `@PHPUnit50Migration:risky <./ruleSets/PHPUnit50MigrationRisky.rst>`_ `@PHPUnit52Migration:risky <./ruleSets/PHPUnit52MigrationRisky.rst>`_ `@PHPUnit54Migration:risky <./ruleSets/PHPUnit54MigrationRisky.rst>`_ `@PHPUnit55Migration:risky <./ruleSets/PHPUnit55MigrationRisky.rst>`_ `@PHPUnit56Migration:risky <./ruleSets/PHPUnit56MigrationRisky.rst>`_ `@PHPUnit57Migration:risky <./ruleSets/PHPUnit57MigrationRisky.rst>`_ `@PHPUnit60Migration:risky <./ruleSets/PHPUnit60MigrationRisky.rst>`_ `@PHPUnit75Migration:risky <./ruleSets/PHPUnit75MigrationRisky.rst>`_ `@PHPUnit84Migration:risky <./ruleSets/PHPUnit84MigrationRisky.rst>`_

   `Source PhpCsFixer\\Fixer\\PhpUnit\\PhpUnitNamespacedFixer <./../src/Fixer/PhpUnit/PhpUnitNamespacedFixer.php>`_
-  `php_unit_no_expectation_annotation <./rules/php_unit/php_unit_no_expectation_annotation.rst>`_

   Usages of ``@expectedException*`` annotations MUST be replaced by ``->setExpectedException*`` methods.

   *warning risky* Risky when PHPUnit classes are overridden or not accessible, or when project
   has PHPUnit incompatibilities.

   Configuration options:

   - | ``target``
     | Target version of PHPUnit.
     | Allowed values: ``'3.2'``, ``'4.3'``, ``'newest'``
     | Default value: ``'newest'``
   - | ``use_class_const``
     | Use ::class notation.
     | Allowed types: ``bool``
     | Default value: ``true``


   Part of rule sets `@PHPUnit32Migration:risky <./ruleSets/PHPUnit32MigrationRisky.rst>`_ `@PHPUnit35Migration:risky <./ruleSets/PHPUnit35MigrationRisky.rst>`_ `@PHPUnit43Migration:risky <./ruleSets/PHPUnit43MigrationRisky.rst>`_ `@PHPUnit48Migration:risky <./ruleSets/PHPUnit48MigrationRisky.rst>`_ `@PHPUnit50Migration:risky <./ruleSets/PHPUnit50MigrationRisky.rst>`_ `@PHPUnit52Migration:risky <./ruleSets/PHPUnit52MigrationRisky.rst>`_ `@PHPUnit54Migration:risky <./ruleSets/PHPUnit54MigrationRisky.rst>`_ `@PHPUnit55Migration:risky <./ruleSets/PHPUnit55MigrationRisky.rst>`_ `@PHPUnit56Migration:risky <./ruleSets/PHPUnit56MigrationRisky.rst>`_ `@PHPUnit57Migration:risky <./ruleSets/PHPUnit57MigrationRisky.rst>`_ `@PHPUnit60Migration:risky <./ruleSets/PHPUnit60MigrationRisky.rst>`_ `@PHPUnit75Migration:risky <./ruleSets/PHPUnit75MigrationRisky.rst>`_ `@PHPUnit84Migration:risky <./ruleSets/PHPUnit84MigrationRisky.rst>`_

   `Source PhpCsFixer\\Fixer\\PhpUnit\\PhpUnitNoExpectationAnnotationFixer <./../src/Fixer/PhpUnit/PhpUnitNoExpectationAnnotationFixer.php>`_
-  `php_unit_set_up_tear_down_visibility <./rules/php_unit/php_unit_set_up_tear_down_visibility.rst>`_

   Changes the visibility of the ``setUp()`` and ``tearDown()`` functions of PHPUnit to ``protected``, to match the PHPUnit TestCase.

   *warning risky* This fixer may change functions named ``setUp()`` or ``tearDown()`` outside
   of PHPUnit tests, when a class is wrongly seen as a PHPUnit test.

   Part of rule sets `@PhpCsFixer:risky <./ruleSets/PhpCsFixerRisky.rst>`_ `@Symfony:risky <./ruleSets/SymfonyRisky.rst>`_

   `Source PhpCsFixer\\Fixer\\PhpUnit\\PhpUnitSetUpTearDownVisibilityFixer <./../src/Fixer/PhpUnit/PhpUnitSetUpTearDownVisibilityFixer.php>`_
-  `php_unit_size_class <./rules/php_unit/php_unit_size_class.rst>`_

   All PHPUnit test cases should have ``@small``, ``@medium`` or ``@large`` annotation to enable run time limits.

   The special groups [small, medium, large] provides a way to identify tests
   that are taking long to be executed.

   Configuration options:

   - | ``group``
     | Define a specific group to be used in case no group is already in use
     | Allowed values: ``'large'``, ``'medium'``, ``'small'``
     | Default value: ``'small'``


   `Source PhpCsFixer\\Fixer\\PhpUnit\\PhpUnitSizeClassFixer <./../src/Fixer/PhpUnit/PhpUnitSizeClassFixer.php>`_
-  `php_unit_strict <./rules/php_unit/php_unit_strict.rst>`_

   PHPUnit methods like ``assertSame`` should be used instead of ``assertEquals``.

   *warning risky* Risky when any of the functions are overridden or when testing object
   equality.

   Configuration options:

   - | ``assertions``
     | List of assertion methods to fix.
     | Allowed values: a subset of ``['assertAttributeEquals', 'assertAttributeNotEquals', 'assertEquals', 'assertNotEquals']``
     | Default value: ``['assertAttributeEquals', 'assertAttributeNotEquals', 'assertEquals', 'assertNotEquals']``


   Part of rule set `@PhpCsFixer:risky <./ruleSets/PhpCsFixerRisky.rst>`_

   `Source PhpCsFixer\\Fixer\\PhpUnit\\PhpUnitStrictFixer <./../src/Fixer/PhpUnit/PhpUnitStrictFixer.php>`_
-  `php_unit_test_annotation <./rules/php_unit/php_unit_test_annotation.rst>`_

   Adds or removes @test annotations from tests, following configuration.

   *warning risky* This fixer may change the name of your tests, and could cause incompatibility
   with abstract classes or interfaces.

   Configuration options:

   - | ``style``
     | Whether to use the @test annotation or not.
     | Allowed values: ``'annotation'``, ``'prefix'``
     | Default value: ``'prefix'``


   Part of rule sets `@PhpCsFixer:risky <./ruleSets/PhpCsFixerRisky.rst>`_ `@Symfony:risky <./ruleSets/SymfonyRisky.rst>`_

   `Source PhpCsFixer\\Fixer\\PhpUnit\\PhpUnitTestAnnotationFixer <./../src/Fixer/PhpUnit/PhpUnitTestAnnotationFixer.php>`_
-  `php_unit_test_case_static_method_calls <./rules/php_unit/php_unit_test_case_static_method_calls.rst>`_

   Calls to ``PHPUnit\Framework\TestCase`` static methods must all be of the same type, either ``$this->``, ``self::`` or ``static::``.

   *warning risky* Risky when PHPUnit methods are overridden or not accessible, or when project
   has PHPUnit incompatibilities.

   Configuration options:

   - | ``call_type``
     | The call type to use for referring to PHPUnit methods.
     | Allowed values: ``'self'``, ``'static'``, ``'this'``
     | Default value: ``'static'``
   - | ``methods``
     | Dictionary of `method` => `call_type` values that differ from the default strategy.
     | Allowed types: ``array``
     | Default value: ``[]``


   Part of rule set `@PhpCsFixer:risky <./ruleSets/PhpCsFixerRisky.rst>`_

   `Source PhpCsFixer\\Fixer\\PhpUnit\\PhpUnitTestCaseStaticMethodCallsFixer <./../src/Fixer/PhpUnit/PhpUnitTestCaseStaticMethodCallsFixer.php>`_
-  `php_unit_test_class_requires_covers <./rules/php_unit/php_unit_test_class_requires_covers.rst>`_

   Adds a default ``@coversNothing`` annotation to PHPUnit test classes that have no ``@covers*`` annotation.

   Part of rule set `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_

   `Source PhpCsFixer\\Fixer\\PhpUnit\\PhpUnitTestClassRequiresCoversFixer <./../src/Fixer/PhpUnit/PhpUnitTestClassRequiresCoversFixer.php>`_
-  `pow_to_exponentiation <./rules/alias/pow_to_exponentiation.rst>`_

   Converts ``pow`` to the ``**`` operator.

   *warning risky* Risky when the function ``pow`` is overridden.

   Part of rule sets `@PHP56Migration:risky <./ruleSets/PHP56MigrationRisky.rst>`_ `@PHP70Migration:risky <./ruleSets/PHP70MigrationRisky.rst>`_ `@PHP71Migration:risky <./ruleSets/PHP71MigrationRisky.rst>`_ `@PHP74Migration:risky <./ruleSets/PHP74MigrationRisky.rst>`_ `@PHP80Migration:risky <./ruleSets/PHP80MigrationRisky.rst>`_ `@PhpCsFixer:risky <./ruleSets/PhpCsFixerRisky.rst>`_ `@Symfony:risky <./ruleSets/SymfonyRisky.rst>`_

   `Source PhpCsFixer\\Fixer\\Alias\\PowToExponentiationFixer <./../src/Fixer/Alias/PowToExponentiationFixer.php>`_
-  `protected_to_private <./rules/class_notation/protected_to_private.rst>`_

   Converts ``protected`` variables and methods to ``private`` where possible.

   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\ClassNotation\\ProtectedToPrivateFixer <./../src/Fixer/ClassNotation/ProtectedToPrivateFixer.php>`_
-  `psr_autoloading <./rules/basic/psr_autoloading.rst>`_

   Classes must be in a path that matches their namespace, be at least one namespace deep and the class name should match the file name.

   *warning risky* This fixer may change your class name, which will break the code that depends
   on the old name.

   Configuration options:

   - | ``dir``
     | If provided, the directory where the project code is placed.
     | Allowed types: ``null``, ``string``
     | Default value: ``null``


   Part of rule sets `@PhpCsFixer:risky <./ruleSets/PhpCsFixerRisky.rst>`_ `@Symfony:risky <./ruleSets/SymfonyRisky.rst>`_

   `Source PhpCsFixer\\Fixer\\Basic\\PsrAutoloadingFixer <./../src/Fixer/Basic/PsrAutoloadingFixer.php>`_
-  `random_api_migration <./rules/alias/random_api_migration.rst>`_

   Replaces ``rand``, ``srand``, ``getrandmax`` functions calls with their ``mt_*`` analogs or ``random_int``.

   *warning risky* Risky when the configured functions are overridden. Or when relying on the
   seed based generating of the numbers.

   Configuration options:

   - | ``replacements``
     | Mapping between replaced functions with the new ones.
     | Allowed types: ``array``
     | Default value: ``['getrandmax' => 'mt_getrandmax', 'rand' => 'mt_rand', 'srand' => 'mt_srand']``


   Part of rule sets `@PHP70Migration:risky <./ruleSets/PHP70MigrationRisky.rst>`_ `@PHP71Migration:risky <./ruleSets/PHP71MigrationRisky.rst>`_ `@PHP74Migration:risky <./ruleSets/PHP74MigrationRisky.rst>`_ `@PHP80Migration:risky <./ruleSets/PHP80MigrationRisky.rst>`_

   `Source PhpCsFixer\\Fixer\\Alias\\RandomApiMigrationFixer <./../src/Fixer/Alias/RandomApiMigrationFixer.php>`_
-  `regular_callable_call <./rules/function_notation/regular_callable_call.rst>`_

   Callables must be called without using ``call_user_func*`` when possible.

   *warning risky* Risky when the ``call_user_func`` or ``call_user_func_array`` function is
   overridden or when are used in constructions that should be avoided, like
   ``call_user_func_array('foo', ['bar' => 'baz'])`` or ``call_user_func($foo,
   $foo = 'bar')``.

   `Source PhpCsFixer\\Fixer\\FunctionNotation\\RegularCallableCallFixer <./../src/Fixer/FunctionNotation/RegularCallableCallFixer.php>`_
-  `return_assignment <./rules/return_notation/return_assignment.rst>`_

   Local, dynamic and directly referenced variables should not be assigned and directly returned by a function or method.

   Part of rule set `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_

   `Source PhpCsFixer\\Fixer\\ReturnNotation\\ReturnAssignmentFixer <./../src/Fixer/ReturnNotation/ReturnAssignmentFixer.php>`_
-  `return_type_declaration <./rules/function_notation/return_type_declaration.rst>`_

   Adjust spacing around colon in return type declarations and backed enum types.

   Rule is applied only in a PHP 7+ environment.

   Configuration options:

   - | ``space_before``
     | Spacing to apply before colon.
     | Allowed values: ``'none'``, ``'one'``
     | Default value: ``'none'``


   Part of rule sets `@PER <./ruleSets/PER.rst>`_ `@PSR12 <./ruleSets/PSR12.rst>`_ `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\FunctionNotation\\ReturnTypeDeclarationFixer <./../src/Fixer/FunctionNotation/ReturnTypeDeclarationFixer.php>`_
-  `self_accessor <./rules/class_notation/self_accessor.rst>`_

   Inside class or interface element ``self`` should be preferred to the class name itself.

   *warning risky* Risky when using dynamic calls like get_called_class() or late static
   binding.

   Part of rule sets `@PhpCsFixer:risky <./ruleSets/PhpCsFixerRisky.rst>`_ `@Symfony:risky <./ruleSets/SymfonyRisky.rst>`_

   `Source PhpCsFixer\\Fixer\\ClassNotation\\SelfAccessorFixer <./../src/Fixer/ClassNotation/SelfAccessorFixer.php>`_
-  `self_static_accessor <./rules/class_notation/self_static_accessor.rst>`_

   Inside a ``final`` class or anonymous class ``self`` should be preferred to ``static``.

   `Source PhpCsFixer\\Fixer\\ClassNotation\\SelfStaticAccessorFixer <./../src/Fixer/ClassNotation/SelfStaticAccessorFixer.php>`_
-  `semicolon_after_instruction <./rules/semicolon/semicolon_after_instruction.rst>`_

   Instructions must be terminated with a semicolon.

   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\Semicolon\\SemicolonAfterInstructionFixer <./../src/Fixer/Semicolon/SemicolonAfterInstructionFixer.php>`_
-  `set_type_to_cast <./rules/alias/set_type_to_cast.rst>`_

   Cast shall be used, not ``settype``.

   *warning risky* Risky when the ``settype`` function is overridden or when used as the 2nd or
   3rd expression in a ``for`` loop .

   Part of rule sets `@PhpCsFixer:risky <./ruleSets/PhpCsFixerRisky.rst>`_ `@Symfony:risky <./ruleSets/SymfonyRisky.rst>`_

   `Source PhpCsFixer\\Fixer\\Alias\\SetTypeToCastFixer <./../src/Fixer/Alias/SetTypeToCastFixer.php>`_
-  `short_scalar_cast <./rules/cast_notation/short_scalar_cast.rst>`_

   Cast ``(boolean)`` and ``(integer)`` should be written as ``(bool)`` and ``(int)``, ``(double)`` and ``(real)`` as ``(float)``, ``(binary)`` as ``(string)``.

   Part of rule sets `@PER <./ruleSets/PER.rst>`_ `@PHP74Migration <./ruleSets/PHP74Migration.rst>`_ `@PHP80Migration <./ruleSets/PHP80Migration.rst>`_ `@PHP81Migration <./ruleSets/PHP81Migration.rst>`_ `@PHP82Migration <./ruleSets/PHP82Migration.rst>`_ `@PSR12 <./ruleSets/PSR12.rst>`_ `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\CastNotation\\ShortScalarCastFixer <./../src/Fixer/CastNotation/ShortScalarCastFixer.php>`_
-  `simple_to_complex_string_variable <./rules/string_notation/simple_to_complex_string_variable.rst>`_

   Converts explicit variables in double-quoted strings and heredoc syntax from simple to complex format (``${`` to ``{$``).

   Doesn't touch implicit variables. Works together nicely with
   ``explicit_string_variable``.

   Part of rule sets `@PHP82Migration <./ruleSets/PHP82Migration.rst>`_ `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\StringNotation\\SimpleToComplexStringVariableFixer <./../src/Fixer/StringNotation/SimpleToComplexStringVariableFixer.php>`_
-  `simplified_if_return <./rules/control_structure/simplified_if_return.rst>`_

   Simplify ``if`` control structures that return the boolean result of their condition.

   `Source PhpCsFixer\\Fixer\\ControlStructure\\SimplifiedIfReturnFixer <./../src/Fixer/ControlStructure/SimplifiedIfReturnFixer.php>`_
-  `simplified_null_return <./rules/return_notation/simplified_null_return.rst>`_

   A return statement wishing to return ``void`` should not return ``null``.

   `Source PhpCsFixer\\Fixer\\ReturnNotation\\SimplifiedNullReturnFixer <./../src/Fixer/ReturnNotation/SimplifiedNullReturnFixer.php>`_
-  `single_blank_line_at_eof <./rules/whitespace/single_blank_line_at_eof.rst>`_

   A PHP file without end tag must always end with a single empty line feed.

   Part of rule sets `@PER <./ruleSets/PER.rst>`_ `@PSR12 <./ruleSets/PSR12.rst>`_ `@PSR2 <./ruleSets/PSR2.rst>`_ `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\Whitespace\\SingleBlankLineAtEofFixer <./../src/Fixer/Whitespace/SingleBlankLineAtEofFixer.php>`_
-  `single_blank_line_before_namespace <./rules/namespace_notation/single_blank_line_before_namespace.rst>`_

   There should be exactly one blank line before a namespace declaration.

   Part of rule sets `@PER <./ruleSets/PER.rst>`_ `@PSR12 <./ruleSets/PSR12.rst>`_ `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\NamespaceNotation\\SingleBlankLineBeforeNamespaceFixer <./../src/Fixer/NamespaceNotation/SingleBlankLineBeforeNamespaceFixer.php>`_
-  `single_class_element_per_statement <./rules/class_notation/single_class_element_per_statement.rst>`_

   There MUST NOT be more than one property or constant declared per statement.

   Configuration options:

   - | ``elements``
     | List of strings which element should be modified.
     | Allowed values: a subset of ``['const', 'property']``
     | Default value: ``['const', 'property']``


   Part of rule sets `@PER <./ruleSets/PER.rst>`_ `@PSR12 <./ruleSets/PSR12.rst>`_ `@PSR2 <./ruleSets/PSR2.rst>`_ `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\ClassNotation\\SingleClassElementPerStatementFixer <./../src/Fixer/ClassNotation/SingleClassElementPerStatementFixer.php>`_
-  `single_import_per_statement <./rules/import/single_import_per_statement.rst>`_

   There MUST be one use keyword per declaration.

   Configuration options:

   - | ``group_to_single_imports``
     | Whether to change group imports into single imports.
     | Allowed types: ``bool``
     | Default value: ``true``


   Part of rule sets `@PER <./ruleSets/PER.rst>`_ `@PSR12 <./ruleSets/PSR12.rst>`_ `@PSR2 <./ruleSets/PSR2.rst>`_ `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\Import\\SingleImportPerStatementFixer <./../src/Fixer/Import/SingleImportPerStatementFixer.php>`_
-  `single_line_after_imports <./rules/import/single_line_after_imports.rst>`_

   Each namespace use MUST go on its own line and there MUST be one blank line after the use statements block.

   Part of rule sets `@PER <./ruleSets/PER.rst>`_ `@PSR12 <./ruleSets/PSR12.rst>`_ `@PSR2 <./ruleSets/PSR2.rst>`_ `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\Import\\SingleLineAfterImportsFixer <./../src/Fixer/Import/SingleLineAfterImportsFixer.php>`_
-  `single_line_comment_spacing <./rules/comment/single_line_comment_spacing.rst>`_

   Single-line comments must have proper spacing.

   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\Comment\\SingleLineCommentSpacingFixer <./../src/Fixer/Comment/SingleLineCommentSpacingFixer.php>`_
-  `single_line_comment_style <./rules/comment/single_line_comment_style.rst>`_

   Single-line comments and multi-line comments with only one line of actual content should use the ``//`` syntax.

   Configuration options:

   - | ``comment_types``
     | List of comment types to fix
     | Allowed values: a subset of ``['asterisk', 'hash']``
     | Default value: ``['asterisk', 'hash']``


   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\Comment\\SingleLineCommentStyleFixer <./../src/Fixer/Comment/SingleLineCommentStyleFixer.php>`_
-  `single_line_throw <./rules/function_notation/single_line_throw.rst>`_

   Throwing exception must be done in single line.

   Part of rule set `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\FunctionNotation\\SingleLineThrowFixer <./../src/Fixer/FunctionNotation/SingleLineThrowFixer.php>`_
-  `single_quote <./rules/string_notation/single_quote.rst>`_

   Convert double quotes to single quotes for simple strings.

   Configuration options:

   - | ``strings_containing_single_quote_chars``
     | Whether to fix double-quoted strings that contains single-quotes.
     | Allowed types: ``bool``
     | Default value: ``false``


   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\StringNotation\\SingleQuoteFixer <./../src/Fixer/StringNotation/SingleQuoteFixer.php>`_
-  `single_space_after_construct <./rules/language_construct/single_space_after_construct.rst>`_

   Ensures a single space after language constructs.

   Configuration options:

   - | ``constructs``
     | List of constructs which must be followed by a single space.
     | Allowed values: a subset of ``['abstract', 'as', 'attribute', 'break', 'case', 'catch', 'class', 'clone', 'comment', 'const', 'const_import', 'continue', 'do', 'echo', 'else', 'elseif', 'enum', 'extends', 'final', 'finally', 'for', 'foreach', 'function', 'function_import', 'global', 'goto', 'if', 'implements', 'include', 'include_once', 'instanceof', 'insteadof', 'interface', 'match', 'named_argument', 'namespace', 'new', 'open_tag_with_echo', 'php_doc', 'php_open', 'print', 'private', 'protected', 'public', 'readonly', 'require', 'require_once', 'return', 'static', 'switch', 'throw', 'trait', 'try', 'type_colon', 'use', 'use_lambda', 'use_trait', 'var', 'while', 'yield', 'yield_from']``
     | Default value: ``['abstract', 'as', 'attribute', 'break', 'case', 'catch', 'class', 'clone', 'comment', 'const', 'const_import', 'continue', 'do', 'echo', 'else', 'elseif', 'enum', 'extends', 'final', 'finally', 'for', 'foreach', 'function', 'function_import', 'global', 'goto', 'if', 'implements', 'include', 'include_once', 'instanceof', 'insteadof', 'interface', 'match', 'named_argument', 'namespace', 'new', 'open_tag_with_echo', 'php_doc', 'php_open', 'print', 'private', 'protected', 'public', 'readonly', 'require', 'require_once', 'return', 'static', 'switch', 'throw', 'trait', 'try', 'use', 'use_lambda', 'use_trait', 'var', 'while', 'yield', 'yield_from']``


   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\LanguageConstruct\\SingleSpaceAfterConstructFixer <./../src/Fixer/LanguageConstruct/SingleSpaceAfterConstructFixer.php>`_
-  `single_trait_insert_per_statement <./rules/class_notation/single_trait_insert_per_statement.rst>`_

   Each trait ``use`` must be done as single statement.

   Part of rule sets `@PER <./ruleSets/PER.rst>`_ `@PSR12 <./ruleSets/PSR12.rst>`_ `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\ClassNotation\\SingleTraitInsertPerStatementFixer <./../src/Fixer/ClassNotation/SingleTraitInsertPerStatementFixer.php>`_
-  `space_after_semicolon <./rules/semicolon/space_after_semicolon.rst>`_

   Fix whitespace after a semicolon.

   Configuration options:

   - | ``remove_in_empty_for_expressions``
     | Whether spaces should be removed for empty `for` expressions.
     | Allowed types: ``bool``
     | Default value: ``false``


   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\Semicolon\\SpaceAfterSemicolonFixer <./../src/Fixer/Semicolon/SpaceAfterSemicolonFixer.php>`_
-  `standardize_increment <./rules/operator/standardize_increment.rst>`_

   Increment and decrement operators should be used if possible.

   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\Operator\\StandardizeIncrementFixer <./../src/Fixer/Operator/StandardizeIncrementFixer.php>`_
-  `standardize_not_equals <./rules/operator/standardize_not_equals.rst>`_

   Replace all ``<>`` with ``!=``.

   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\Operator\\StandardizeNotEqualsFixer <./../src/Fixer/Operator/StandardizeNotEqualsFixer.php>`_
-  `statement_indentation <./rules/whitespace/statement_indentation.rst>`_

   Each statement must be indented.

   `Source PhpCsFixer\\Fixer\\Whitespace\\StatementIndentationFixer <./../src/Fixer/Whitespace/StatementIndentationFixer.php>`_
-  `static_lambda <./rules/function_notation/static_lambda.rst>`_

   Lambdas not (indirect) referencing ``$this`` must be declared ``static``.

   *warning risky* Risky when using ``->bindTo`` on lambdas without referencing to ``$this``.

   `Source PhpCsFixer\\Fixer\\FunctionNotation\\StaticLambdaFixer <./../src/Fixer/FunctionNotation/StaticLambdaFixer.php>`_
-  `strict_comparison <./rules/strict/strict_comparison.rst>`_

   Comparisons should be strict.

   *warning risky* Changing comparisons to strict might change code behavior.

   Part of rule set `@PhpCsFixer:risky <./ruleSets/PhpCsFixerRisky.rst>`_

   `Source PhpCsFixer\\Fixer\\Strict\\StrictComparisonFixer <./../src/Fixer/Strict/StrictComparisonFixer.php>`_
-  `strict_param <./rules/strict/strict_param.rst>`_

   Functions should be used with ``$strict`` param set to ``true``.

   The functions "array_keys", "array_search", "base64_decode", "in_array" and
   "mb_detect_encoding" should be used with $strict param.

   *warning risky* Risky when the fixed function is overridden or if the code relies on
   non-strict usage.

   Part of rule set `@PhpCsFixer:risky <./ruleSets/PhpCsFixerRisky.rst>`_

   `Source PhpCsFixer\\Fixer\\Strict\\StrictParamFixer <./../src/Fixer/Strict/StrictParamFixer.php>`_
-  `string_length_to_empty <./rules/string_notation/string_length_to_empty.rst>`_

   String tests for empty must be done against ``''``, not with ``strlen``.

   *warning risky* Risky when ``strlen`` is overridden, when called using a ``stringable``
   object, also no longer triggers warning when called using non-string(able).

   Part of rule sets `@PhpCsFixer:risky <./ruleSets/PhpCsFixerRisky.rst>`_ `@Symfony:risky <./ruleSets/SymfonyRisky.rst>`_

   `Source PhpCsFixer\\Fixer\\StringNotation\\StringLengthToEmptyFixer <./../src/Fixer/StringNotation/StringLengthToEmptyFixer.php>`_
-  `string_line_ending <./rules/string_notation/string_line_ending.rst>`_

   All multi-line strings must use correct line ending.

   *warning risky* Changing the line endings of multi-line strings might affect string
   comparisons and outputs.

   Part of rule sets `@PhpCsFixer:risky <./ruleSets/PhpCsFixerRisky.rst>`_ `@Symfony:risky <./ruleSets/SymfonyRisky.rst>`_

   `Source PhpCsFixer\\Fixer\\StringNotation\\StringLineEndingFixer <./../src/Fixer/StringNotation/StringLineEndingFixer.php>`_
-  `switch_case_semicolon_to_colon <./rules/control_structure/switch_case_semicolon_to_colon.rst>`_

   A case should be followed by a colon and not a semicolon.

   Part of rule sets `@PER <./ruleSets/PER.rst>`_ `@PSR12 <./ruleSets/PSR12.rst>`_ `@PSR2 <./ruleSets/PSR2.rst>`_ `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\ControlStructure\\SwitchCaseSemicolonToColonFixer <./../src/Fixer/ControlStructure/SwitchCaseSemicolonToColonFixer.php>`_
-  `switch_case_space <./rules/control_structure/switch_case_space.rst>`_

   Removes extra spaces between colon and case value.

   Part of rule sets `@PER <./ruleSets/PER.rst>`_ `@PSR12 <./ruleSets/PSR12.rst>`_ `@PSR2 <./ruleSets/PSR2.rst>`_ `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\ControlStructure\\SwitchCaseSpaceFixer <./../src/Fixer/ControlStructure/SwitchCaseSpaceFixer.php>`_
-  `switch_continue_to_break <./rules/control_structure/switch_continue_to_break.rst>`_

   Switch case must not be ended with ``continue`` but with ``break``.

   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\ControlStructure\\SwitchContinueToBreakFixer <./../src/Fixer/ControlStructure/SwitchContinueToBreakFixer.php>`_
-  `ternary_operator_spaces <./rules/operator/ternary_operator_spaces.rst>`_

   Standardize spaces around ternary operator.

   Part of rule sets `@PER <./ruleSets/PER.rst>`_ `@PSR12 <./ruleSets/PSR12.rst>`_ `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\Operator\\TernaryOperatorSpacesFixer <./../src/Fixer/Operator/TernaryOperatorSpacesFixer.php>`_
-  `ternary_to_elvis_operator <./rules/operator/ternary_to_elvis_operator.rst>`_

   Use the Elvis operator ``?:`` where possible.

   *warning risky* Risky when relying on functions called on both sides of the ``?`` operator.

   Part of rule sets `@PhpCsFixer:risky <./ruleSets/PhpCsFixerRisky.rst>`_ `@Symfony:risky <./ruleSets/SymfonyRisky.rst>`_

   `Source PhpCsFixer\\Fixer\\Operator\\TernaryToElvisOperatorFixer <./../src/Fixer/Operator/TernaryToElvisOperatorFixer.php>`_
-  `ternary_to_null_coalescing <./rules/operator/ternary_to_null_coalescing.rst>`_

   Use ``null`` coalescing operator ``??`` where possible. Requires PHP >= 7.0.

   Part of rule sets `@PHP70Migration <./ruleSets/PHP70Migration.rst>`_ `@PHP71Migration <./ruleSets/PHP71Migration.rst>`_ `@PHP73Migration <./ruleSets/PHP73Migration.rst>`_ `@PHP74Migration <./ruleSets/PHP74Migration.rst>`_ `@PHP80Migration <./ruleSets/PHP80Migration.rst>`_ `@PHP81Migration <./ruleSets/PHP81Migration.rst>`_ `@PHP82Migration <./ruleSets/PHP82Migration.rst>`_

   `Source PhpCsFixer\\Fixer\\Operator\\TernaryToNullCoalescingFixer <./../src/Fixer/Operator/TernaryToNullCoalescingFixer.php>`_
-  `trailing_comma_in_multiline <./rules/control_structure/trailing_comma_in_multiline.rst>`_

   Multi-line arrays, arguments list, parameters list and ``match`` expressions must have a trailing comma.

   Configuration options:

   - | ``after_heredoc``
     | Whether a trailing comma should also be placed after heredoc end.
     | Allowed types: ``bool``
     | Default value: ``false``
   - | ``elements``
     | Where to fix multiline trailing comma (PHP >= 8.0 for `parameters` and `match`).
     | Allowed values: a subset of ``['arguments', 'arrays', 'match', 'parameters']``
     | Default value: ``['arrays']``


   Part of rule sets `@PHP73Migration <./ruleSets/PHP73Migration.rst>`_ `@PHP74Migration <./ruleSets/PHP74Migration.rst>`_ `@PHP80Migration <./ruleSets/PHP80Migration.rst>`_ `@PHP81Migration <./ruleSets/PHP81Migration.rst>`_ `@PHP82Migration <./ruleSets/PHP82Migration.rst>`_ `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\ControlStructure\\TrailingCommaInMultilineFixer <./../src/Fixer/ControlStructure/TrailingCommaInMultilineFixer.php>`_
-  `trim_array_spaces <./rules/array_notation/trim_array_spaces.rst>`_

   Arrays should be formatted like function/method arguments, without leading or trailing single line space.

   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\ArrayNotation\\TrimArraySpacesFixer <./../src/Fixer/ArrayNotation/TrimArraySpacesFixer.php>`_
-  `types_spaces <./rules/whitespace/types_spaces.rst>`_

   A single space or none should be around union type and intersection type operators.

   Configuration options:

   - | ``space``
     | spacing to apply around union type and intersection type operators.
     | Allowed values: ``'none'``, ``'single'``
     | Default value: ``'none'``
   - | ``space_multiple_catch``
     | spacing to apply around type operator when catching exceptions of multiple types, use `null` to follow the value configured for `space`.
     | Allowed values: ``'none'``, ``'single'``, ``null``
     | Default value: ``null``


   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\Whitespace\\TypesSpacesFixer <./../src/Fixer/Whitespace/TypesSpacesFixer.php>`_
-  `unary_operator_spaces <./rules/operator/unary_operator_spaces.rst>`_

   Unary operators should be placed adjacent to their operands.

   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\Operator\\UnaryOperatorSpacesFixer <./../src/Fixer/Operator/UnaryOperatorSpacesFixer.php>`_
-  `use_arrow_functions <./rules/function_notation/use_arrow_functions.rst>`_

   Anonymous functions with one-liner return statement must use arrow functions.

   *warning risky* Risky when using ``isset()`` on outside variables that are not imported with
   ``use ()``.

   Part of rule sets `@PHP74Migration:risky <./ruleSets/PHP74MigrationRisky.rst>`_ `@PHP80Migration:risky <./ruleSets/PHP80MigrationRisky.rst>`_

   `Source PhpCsFixer\\Fixer\\FunctionNotation\\UseArrowFunctionsFixer <./../src/Fixer/FunctionNotation/UseArrowFunctionsFixer.php>`_
-  `visibility_required <./rules/class_notation/visibility_required.rst>`_

   Visibility MUST be declared on all properties and methods; ``abstract`` and ``final`` MUST be declared before the visibility; ``static`` MUST be declared after the visibility.

   Configuration options:

   - | ``elements``
     | The structural elements to fix (PHP >= 7.1 required for `const`).
     | Allowed values: a subset of ``['const', 'method', 'property']``
     | Default value: ``['property', 'method', 'const']``


   Part of rule sets `@PER <./ruleSets/PER.rst>`_ `@PHP71Migration <./ruleSets/PHP71Migration.rst>`_ `@PHP73Migration <./ruleSets/PHP73Migration.rst>`_ `@PHP74Migration <./ruleSets/PHP74Migration.rst>`_ `@PHP80Migration <./ruleSets/PHP80Migration.rst>`_ `@PHP81Migration <./ruleSets/PHP81Migration.rst>`_ `@PHP82Migration <./ruleSets/PHP82Migration.rst>`_ `@PSR12 <./ruleSets/PSR12.rst>`_ `@PSR2 <./ruleSets/PSR2.rst>`_ `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\ClassNotation\\VisibilityRequiredFixer <./../src/Fixer/ClassNotation/VisibilityRequiredFixer.php>`_
-  `void_return <./rules/function_notation/void_return.rst>`_

   Add ``void`` return type to functions with missing or empty return statements, but priority is given to ``@return`` annotations. Requires PHP >= 7.1.

   *warning risky* Modifies the signature of functions.

   Part of rule sets `@PHP71Migration:risky <./ruleSets/PHP71MigrationRisky.rst>`_ `@PHP74Migration:risky <./ruleSets/PHP74MigrationRisky.rst>`_ `@PHP80Migration:risky <./ruleSets/PHP80MigrationRisky.rst>`_

   `Source PhpCsFixer\\Fixer\\FunctionNotation\\VoidReturnFixer <./../src/Fixer/FunctionNotation/VoidReturnFixer.php>`_
-  `whitespace_after_comma_in_array <./rules/array_notation/whitespace_after_comma_in_array.rst>`_

   In array declaration, there MUST be a whitespace after each comma.

   Configuration options:

   - | ``ensure_single_space``
     | If there are only horizontal whitespaces after the comma then ensure it is a single space.
     | Allowed types: ``bool``
     | Default value: ``false``


   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\ArrayNotation\\WhitespaceAfterCommaInArrayFixer <./../src/Fixer/ArrayNotation/WhitespaceAfterCommaInArrayFixer.php>`_
-  `yoda_style <./rules/control_structure/yoda_style.rst>`_

   Write conditions in Yoda style (``true``), non-Yoda style (``['equal' => false, 'identical' => false, 'less_and_greater' => false]``) or ignore those conditions (``null``) based on configuration.

   Configuration options:

   - | ``equal``
     | Style for equal (`==`, `!=`) statements.
     | Allowed types: ``bool``, ``null``
     | Default value: ``true``
   - | ``identical``
     | Style for identical (`===`, `!==`) statements.
     | Allowed types: ``bool``, ``null``
     | Default value: ``true``
   - | ``less_and_greater``
     | Style for less and greater than (`<`, `<=`, `>`, `>=`) statements.
     | Allowed types: ``bool``, ``null``
     | Default value: ``null``
   - | ``always_move_variable``
     | Whether variables should always be on non assignable side when applying Yoda style.
     | Allowed types: ``bool``
     | Default value: ``false``


   Part of rule sets `@PhpCsFixer <./ruleSets/PhpCsFixer.rst>`_ `@Symfony <./ruleSets/Symfony.rst>`_

   `Source PhpCsFixer\\Fixer\\ControlStructure\\YodaStyleFixer <./../src/Fixer/ControlStructure/YodaStyleFixer.php>`_
