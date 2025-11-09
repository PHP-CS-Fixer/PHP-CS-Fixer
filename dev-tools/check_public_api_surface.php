#!/usr/bin/env php
<?php

declare(strict_types=1);

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

error_reporting(\E_ALL & ~\E_DEPRECATED & ~\E_USER_DEPRECATED);

require __DIR__.'/../vendor/autoload.php';

use Symfony\Component\Finder\Finder;

/**
 * Validates that public interface does not expose internal types.
 *
 * This script checks that:
 * - Public methods in non-internal classes don't return @internal types
 * - Public methods in non-internal classes don't accept @internal types as parameters
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class check_public_api_surface
{
    /**
     * Known violations that should be fixed in the future.
     * Format: 'ClassName::methodName() => InternalType'.
     *
     * @var array<string, true>
     */
    private const KNOWN_VIOLATIONS = [
        'PhpCsFixer\DocBlock\Annotation::__construct() => PhpCsFixer\Tokenizer\Analyzer\Analysis\NamespaceAnalysis' => true,
        'PhpCsFixer\DocBlock\Annotation::__construct() => PhpCsFixer\Tokenizer\Analyzer\Analysis\NamespaceUseAnalysis' => true,
    ];

    private array $violations = [];
    private array $internalClasses = [];
    private array $classFiles = [];

    public function run(): int
    {
        $this->findAllClasses();
        $this->identifyInternalClasses();
        $this->checkPublicMethods();

        return $this->report();
    }

    private function findAllClasses(): void
    {
        $finder = new Finder();
        $finder->files()->name('*.php')->in(__DIR__.'/../src')->exclude('Fixer/Internal');

        foreach ($finder as $file) {
            $content = $file->getContents();

            // Extract namespace and class name
            if (preg_match('/namespace\s+([^;]+);/', $content, $nsMatch)
                && preg_match('/(class|interface|trait)\s+(\w+)/', $content, $classMatch)) {
                $fqcn = $nsMatch[1].'\\'.$classMatch[2];
                $this->classFiles[$fqcn] = $file->getPathname();
            }
        }

        echo 'Found '.count($this->classFiles)." classes/interfaces/traits\n";
    }

    private function identifyInternalClasses(): void
    {
        foreach ($this->classFiles as $fqcn => $file) {
            $content = file_get_contents($file);

            // Check if class/interface/trait is marked as @internal
            // Look for @internal in the docblock before class/interface/trait keyword
            if (preg_match('/\/\*\*.*?@internal.*?\*\/\s*(final\s+)?(abstract\s+)?(class|interface|trait)\s+\w+/s', $content)) {
                $this->internalClasses[$fqcn] = true;
            }
        }

        echo 'Found '.count($this->internalClasses)." internal classes/interfaces/traits\n\n";
    }

    private function checkPublicMethods(): void
    {
        foreach ($this->classFiles as $fqcn => $file) {
            // Skip if class itself is internal
            if (isset($this->internalClasses[$fqcn])) {
                continue;
            }

            // Skip if class doesn't exist (e.g., it's not autoloadable)
            if (!class_exists($fqcn) && !interface_exists($fqcn) && !trait_exists($fqcn)) {
                continue;
            }

            $content = file_get_contents($file);

            // Find all public methods
            if (preg_match_all('/\/\*\*.*?\*\/\s+public\s+function\s+(\w+)/s', $content, $matches, \PREG_OFFSET_CAPTURE)) {
                foreach ($matches[0] as $idx => $match) {
                    $methodName = $matches[1][$idx][0];
                    $docBlockAndSignature = $match[0];

                    // Skip if method itself is marked as @internal
                    if (str_contains($docBlockAndSignature, '@internal')) {
                        continue;
                    }

                    // Extract types from @return and @param annotations
                    $this->checkMethodTypes($fqcn, $methodName, $docBlockAndSignature, $file);
                }
            }
        }
    }

    private function checkMethodTypes(string $className, string $methodName, string $docBlock, string $file): void
    {
        // Extract @return and @param type hints
        if (preg_match_all('/@(?:return|param)\s+([^\s*]+)/', $docBlock, $matches)) {
            foreach ($matches[1] as $typeHint) {
                $types = $this->extractTypes($typeHint);

                foreach ($types as $type) {
                    if (isset($this->internalClasses[$type])) {
                        $violationKey = sprintf('%s::%s() => %s', $className, $methodName, $type);

                        // Skip known violations
                        if (isset(self::KNOWN_VIOLATIONS[$violationKey])) {
                            continue;
                        }

                        $this->violations[] = sprintf(
                            "Public method %s::%s() exposes internal type %s\n  in %s",
                            $className,
                            $methodName,
                            $type,
                            str_replace(__DIR__.'/../', '', $file)
                        );
                    }
                }
            }
        }
    }

    /**
     * Extract concrete class/interface names from type hint.
     *
     * @return list<string>
     */
    private function extractTypes(string $typeHint): array
    {
        $types = [];

        // Split union types
        $parts = preg_split('/[|&]/', $typeHint);

        foreach ($parts as $part) {
            $part = trim($part);

            // Remove array notation
            $part = preg_replace('/\[\]$/', '', $part);

            // Remove generic notation (e.g., array<...>, list<...>)
            if (preg_match('/^(array|list|iterable)<(.+)>$/', $part, $match)) {
                // Recursively extract types from generic
                $types = array_merge($types, $this->extractTypes($match[2]));

                continue;
            }

            // Skip built-in types
            if (in_array($part, [
                'void', 'null', 'mixed', 'never',
                'string', 'int', 'float', 'bool', 'array', 'object', 'callable', 'iterable', 'resource',
                'self', 'static', 'parent', '$this',
                'true', 'false',
            ], true)) {
                continue;
            }

            // Skip empty or invalid types
            if ('' === $part || str_starts_with($part, '$')) {
                continue;
            }

            // Remove leading backslash
            $part = ltrim($part, '\\');

            // Check if it's a class that we know about
            if (isset($this->classFiles[$part])) {
                $types[] = $part;
            } else {
                // Try to find it in our class files (might be a short name)
                foreach ($this->classFiles as $fqcn => $file) {
                    if (str_ends_with($fqcn, '\\'.$part)) {
                        $types[] = $fqcn;

                        break;
                    }
                }
            }
        }

        return array_unique($types);
    }

    private function report(): int
    {
        if ([] === $this->violations) {
            echo "\033[0;32m✓ No public interface violations detected.\033[0m\n";

            return 0;
        }

        echo "\033[97;41mPublic interface violations detected:\033[0m\n\n";
        foreach ($this->violations as $violation) {
            echo "  ❌ {$violation}\n\n";
        }

        return 1;
    }
}

$checker = new PublicApiSurfaceChecker();

exit($checker->run());
