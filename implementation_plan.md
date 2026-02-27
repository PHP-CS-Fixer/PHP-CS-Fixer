# PHP CS Fixer v4.0 Naming Standardization Implementation Plan

## Executive Summary

Based on systematic analysis of all 297 fixers, this plan addresses the most critical naming inconsistencies while maintaining backward compatibility and providing clear migration paths.

## 1. Priority Fixes - Ordering/Sorting Inconsistencies

### 1.1 Critical Issue: Mixed Ordering Patterns

**Current State:**
- 6 `ordered_*` fixers in ClassNotation/AttributeNotation/Import categories  
- 5 `*_order` fixers in Phpdoc category
- Inconsistent option naming across similar functionality

**Target State:**
All ordering fixers follow `ordered_*` pattern with standardized options.

### 1.2 Specific Renames Required

| Current Fixer | New Fixer | Category | Priority |
|---------------|-----------|----------|----------|
| `phpdoc_order` | `ordered_phpdoc_tags` | Critical | High |
| `phpdoc_param_order` | `ordered_phpdoc_params` | Critical | High |
| `phpdoc_types_order` | `ordered_phpdoc_types` | Critical | High |
| `phpdoc_order_by_value` | `ordered_phpdoc_by_value` | Critical | High |
| `phpdoc_var_annotation_correct_order` | `ordered_phpdoc_var_annotation` | Critical | High |

### 1.3 Option Standardization for Ordering Fixers

**Current Inconsistencies:**
```php
// OrderedTypesFixer
['sort_algorithm' => 'alpha', 'null_adjustment' => 'always_first', 'case_sensitive' => false]

// OrderedInterfacesFixer  
['order' => 'alpha', 'direction' => 'ascend', 'case_sensitive' => false]

// PhpdocOrderFixer
['order' => ['param', 'return', 'throws']]
```

**Proposed Standard:**
```php
// All ordering fixers should support:
[
    'sort_algorithm' => 'alpha|length|none|custom',  // Primary sort method
    'order' => ['custom', 'array', 'values'],        // When algorithm=custom
    'direction' => 'asc|desc',                       // Sort direction  
    'case_sensitive' => bool,                        // Case sensitivity
    'null_position' => 'first|last|natural',        // Special handling (types only)
]
```

## 2. Secondary Fixes - Negation Outliers

### 2.1 NotOperator Fixers
| Current | Proposed | Rationale |
|---------|----------|-----------|
| `not_operator_with_space` | `no_space_around_not_operator` | Consistency with `no_*` pattern |
| `not_operator_with_successor_space` | `no_space_after_not_operator` | Consistency with `no_*` pattern |

## 3. Implementation Strategy

### 3.1 Phase 1: Foundation (v3.x preparation)
1. **Add Deprecation Infrastructure**
   ```php
   // In FixerFactory
   private const DEPRECATED_FIXERS = [
       'phpdoc_order' => [
           'replacement' => 'ordered_phpdoc_tags',
           'since' => '3.x',
           'removal' => '4.0',
       ],
       // ... more deprecations
   ];
   ```

2. **Create Fixer Aliases**
   ```php
   // Support both old and new names during transition
   private const FIXER_ALIASES = [
       'phpdoc_order' => 'ordered_phpdoc_tags',
       'phpdoc_param_order' => 'ordered_phpdoc_params',
       // ...
   ];
   ```

### 3.2 Phase 2: Implementation (v4.0)
1. **Rename Actual Fixer Classes**
   - Move files and update namespaces
   - Update all internal references
   - Maintain aliases for backward compatibility

2. **Standardize Configuration Options**
   - Implement standard option patterns
   - Add option aliases for old names with deprecation warnings
   - Update documentation

3. **Update Tests and Documentation**
   - Rename test files and update test cases
   - Update all documentation examples
   - Create migration guide

### 3.3 Phase 3: Migration Tools (v4.0)
1. **Configuration Migration Script**
   ```bash
   php-cs-fixer migrate-config .php-cs-fixer.php --to-version=4.0
   ```

