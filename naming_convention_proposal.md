# PHP CS Fixer v4.0 Naming Convention Proposal

## Overview

This document proposes standardized naming conventions for PHP CS Fixer v4.0 to address current inconsistencies in fixer names and configuration options.

## 1. Fixer Naming Conventions

### 1.1 General Principles

- Use **snake_case** for all fixer names (consistent with current majority)
- Use descriptive, consistent prefixes for related functionality
- Prioritize clarity and predictability over brevity

### 1.2 Specific Patterns

#### Ordering/Sorting Fixers
**Current inconsistencies:**
- `ordered_*` (ClassNotation): `ordered_types`, `ordered_interfaces`, etc.
- `*_order` (Phpdoc): `phpdoc_order`, `phpdoc_param_order`, etc.

**Proposed standard:** `ordered_*`
- `ordered_attributes` ✓ (already correct)
- `ordered_class_elements` ✓ (already correct)  
- `ordered_imports` ✓ (already correct)
- `ordered_interfaces` ✓ (already correct)
- `ordered_traits` ✓ (already correct)
- `ordered_types` ✓ (already correct)
- `phpdoc_order` → `ordered_phpdoc_tags`
- `phpdoc_param_order` → `ordered_phpdoc_params`
- `phpdoc_types_order` → `ordered_phpdoc_types`
- `phpdoc_order_by_value` → `ordered_phpdoc_by_value`

#### Negation Fixers
**Current:** Mixed `no_*` and `not_*` prefixes

**Proposed standard:** `no_*`
- `not_operator_with_space` → `no_space_around_not_operator`
- `not_operator_with_successor_space` → `no_space_after_not_operator`

#### Quantity/Cardinality Fixers
**Current:** Mixed patterns for single/multiple

**Proposed standard:**
- `single_*` for enforcing singular items
- `no_multiple_*` for preventing multiple items
- Examples: `single_blank_line_at_eof`, `no_multiple_statements_per_line`

#### Spacing/Whitespace Terminology
**Current:** Mixed `space` and `whitespace` usage

**Proposed standard:**
- `space` for single character spaces and specific positioning
- `whitespace` for general whitespace handling (tabs, spaces, newlines)
- Examples: `no_space_around_double_colon`, `no_trailing_whitespace`

### 1.3 PHPDoc Casing
**Current:** Inconsistent `Phpdoc` vs `PhpDoc`

**Proposed standard:** `phpdoc_*` (lowercase, consistent with snake_case)

## 2. Configuration Option Naming

### 2.1 Sorting/Ordering Options

#### Primary Sort Option
**Option name:** `sort_algorithm`
**Values:** `'alpha'|'length'|'none'|'custom'`
**Description:** Primary sorting method

#### Custom Order Option  
**Option name:** `order`
**Type:** `string[]`
**Description:** Custom ordering when `sort_algorithm` is `'custom'`

#### Sort Direction
**Option name:** `direction` 
**Values:** `'asc'|'desc'`
**Description:** Sort direction (ascending/descending)

#### Case Sensitivity
**Option name:** `case_sensitive`
**Type:** `bool`
**Description:** Whether sorting is case-sensitive

#### Special Positioning
**Option name:** `null_position` (for types)
**Values:** `'first'|'last'|'natural'`
**Description:** Special handling for null types

### 2.2 Standardized Option Patterns

```php
// Example: ordered_types fixer
[
    'sort_algorithm' => 'alpha',  // 'alpha'|'length'|'none'|'custom'
    'direction' => 'asc',         // 'asc'|'desc'  
    'case_sensitive' => false,    // bool
    'null_position' => 'first',   // 'first'|'last'|'natural'
]

// Example: ordered_phpdoc_tags fixer  
[
    'sort_algorithm' => 'custom', // 'alpha'|'custom'|'none'
    'order' => ['param', 'return', 'throws'], // string[]
    'case_sensitive' => false,    // bool
]
```

## 3. Migration Strategy

