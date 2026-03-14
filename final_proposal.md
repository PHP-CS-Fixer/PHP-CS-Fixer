# PHP CS Fixer Naming Convention Modernization - Final Proposal

## Summary

This proposal addresses the issue raised in #7461 to modernize and standardize PHP CS Fixer's naming conventions for v4.0. Based on comprehensive analysis of all 297 fixers, specific inconsistencies have been identified and solutions proposed.

## Key Findings

### Current State Analysis
- **297 total fixers** analyzed across all categories
- **11 ordering/sorting fixers** with inconsistent naming patterns
- **2 negation outliers** using `not_*` instead of standard `no_*` prefix  
- **Mixed terminology** in space/whitespace handling
- **Inconsistent option naming** across similar functionality

### Critical Issues Identified

1. **Ordering Fixer Inconsistency**: 
   - 6 fixers use `ordered_*` pattern (ClassNotation, AttributeNotation, Import)
   - 5 fixers use `*_order` pattern (Phpdoc)

2. **Option Naming Inconsistency**:
   - `sort_algorithm` vs `order` vs missing standardization
   - Inconsistent value patterns across similar fixers

3. **Negation Pattern Outliers**:
   - `NotOperatorWithSpaceFixer` and `NotOperatorWithSuccessorSpaceFixer` break `no_*` convention

## Recommended Changes for v4.0

### Priority 1: Ordering Fixer Standardization

**Rename these 5 PHPDoc fixers to follow `ordered_*` pattern:**

| Current Name | New Name | Impact |
|-------------|----------|--------|
| `phpdoc_order` | `ordered_phpdoc_tags` | High usage |
| `phpdoc_param_order` | `ordered_phpdoc_params` | Medium usage |
| `phpdoc_types_order` | `ordered_phpdoc_types` | Medium usage |
| `phpdoc_order_by_value` | `ordered_phpdoc_by_value` | Low usage |
| `phpdoc_var_annotation_correct_order` | `ordered_phpdoc_var_annotation` | Low usage |

**Standardize options across all ordering fixers:**
```php
[
    'sort_algorithm' => 'alpha|custom|none',  // Primary sort method
    'order' => string[],                      // Custom order array  
    'case_sensitive' => bool,                 // Case sensitivity
    'direction' => 'asc|desc',               // Sort direction (when applicable)
]
```

### Priority 2: Negation Pattern Fixes

**Rename for consistency:**
- `not_operator_with_space` → `no_space_around_not_operator`
- `not_operator_with_successor_space` → `no_space_after_not_operator`

### Priority 3: Option Standardization

**Implement consistent option names:**
- `null_adjustment` → `null_position` (for type ordering)
- Standardize `sort_algorithm` across all ordering fixers
- Maintain `case_sensitive` consistency (already good)

## Implementation Strategy

### Backward Compatibility Approach

1. **Maintain aliases** for all renamed fixers during v4.x lifecycle
2. **Emit deprecation warnings** when old names are used
3. **Support legacy option formats** with automatic conversion
4. **Provide migration tooling** for automated config updates

### Migration Path

**For users:**
```bash
# Automated migration
php-cs-fixer migrate-config .php-cs-fixer.php --to-version=4.0

# Manual updates
# Before:
'phpdoc_order' => ['order' => ['param', 'return', 'throws']]

# After:  
'ordered_phpdoc_tags' => [
    'sort_algorithm' => 'custom',
    'order' => ['param', 'return', 'throws']
]
```

**For core development:**
1. Create new fixer classes with standardized names/options
2. Keep old classes as deprecated aliases
3. Update all documentation and examples
4. Implement comprehensive test coverage

### Implementation Timeline

- **v3.x**: Add deprecation warnings and preparation
- **v4.0-beta**: Implement renames with full backward compatibility
- **v4.0**: Official release with migration guide
- **v5.0**: Remove deprecated aliases

## Benefits

### For Users
- **Predictable naming**: Easier to discover and remember fixer names
- **Consistent configuration**: Same option patterns across similar fixers
- **Better tooling**: Improved IDE autocomplete and validation
- **Clear migration path**: Automated tools and detailed documentation

### For Maintainers
- **Cleaner codebase**: Consistent patterns throughout
- **Easier contribution**: Clear conventions for new fixers
- **Better organization**: Logical grouping and naming
- **Future-proof**: Extensible patterns for new functionality

## Risk Assessment

### Low Risk Changes
- **Option standardization**: Minimal user impact with alias support
- **Negation pattern fixes**: Very low usage fixers
- **Documentation updates**: No breaking changes

### Medium Risk Changes  
- **PHPDoc fixer renames**: Moderate usage, but with full backward compatibility
- **New configuration patterns**: Requires careful migration handling

### Mitigation Strategies
- **Comprehensive testing**: Both unit and integration tests
- **Extended deprecation period**: v4.x → v5.0 timeline for full removal
- **Clear communication**: Migration guides, release notes, community outreach
- **Automated tooling**: Migration scripts to reduce manual effort

## Conclusion

This proposal provides a comprehensive solution to standardize PHP CS Fixer naming conventions while maintaining backward compatibility and minimizing user disruption. The phased approach allows for careful migration and ensures the project evolves toward greater consistency and usability.

The changes directly address the core issues raised in #7461 and implement the suggestion from @Wirone to "propose fixers' mass renaming when I find time for it." This modernization effort will significantly improve the developer experience and establish clear conventions for future development.

## Next Steps

1. **Community review** of this proposal
2. **Proof-of-concept implementation** (already created)
3. **Detailed implementation planning** for specific fixers
4. **Migration tooling development**
5. **Documentation and communication planning**

This proposal is ready for community feedback and can serve as the foundation for implementation in PHP CS Fixer v4.0.