2. **Automated Refactoring**
   - Scan configuration files for deprecated names
   - Automatically replace with new names
   - Update option names and values

## 4. Detailed Implementation

### 4.1 Example: PhpdocOrderFixer → OrderedPhpdocTagsFixer

**Current Implementation:**
```php
// src/Fixer/Phpdoc/PhpdocOrderFixer.php
final class PhpdocOrderFixer extends AbstractFixer implements ConfigurableFixerInterface
{
    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('order', 'Sequence in which annotations in PHPDoc should be ordered.'))
                ->setAllowedTypes(['string[]'])
                ->setDefault(['param', 'throws', 'return'])
                ->getOption(),
        ]);
    }
}
```

**New Implementation:**
```php
// src/Fixer/Phpdoc/OrderedPhpdocTagsFixer.php  
final class OrderedPhpdocTagsFixer extends AbstractFixer implements ConfigurableFixerInterface
{
    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('sort_algorithm', 'How PHPDoc tags should be sorted.'))
                ->setAllowedValues(['alpha', 'custom', 'none'])
                ->setDefault('custom')
                ->getOption(),
            (new FixerOptionBuilder('order', 'Custom order when sort_algorithm is custom.'))
                ->setAllowedTypes(['string[]'])
                ->setDefault(['param', 'throws', 'return'])
                ->getOption(),
            (new FixerOptionBuilder('case_sensitive', 'Whether sorting should be case sensitive.'))
                ->setAllowedTypes(['bool'])
                ->setDefault(false)
                ->getOption(),
        ]);
    }
    
    // Support legacy configuration
    public function configure(array $configuration): void
    {
        // Handle legacy 'order' option when no 'sort_algorithm' specified
        if (isset($configuration['order']) && !isset($configuration['sort_algorithm'])) {
            $configuration['sort_algorithm'] = 'custom';
        }
        
        parent::configure($configuration);
    }
}
```

**Alias Registration:**
```php
// src/FixerFactory.php (or appropriate location)
private const FIXER_ALIASES = [
    'phpdoc_order' => OrderedPhpdocTagsFixer::class,
];

public function registerBuiltInFixers(): self
{
    // Register new fixers
    $this->registerFixer(new OrderedPhpdocTagsFixer());
    
    // Register aliases with deprecation warnings
    foreach (self::FIXER_ALIASES as $oldName => $newClass) {
        $this->registerFixerAlias($oldName, $newClass);
    }
    
    return $this;
}

private function registerFixerAlias(string $alias, string $fixerClass): void
{
    // Emit deprecation warning when old name is used
    trigger_error(
        sprintf(
            'Fixer "%s" is deprecated, use "%s" instead.',
            $alias,
            $this->getFixerNameFromClass($fixerClass)
        ),
        E_USER_DEPRECATED
    );
    
    // Register alias
    $this->fixers[$alias] = new $fixerClass();
}
```

### 4.2 Migration Script Implementation