### 3.1 Backward Compatibility
- Maintain aliases for old fixer names during v4.x
- Emit deprecation warnings for old names
- Support old option names with deprecation warnings

### 3.2 Configuration Migration
- Provide automated migration tool for `.php-cs-fixer.php` files
- Update documentation with migration examples
- Create mapping table for all renamed fixers/options

### 3.3 Implementation Phases

#### Phase 1: Preparation (v3.x)
- [ ] Add deprecation warnings for fixers to be renamed
- [ ] Document upcoming changes in UPGRADE.md
- [ ] Create fixer aliases in preparation

#### Phase 2: Implementation (v4.0)
- [ ] Rename fixers following new conventions
- [ ] Standardize option names across similar fixers
- [ ] Update all internal references
- [ ] Update documentation and examples

#### Phase 3: Cleanup (v4.1+)
- [ ] Remove deprecated aliases (planned for v5.0)
- [ ] Remove backward compatibility for old option names

## 4. Complete Rename Mapping

### 4.1 Fixer Renames

| Current Name | New Name | Rationale |
|-------------|----------|-----------|
| `phpdoc_order` | `ordered_phpdoc_tags` | Consistency with ordered_* pattern |
| `phpdoc_param_order` | `ordered_phpdoc_params` | Consistency with ordered_* pattern |
| `phpdoc_types_order` | `ordered_phpdoc_types` | Consistency with ordered_* pattern |
| `phpdoc_order_by_value` | `ordered_phpdoc_by_value` | Consistency with ordered_* pattern |
| `not_operator_with_space` | `no_space_around_not_operator` | Consistency with no_* pattern |
| `not_operator_with_successor_space` | `no_space_after_not_operator` | Consistency with no_* pattern |

### 4.2 Option Renames

| Fixer | Current Option | New Option | Rationale |
|-------|---------------|------------|-----------|
| `ordered_interfaces` | `order` | `sort_algorithm` | Standardization |
| `ordered_interfaces` | `direction` | `direction` | ✓ (keep) |
| `ordered_types` | `null_adjustment` | `null_position` | Clarity |
| `phpdoc_order` | `order` | `order` | ✓ (keep, but standardize type) |

## 5. Benefits

### 5.1 User Experience
- **Predictable naming:** Users can guess fixer names
- **Consistent options:** Similar fixers use same option names
- **Better discoverability:** Logical grouping of related fixers

### 5.2 Maintainability  
- **Clearer codebase:** Consistent patterns throughout
- **Easier testing:** Standardized test patterns
- **Better tooling:** IDE autocomplete and validation

### 5.3 Future-proofing
- **Extensible patterns:** New fixers follow established conventions
- **Migration path:** Clear upgrade process for users
- **Community adoption:** Easier for contributors to follow patterns

## 6. Implementation Details

### 6.1 Fixer Aliases
```php
// In FixerFactory or similar
private const FIXER_ALIASES = [
    'phpdoc_order' => 'ordered_phpdoc_tags',
    'phpdoc_param_order' => 'ordered_phpdoc_params',
    // ... more aliases
];
```

### 6.2 Option Aliases  
```php
// In configurable fixers
protected function normalizeConfiguration(array $configuration): array
{
    // Handle legacy option names
    if (isset($configuration['order']) && !isset($configuration['sort_algorithm'])) {
        $configuration['sort_algorithm'] = 'custom';
        trigger_deprecation(...);
    }
    
    return parent::normalizeConfiguration($configuration);
}
```

### 6.3 Migration Tool
```bash
# Proposed CLI command
php-cs-fixer migrate-config .php-cs-fixer.php --target-version=4.0
```

## 7. Next Steps

1. **Community Review:** Gather feedback on proposed conventions
2. **Proof of Concept:** Implement sample renames to validate approach  
3. **Documentation:** Create detailed migration guide
4. **Implementation:** Begin phased rollout in v4.0 development

This proposal aims to create a more consistent, predictable, and maintainable naming system for PHP CS Fixer while providing clear migration paths for existing users.