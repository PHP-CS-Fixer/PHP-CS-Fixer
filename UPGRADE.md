UPGRADE GUIDE FROM 1.x to 2.0
=============================

This is guide for upgrade from version 1.x to 2.0 for using the CLI tool.

Rules and sets
--------------
To configure which fixers should be used you must now set rules and sets instead of fixers and level. This affects both configuration file and CLI arguments.

Default ruleset was changed from Symfony standard to more generic PSR2. You can still use Symfony standard, which in fact extends PSR2.

The term of risky fixers was introduced. Risky fixer is a fixer that may change the meaning of code (like `StrictComparisonFixer` fixer, which will change `==` into `===`). No rules that are followed by risky fixers are run by default. You need to explicitly permit risky fixers to run them.

Default configuraton changes
----------------------------
By default, PSR2 rules are used instead of Symfony rules.
Files that will be fixed are php/phpt/twig instead of php/twig/xml/yml.
Finally, the caching mechanism is enabled by default.

CLI options
-----------

1.x           | 2.0           | Description                                     | Note
------------- | ------------- | ----------------------------------------------- | ----
              | --allow-risky | Are risky fixers allowed                        |
              | --cache-file  | The path to the cache file                      | option was added
--config      |               | Config class codename                           | option was removed
--config-file | --config      | The path to a .php_cs file                      | option was renamed
--diff        | --diff        | Show diff                                       |
--dry-run     | --dry-run     | Run in dry-run mode                             |
--fixers      |               | Coding standard fixers                          | options was removed, see --rules
--format      | --format      | Choose format                                   |
--level       |               | Coding standard level                           | options was removed, see --rules
              | --rules       | Rules to be used                                | option was added
              | --using-cache | Does cache should be used                       | option was added
              | --path-mode   | Should the finder from configuration be         | option was added
              |               | overriden or intersected with `path` argument   |

CLI argument
------------

On 2.x line `path` argument is an array, so you may pass multiple paths.

Intersection path mode makes the `path` argument a mask for finder you have defined in your configuration file.
Only files pointed by both finder and CLI `path` argument will be fixed.

Exit codes
----------

Exit codes have been change and are build using the following bit flags:

1.x bit | 2.0 bit | Description                             | Note
-------:| -------:| --------------------------------------- | ----
0       | 0       | OK                                      |
1       |         | No changes made                         | flag was removed
        | 4       | Some files have invalid syntax          | flag was added, works only in dry-run mode
        | 8       | Some files need fixing                  | flag was added, works only in dry-run mode
16      | 16      | Configuration error of the application  |
32      | 32      | Configuration error of a Fixer          |
        | 64      | Exception within the application        | flag was added

Namespace
---------
`Symfony\CS` namespace was renamed into `PhpCsFixer`.

Config file
-----------
From now you can create new configuration file: `.php_cs.dist`. This file is used if no `.php_cs` file was found. It is recommended to create `.php_cs.dist` file attached in your repository and add `.php_cs` file to `.gitignore` for allowing your contributors to have theirs own configuration file.

Config and Finder classes
-------------------------
All off `Symfony\CS\Config\*` and `Symfony\CS\Finder\*` classes have been removed, instead use `PhpCsFixer\Config` and `PhpCsFixer\Finder`.

For that reason you can not set config class by `--config` CLI argument, from now it is used to set configuration file. Thanks to this the `--config-file` CLI argument is no longer available.

Renamed rules
-------------

Old name | New name | Note
-------- | -------- | ----
array_element_no_space_before_comma            | no_whitespace_before_comma_in_array
array_element_white_space_after_comma          | whitespace_after_comma_in_array
blankline_after_open_tag                       | blank_line_after_opening_tag
double_arrow_multiline_whitespaces             | no_multiline_whitespace_around_double_arrow
duplicate_semicolon                            | no_empty_statement                                | new one fixes more cases
empty_return                                   | simplified_null_return
eof_ending                                     | single_blank_line_at_eof
extra_empty_lines                              | no_extra_consecutive_blank_lines
function_call_space                            | no_spaces_after_function_name
indentation                                    | no_tab_indentation
join_function                                  | no_alias_functions                                | new one fixes more aliases
line_after_namespace                           | blank_line_after_namespace
linefeed                                       | unix_line_endings
list_commas                                    | no_trailing_comma_in_list_call
logical_not_operators_with_spaces              | not_operator_with_space
logical_not_operators_with_successor_space     | not_operator_with_successor_space
method_argument_default_value                  | no_unreachable_default_argument_value
multiline_array_trailing_comma                 | trailing_comma_in_multiline_array
multiline_spaces_before_semicolon              | no_multiline_whitespace_before_semicolons
multiple_use                                   | single_import_per_statement
namespace_no_leading_whitespace                | no_leading_namespace_whitespace
newline_after_open_tag                         | linebreak_after_opening_tag
no_empty_lines_after_phpdocs                   | no_blank_lines_after_phpdoc
object_operator                                | object_operator_without_whitespace
operators_spaces                               | binary_operator_spaces
ordered_use                                    | ordered_imports
parenthesis                                    | no_spaces_inside_parenthesis
php4_constructor                               | no_php4_constructor
php_closing_tag                                | no_closing_tag
phpdoc_params                                  | phpdoc_align
phpdoc_short_description                       | phpdoc_summary
remove_leading_slash_use                       | no_leading_import_slash
remove_lines_between_uses                      | no_blank_lines_between_uses
return                                         | blank_line_before_return
short_bool_cast                                | no_short_bool_cast
short_echo_tag                                 | no_short_echo_tag
short_tag                                      | full_opening_tag
single_array_no_trailing_comma                 | no_trailing_comma_in_singleline_array
spaces_after_semicolon                         | space_after_semicolon
spaces_before_semicolon                        | no_singleline_whitespace_before_semicolons
spaces_cast                                    | cast_spaces
standardize_not_equal                          | standardize_not_equals
strict                                         | strict_comparison
ternary_spaces                                 | ternary_operator_spaces
trailing_spaces                                | no_trailing_whitespace
unary_operators_spaces                         | unary_operator_spaces
unneeded_control_parentheses                   | no_unneeded_control_parentheses
unused_use                                     | no_unused_imports
visibility                                     | visibility_required
whitespacy_lines                               | no_whitespace_in_blank_lines

Changes to Fixers
-----------------

Fixer | Note
----- | ----
psr0  | Fixer no longer takes base dir from `ConfigInterface::getDir`, instead you may configure the fixer with `['dir' => 'my/path']`.
