<?php

/**
 * Script to analyze all fixers and their configurations for naming convention standardization
 */

declare(strict_types=1);

function findAllFixers(): array {
    $fixers = [];
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator(__DIR__ . '/src/Fixer')
    );
    
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php' && str_ends_with($file->getFilename(), 'Fixer.php')) {
            $relativePath = str_replace(__DIR__ . '/src/Fixer/', '', $file->getPathname());
            $fixerName = basename($file->getFilename(), '.php');
            
            $fixers[] = [
                'name' => $fixerName,
                'path' => $relativePath,
                'full_path' => $file->getPathname(),
            ];
        }
    }
    
    return $fixers;
}

function analyzeFixerContent(string $filePath): array {
    $content = file_get_contents($filePath);
    $analysis = [
        'has_configuration' => false,
        'options' => [],
        'implements_configurable' => false,
    ];
    
    // Check if implements ConfigurableFixerInterface
    if (strpos($content, 'implements ConfigurableFixerInterface') !== false) {
        $analysis['implements_configurable'] = true;
        $analysis['has_configuration'] = true;
    }
    
    // Extract configuration options
    if (preg_match('/createConfigurationDefinition.*?\{(.*?)\n    \}/s', $content, $matches)) {
        $configContent = $matches[1];
        
        // Extract FixerOptionBuilder calls
        preg_match_all('/new FixerOptionBuilder\(\'([^\']+)\',\s*\'([^\']*)\'\)/', $configContent, $optionMatches, PREG_SET_ORDER);
        
        foreach ($optionMatches as $match) {
            $analysis['options'][] = [
                'name' => $match[1],
                'description' => $match[2],
            ];
        }
    }
    
    return $analysis;
}

function categorizeFixers(array $fixers): array {
    $categories = [
        'ordered' => [],
        'no_prefix' => [],
        'not_prefix' => [],
        'single_prefix' => [],
        'space_whitespace' => [],
        'phpdoc' => [],
        'other' => [],
    ];
    
    foreach ($fixers as $fixer) {
        $name = $fixer['name'];
        $lowerName = strtolower($name);
        
        if (strpos($lowerName, 'ordered') === 0) {
            $categories['ordered'][] = $fixer;
        } elseif (strpos($lowerName, 'no') === 0) {
            $categories['no_prefix'][] = $fixer;
        } elseif (strpos($lowerName, 'not') === 0) {
            $categories['not_prefix'][] = $fixer;
        } elseif (strpos($lowerName, 'single') === 0) {
            $categories['single_prefix'][] = $fixer;
        } elseif (strpos($lowerName, 'space') !== false || strpos($lowerName, 'whitespace') !== false) {
            $categories['space_whitespace'][] = $fixer;
        } elseif (strpos($lowerName, 'phpdoc') === 0) {
            $categories['phpdoc'][] = $fixer;
        } else {
            $categories['other'][] = $fixer;
        }
    }
    
    return $categories;
}

// Main execution
$fixers = findAllFixers();
echo "Found " . count($fixers) . " fixers\n\n";

$categories = categorizeFixers($fixers);

foreach ($categories as $categoryName => $categoryFixers) {
    echo "=== " . strtoupper($categoryName) . " (" . count($categoryFixers) . " fixers) ===\n";
    
    foreach ($categoryFixers as $fixer) {
        $analysis = analyzeFixerContent($fixer['full_path']);
        
        echo "- {$fixer['name']}";
        if ($analysis['has_configuration']) {
            echo " [CONFIGURABLE]";
            if (!empty($analysis['options'])) {
                $optionNames = array_map(fn($opt) => $opt['name'], $analysis['options']);
                echo " Options: " . implode(', ', $optionNames);
            }
        }
        echo "\n";
    }
    echo "\n";
}

// Generate specific analysis for ordering fixers
echo "=== DETAILED ORDERING FIXER ANALYSIS ===\n";
foreach ($categories['ordered'] as $fixer) {
    $analysis = analyzeFixerContent($fixer['full_path']);
    echo "Fixer: {$fixer['name']}\n";
    echo "Path: {$fixer['path']}\n";
    
    if (!empty($analysis['options'])) {
        echo "Options:\n";
        foreach ($analysis['options'] as $option) {
            echo "  - {$option['name']}: {$option['description']}\n";
        }
    }
    echo "\n";
}

// Generate analysis for phpdoc order fixers
echo "=== PHPDOC ORDER FIXER ANALYSIS ===\n";
$phpdocOrderFixers = array_filter($fixers, fn($f) => strpos(strtolower($f['name']), 'order') !== false && strpos(strtolower($f['name']), 'phpdoc') !== false);

foreach ($phpdocOrderFixers as $fixer) {
    $analysis = analyzeFixerContent($fixer['full_path']);
    echo "Fixer: {$fixer['name']}\n";
    echo "Path: {$fixer['path']}\n";
    
    if (!empty($analysis['options'])) {
        echo "Options:\n";
        foreach ($analysis['options'] as $option) {
            echo "  - {$option['name']}: {$option['description']}\n";
        }
    }
    echo "\n";
}