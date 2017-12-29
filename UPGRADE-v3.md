UPGRADE GUIDE FROM 2.x to 3.0
=============================

This is guide for upgrade from version 2.x to 3.0 for using the CLI tool.

CLI options
-----------

| 2.x             | 3.0             | Description                                     | Note                                   |
| --------------- | --------------- | ----------------------------------------------- | -------------------------------------- |
| --diff-format   | --diff-format   | Type of differ                                  | Allowed value `sbd` was removed,       |
|                 |                 |                                                 | new default is `udiff`                 |

Changes to rules
----------------

### Removed rootless configuration

Rule                                 | Root option    | Note
------------------------------------ | -------------- | ----
`general_phpdoc_annotation_remove`   | `annotations`
`no_extra_consecutive_blank_lines`   | `tokens`
`no_spaces_around_offset`            | `positions`
`no_unneeded_control_parentheses`    | `statements`
`ordered_class_elements`             | `order`
`php_unit_construct`                 | `assertions`
`php_unit_dedicate_assert`           | `target`       | root option works differently than rootless configuration
`php_unit_strict`                    | `assertions`
`phpdoc_no_alias_tag`                | `replacements`
`phpdoc_return_self_reference`       | `replacements`
`random_api_migration`               | `replacements`
`single_class_element_per_statement` | `elements`
`visibility_required`                | `elements`

### Changed options

Rule | Option | Change
---- | ------ | ------
`binary_operator_spaces`           | `align_double_arrow` | option was removed, use `operators` instead
`binary_operator_spaces`           | `align_equals`       | option was removed use `operators` instead
`doctrine_annotation_spaces`       | `around_argument_assignments` | option was removed, use `before_argument_assignments` and `after_argument_assignments` instead
`doctrine_annotation_spaces`       | `around_array_assignments`    | option was removed, use `after_array_assignments_colon`, `after_array_assignments_equals`, `before_array_assignments_colon` and `before_array_assignments_equals` instead
`is_null`                          | `use_yoda_style` | option was removed, use `yoda_style` rule instead
`no_extra_consecutive_blank_lines` | `tokens`    | one of possible values, `useTrait`, was renamed to `use_trait`
`php_unit_dedicate_assert`         | `functions` | option was removed, use `target` instead
