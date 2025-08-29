# UPGRADE GUIDE FROM 2.x to 3.0

This is guide for upgrade from version 2.x to 3.0 for using the CLI tool.

*Before following this guide, install [v2.19](https://github.com/PHP-CS-Fixer/PHP-CS-Fixer/releases/tag/v2.19.0) and run in verbose mode (`php-cs-fixer fix -v`) or in future mode (`PHP_CS_FIXER_FUTURE_MODE=1 php-cs-fixer fix`) to identify deprecations and fix them first.*

## Rename of files

| 2.x              | 3.0                      | Description                            |
|------------------|--------------------------|----------------------------------------|
| `.php_cs`        | `.php-cs-fixer.php`      | Configuration file (local)             |
| `.php_cs.dist`   | `.php-cs-fixer.dist.php` | Configuration file (to be distributed) |
| `.php_cs.cache`  | `.php-cs-fixer.cache`    | Cache file                             |

## CLI options

| 2.x              | 3.0             | Description                                     | Note                                   |
| ---------------- | --------------- | ----------------------------------------------- | -------------------------------------- |
| --diff-format    |                 | Type of differ                                  | Option was removed, all diffs are now  |
|                  |                 |                                                 | `udiff`                                |
| --show-progress  | --show-progress | Type of progress indicator                      | Allowed values were modified:          |
|                  |                 |                                                 | `run-in` and `estimating` was removed, |
|                  |                 |                                                 | `estimating-max` was renamed to `dots` |
| --rules          | --rules         | Default value changed from @PSR2 to @PSR12      |                                        |
| --config --rules |                 |                                                 | No longer allowed to pass both         |

## Changes to rules

### Renamed rules

| Old name                                   | New name                                                                          | Note                                                 |
|--------------------------------------------|-----------------------------------------------------------------------------------|------------------------------------------------------|
|`blank_line_before_return`                  | `blank_line_before_statement`                                                     | use configuration `['statements' => ['return']]`     |
|`final_static_access`                       | `self_static_accessor`                                                            |                                                      |
|`hash_to_slash_comment`                     | `single_line_comment_style`                                                       | use configuration `['comment_types' => ['hash']]`    |
|`lowercase_constants`                       | `constant_case`                                                                   | use configuration `['case' => 'lower']`              |
|`method_separation`                         | `class_attributes_separation`                                                     | use configuration `['elements' => ['method']]`       |
|`no_extra_consecutive_blank_lines`          | `no_extra_blank_lines`                                                            |                                                      |
|`no_multiline_whitespace_before_semicolons` | `multiline_whitespace_before_semicolons`                                          |                                                      |
|`no_short_echo_tag`                         | `echo_tag_syntax`                                                                 | use configuration `['format' => 'long']`             |
|`php_unit_ordered_covers`                   | `phpdoc_order_by_value`                                                           | use configuration `['annotations' => [ 'covers' ]]`  |
|`phpdoc_inline_tag`                         | `general_phpdoc_tag_rename`, `phpdoc_inline_tag_normalizer` and `phpdoc_tag_type` |                                                      |
|`pre_increment`                             | `increment_style`                                                                 | use configuration `['style' => 'pre']`               |
|`psr0`                                      | `psr_autoloading`                                                                 | use configuration `['dir' => x ]`                    |
|`psr4`                                      | `psr_autoloading`                                                                 |                                                      |
|`silenced_deprecation_error`                | `error_suppression`                                                               |                                                      |
|`trailing_comma_in_multiline_array`         | `trailing_comma_in_multiline`                                                     | use configuration `['elements' => ['arrays']]`       |

### Removed rootless configuration

| Rule                                 | Root option    | Note                                                      |
|--------------------------------------| -------------- |-----------------------------------------------------------|
| `general_phpdoc_annotation_remove`   | `annotations`  |                                                           |
| `no_extra_consecutive_blank_lines`   | `tokens`       |                                                           |
| `no_spaces_around_offset`            | `positions`    |                                                           |
| `no_unneeded_control_parentheses`    | `statements`   |                                                           |
| `ordered_class_elements`             | `order`        |                                                           |
| `php_unit_construct`                 | `assertions`   |                                                           |
| `php_unit_dedicate_assert`           | `target`       | root option works differently than rootless configuration |
| `php_unit_strict`                    | `assertions`   |                                                           |
| `phpdoc_no_alias_tag`                | `replacements` |                                                           |
| `phpdoc_return_self_reference`       | `replacements` |                                                           |
| `random_api_migration`               | `replacements` |                                                           |
| `single_class_element_per_statement` | `elements`     |                                                           |
| `visibility_required`                | `elements`     |                                                           |

### Changed options

| Rule                               | Option                                       | Change                                                                                                                                                                    |
|------------------------------------|----------------------------------------------|---------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| `binary_operator_spaces`           | `align_double_arrow`                         | option was removed, use `operators` instead                                                                                                                               |
| `binary_operator_spaces`           | `align_equals`                               | option was removed use `operators` instead                                                                                                                                |
| `blank_line_before_statement`      | `statements: die`                            | option `die` was removed from `statements`, use `exit` instead                                                                                                            |
| `class_attributes_separation`      | `elements`                                   | option does no longer accept flat array as a value, use map instead                                                                                                       |
| `class_definition`                 | `multiLineExtendsEachSingleLine`             | option was renamed to  `multi_line_extends_each_single_line`                                                                                                              |
| `class_definition`                 | `singleItemSingleLine`                       | option was renamed to  `single_item_single_line`                                                                                                                          |
| `class_definition`                 | `singleLine`                                 | option was renamed to  `single_line`                                                                                                                                      |
| `doctrine_annotation_spaces`       | `around_argument_assignments`                | option was removed, use `before_argument_assignments` and `after_argument_assignments` instead                                                                            |
| `doctrine_annotation_spaces`       | `around_array_assignments`                   | option was removed, use `after_array_assignments_colon`, `after_array_assignments_equals`, `before_array_assignments_colon` and `before_array_assignments_equals` instead |
| `final_internal_class`             | `annotation-black-list`                      | option was renamed, use `annotation_exclude`                                                                                                                              |
| `final_internal_class`             | `annotation-white-list`                      | option was renamed, use `annotation_include`                                                                                                                              |
| `final_internal_class`             | `consider-absent-docblock-as-internal-class` | option was renamed, use `consider_absent_docblock_as_internal_class`                                                                                                      |
| `header_comment`                   | `commentType`                                | option was renamed to `comment_type`                                                                                                                                      |
| `is_null`                          | `use_yoda_style`                             | option was removed, use `yoda_style` rule instead                                                                                                                         |
| `no_extra_consecutive_blank_lines` | `tokens`                                     | one of possible values, `useTrait`, was renamed to `use_trait`                                                                                                            |
| `ordered_class_elements`           | `sortAlgorithm`                              | option was renamed, use `sort_algorithm` instead                                                                                                                          |
| `ordered_imports`                  | `importsOrder`                               | option was renamed, use `imports_order`                                                                                                                                   |
| `ordered_imports`                  | `sortAlgorithm`                              | option was renamed, use `sort_algorithm`                                                                                                                                  |
| `php_unit_dedicate_assert`         | `functions`                                  | option was removed, use `target` instead                                                                                                                                  |
| `php_unit_test_annotation`         | `case`                                       | option was removed, use `php_unit_method_casing` rule instead                                                                                                             |

### Changed default values of options

| Rule                         | Option                            | Old value                                            | New value                                                                |
|------------------------------|-----------------------------------|------------------------------------------------------|--------------------------------------------------------------------------|
| `array_syntax`               | `syntax`                          | `'long'`                                             | `'short'`                                                                |
| `function_to_constant`       | `functions`                       | `['get_class', 'php_sapi_name', 'phpversion', 'pi']` | `['get_called_class', 'get_class', 'php_sapi_name', 'phpversion', 'pi']` |
| `list_syntax`                | `syntax`                          | `'long'`                                             | `'short'`                                                                |
| `method_argument_space`      | `on_multiline`                    | `'ignore'`                                           | `'ensure_fully_multiline'`                                               |
| `native_constant_invocation` | `strict`                          | `false`                                              | `true`                                                                   |
| `native_function_casing`     | `include`                         | `'@internal'`                                        | `'@compiler_optimized'`                                                  |
| `native_function_invocation` | `include`                         | `'@internal'`                                        | `'@compiler_optimized'`                                                  |
| `native_function_invocation` | `strict`                          | `false`                                              | `true`                                                                   |
| `non_printable_character`    | `use_escape_sequences_in_strings` | `false`                                              | `true` (when running on PHP 7.0 and up)                                  |
| `php_unit_dedicate_assert`   | `target`                          | `'5.0'`                                              | `'newest'`                                                               |
| `phpdoc_align`               | `tags`                            | `['param', 'return', 'throws', 'type', 'var']`       | `['method', 'param', 'property', 'return', 'throws', 'type', 'var']`     |
| `phpdoc_scalar`              | `types`                           | `['boolean', 'double', 'integer', 'real', 'str']`    | `['boolean', 'callback', 'double', 'integer', 'real', 'str']`            |

### Removed rule sets

| Rule set          | Note       |
|-------------------|------------|
| `@PHP56Migration` | was empty  |

### Rule behavior changes

- `no_unused_imports` now runs all files defined in the configuration (used to exclude some hardcoded directories)

### Various

- `udiff` output now includes the file name in the output (if applicable)

## Code BC changes

### Removed; various

- class `AbstractAlignFixerHelper` has been removed
- class `AccessibleObject` has been removed
- class `AlignDoubleArrowFixerHelper` has been removed
- class `AlignEqualsFixerHelper` has been removed
- class `FixerConfigurationResolverRootless` has been removed
- `HeaderCommentFixer` deprecated properties have been removed
- `MethodArgumentSpaceFixer` deprecated methods have been removed
- `NoMixedEchoPrintFixer` the property `$defaultConfig` has been removed
- class `Tokens`, the following methods has been removed:
   - `current()`
   - `key()`
   - `next()`
   - `rewind()`
   - `valid()`
- namespace `PhpCsFixer\Test\` and each class in it has been removed, as it served pure development purpose and should not be part of production code - reach out to community if you are willing to help building dev package

### Interface changes

- `ConfigurableFixerInterface` has been updated
- `ConfigurationDefinitionFixerInterface` has been removed in favor of the updated `ConfigurableFixerInterface`
- `DefinedFixerInterface` has been removed, related methods are now part of the updated `FixerInterface` interface
- `DifferInterface` has been updated
- `FixerInterface` interface has been updated
- `PhpCsFixer\RuleSetInterface` has been removed in favor of `\PhpCsFixer\RuleSet\RuleSetInterface`

### BC breaks; various

- class `Token` is now `final`
- class `Tokens` is now `final`
- method `create` of class `Config` has been removed, [use the constructor](./doc/config.rst)
- method `create` of class `RuleSet` has been removed, [use the constructor](./doc/custom_rules.rst)

### BC breaks; common internal classes

- method `getClassyElements` of class `TokensAnalyzer` parameter `$returnTraitsImports` has been removed; now always returns trait import information
- method `getSetDefinitionNames` of class `RuleSet` has been removed, use `RuleSets::getSetDefinitionNames()`