```php
#!/usr/bin/env php
<?php

/**
 * Configuration migration script for PHP CS Fixer v4.0
 */

class ConfigMigrator
{
    private const FIXER_RENAMES = [
        'phpdoc_order' => 'ordered_phpdoc_tags',
        'phpdoc_param_order' => 'ordered_phpdoc_params',
        'phpdoc_types_order' => 'ordered_phpdoc_types',
        'phpdoc_order_by_value' => 'ordered_phpdoc_by_value',
        'not_operator_with_space' => 'no_space_around_not_operator',
        'not_operator_with_successor_space' => 'no_space_after_not_operator',
    ];
    
    private const OPTION_RENAMES = [
        'ordered_phpdoc_tags' => [
            // Legacy: direct 'order' array becomes 'custom' algorithm with 'order' option
            'order' => ['sort_algorithm' => 'custom', 'order' => null], // null means keep value
        ],
    ];
    
    public function migrateFile(string $configPath): string
    {
        $content = file_get_contents($configPath);
        
        // Replace fixer names
        foreach (self::FIXER_RENAMES as $old => $new) {
            $content = preg_replace(
                "/(['\"])" . preg_quote($old, '/') . "\\1/",
                '$1' . $new . '$1',
                $content
            );
        }
        
        // TODO: Handle complex option renames
        // This would require parsing the PHP array structure
        
        return $content;
    }
}

// Usage
if ($argc < 2) {
    echo "Usage: php migrate-config.php <config-file>\n";
    exit(1);
}

$migrator = new ConfigMigrator();
$newContent = $migrator->migrateFile($argv[1]);

// Backup original
copy($argv[1], $argv[1] . '.backup');

// Write migrated version
file_put_contents($argv[1], $newContent);

echo "Configuration migrated successfully.\n";
echo "Backup saved as: {$argv[1]}.backup\n";
```

## 5. Testing Strategy

### 5.1 Backward Compatibility Tests
```php
class BackwardCompatibilityTest extends TestCase
{
    public function testDeprecatedFixerNamesStillWork(): void
    {
        $factory = new FixerFactory();
        
        // Old names should still resolve to fixers
        $fixer = $factory->getFixer('phpdoc_order');
        self::assertInstanceOf(OrderedPhpdocTagsFixer::class, $fixer);
    }
    
    public function testDeprecatedOptionsStillWork(): void
    {
        $fixer = new OrderedPhpdocTagsFixer();
        
        // Legacy configuration should work with deprecation warning
        $this->expectDeprecation();
        $fixer->configure(['order' => ['param', 'return']]);
        
        // Should be converted to new format internally
        $config = $fixer->getConfiguration();
        self::assertEquals('custom', $config['sort_algorithm']);
        self::assertEquals(['param', 'return'], $config['order']);
    }
}
```

### 5.2 Migration Tests
```php
class MigrationTest extends TestCase
{
    public function testConfigFileMigration(): void
    {
        $oldConfig = "<?php return ['rules' => ['phpdoc_order' => true]];";
        $expected = "<?php return ['rules' => ['ordered_phpdoc_tags' => true]];";
        
        $migrator = new ConfigMigrator();
        $result = $migrator->migrateContent($oldConfig);
        
        self::assertEquals($expected, $result);
    }
}
```

## 6. Documentation Updates

### 6.1 Migration Guide
Create `UPGRADE-v4.md` section:

```markdown
## Fixer Renames

Several fixers have been renamed for consistency:

| Old Name | New Name | Migration |
|----------|----------|-----------|
| `phpdoc_order` | `ordered_phpdoc_tags` | Automatic |
| `phpdoc_param_order` | `ordered_phpdoc_params` | Automatic |

### Automated Migration
```bash
php-cs-fixer migrate-config .php-cs-fixer.php --to-version=4.0
```

### Manual Migration
```php
// Before (v3.x)
'phpdoc_order' => ['order' => ['param', 'return', 'throws']]

// After (v4.0)  
'ordered_phpdoc_tags' => [
    'sort_algorithm' => 'custom',
    'order' => ['param', 'return', 'throws']
]
```

## 7. Timeline

- **v3.x (Current)**: Add deprecation warnings and aliases
- **v4.0-beta**: Implement renames with full backward compatibility
- **v4.0-stable**: Official release with migration guide
- **v4.1+**: Remove deprecation warnings, keep aliases
- **v5.0**: Remove old aliases completely

## 8. Benefits

1. **Consistency**: All ordering fixers follow same pattern
2. **Predictability**: Users can guess fixer names
3. **Maintainability**: Clearer code organization
4. **Extensibility**: New fixers follow established patterns
5. **User Experience**: Better tooling support and discoverability

This implementation plan provides a comprehensive, backward-compatible approach to standardizing PHP CS Fixer naming conventions while minimizing disruption to existing users.