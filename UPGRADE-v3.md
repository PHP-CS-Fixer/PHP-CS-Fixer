UPGRADE GUIDE FROM 2.x to 3.0
=============================

This is guide for upgrade from version 2.x to 3.0 for using the CLI tool.

Changes to rules
----------------

### Renamed rules

Old name | New name | Note
-------- | -------- | ----
`blank_line_before_return`                      | `blank_line_before_statement`                 | use configuration `['statements' => ['return']]`
`hash_to_slash_comment`                         | `single_line_comment_style`                   | use configuration `['comment_types' => ['hash']]`
`method_separation`                             | `class_attributes_separation`                 | use configuration `['elements' => ['method']]`
`no_extra_consecutive_blank_lines`              | `no_extra_blank_lines`                        |
`pre_increment`                                 | `increment_style`                             | use configuration `['style' => 'pre']`
