UPGRADE GUIDE FROM 2.x to 3.0
=============================

This is guide for upgrade from version 2.x to 3.0 for using the CLI tool.

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
