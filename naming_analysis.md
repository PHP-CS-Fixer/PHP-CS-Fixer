# PHP CS Fixer Naming Convention Analysis

## Current Naming Inconsistencies

### 1. Ordering/Sorting Fixers

**Inconsistent naming patterns:**
- `ordered_*` (ClassNotation): `OrderedTypesFixer`, `OrderedInterfacesFixer`, `OrderedClassElementsFixer`, `OrderedTraitsFixer`, `OrderedAttributesFixer`, `OrderedImportsFixer`
- `*_order` (Phpdoc): `PhpdocOrderFixer`, `PhpdocParamOrderFixer`, `PhpdocTypesOrderFixer`, `PhpdocOrderByValueFixer`
- `*_order` (Other): `FopenFlagOrderFixer`, `PhpUnitDataProviderMethodOrderFixer`

**Option naming inconsistencies:**
- `sort_algorithm` (OrderedTypesFixer) vs `order` (OrderedInterfacesFixer) vs `order` (PhpdocOrderFixer)
- `case_sensitive` (consistent across multiple fixers) ✓
- `direction` (OrderedInterfacesFixer) vs `null_adjustment` (OrderedTypesFixer)

### 2. Negation Prefixes

**Current patterns:**
- `No*` prefix (vast majority): `NoAliasFunctionsFixer`, `NoBlankLinesAfterClassOpeningFixer`, etc.
- `Not*` prefix (few cases): `NotOperatorWithSpaceFixer`, `NotOperatorWithSuccessorSpaceFixer`

### 3. Quantity Descriptors

**Current patterns:**
- `Single*` prefix: `SingleBlankLineAtEofFixer`, `SingleClassElementPerStatementFixer`, etc.
- `Multiple*` in negative form: `NoMultipleStatementsPerLineFixer`
- `*Singleline*` in negative form: `NoSinglelineWhitespaceBeforeSemicolonsFixer`, `NoTrailingCommaInSinglelineArrayFixer`

### 4. Phpdoc vs PhpDoc Casing

**Inconsistent casing:**
- `Phpdoc*` (lowercase 'd'): `PhpdocOrderFixer`, `PhpdocAnnotationWithoutDotFixer`, etc.
- `PhpDoc*` (camelCase): Should be standardized

### 5. Spacing/Whitespace Terminology

**Inconsistent terms:**
- `*Whitespace*`: `NoTrailingWhitespaceFixer`, `NoWhitespaceBeforeCommaInArrayFixer`
- `*Space*`: `NoSpaceAroundDoubleColonFixer`, `SingleSpaceAfterConstructFixer`

## Option Naming Inconsistencies

### Common Configuration Options

1. **Sorting/Ordering:**
   - `sort_algorithm` vs `order` vs `direction`
   - Values: `'alpha'|'none'` vs `'ascend'|'descend'` vs custom arrays

2. **Case Sensitivity:**
   - `case_sensitive` (consistent) ✓

3. **Positioning/Adjustment:**
   - `null_adjustment` vs `direction`

## Proposed Naming Conventions

### 1. Fixer Names

**Ordering/Sorting fixers:**
- Standardize on `ordered_*` pattern for consistency
- Examples: `ordered_attributes`, `ordered_interfaces`, `ordered_types`
- Migrate phpdoc fixers: `phpdoc_order` → `ordered_phpdoc_tags`

**Negation:**
- Standardize on `no_*` prefix
- Examples: `no_alias_functions`, `no_trailing_whitespace`

**Quantity:**
- Use `single_*` for singular requirements
- Use `no_multiple_*` for preventing multiple occurrences

**Terminology:**
- Standardize on `space` instead of `whitespace` where referring to single characters
- Use `whitespace` for general whitespace handling

### 2. Option Names

**Sorting/Ordering options:**
- `sort_algorithm`: `'alpha'|'length'|'none'|'custom'`
- `order`: `string[]` (for custom ordering)
- `direction`: `'asc'|'desc'` (when applicable)
- `case_sensitive`: `bool`

**Position/Adjustment options:**
- `null_position`: `'first'|'last'|'none'`
- `position`: generic positioning option

## Implementation Plan for v4.0

### Phase 1: Analysis & Documentation
- [x] Identify all naming inconsistencies
- [ ] Document breaking changes for migration guide
- [ ] Create mapping of old → new names

### Phase 2: Deprecation Strategy
- [ ] Add deprecation warnings for old fixer names
- [ ] Implement alias system for backward compatibility
- [ ] Update documentation with new conventions

### Phase 3: Implementation
- [ ] Rename fixers following new conventions
- [ ] Standardize option names
- [ ] Update all references in codebase
- [ ] Update tests and documentation

### Phase 4: Migration Tools
- [ ] Create automated migration script for config files
- [ ] Update VS Code extension and other tooling
- [ ] Provide clear migration documentation

## Breaking Changes Summary

### Fixer Renames
1. Phpdoc order fixers: `phpdoc_order` → `ordered_phpdoc_tags`
2. Not operator fixers: `not_operator_*` → `no_operator_*` (if applicable)
3. Whitespace terminology standardization

### Option Renames
1. `sort_algorithm` standardization across all ordering fixers
2. `null_adjustment` → `null_position`
3. `direction` → `sort_direction` (for clarity)

## Benefits of Standardization

1. **Consistency**: Easier to remember and discover fixers
2. **Predictability**: Similar fixers follow same naming patterns  
3. **Maintainability**: Clearer codebase organization
4. **User Experience**: More intuitive configuration
5. **Tooling**: Better IDE support and autocomplete