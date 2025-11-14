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

namespace PhpCsFixer\PHPStan\Rules;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassMethodNode;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * Validates that public methods in non-internal classes do not expose internal types.
 *
 * @implements Rule<InClassMethodNode>
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class NoInternalTypesInPublicApiRule implements Rule
{
    /**
     * Known violations that should be fixed in the future.
     *
     * @var array<string, true>
     */
    private const KNOWN_VIOLATIONS = [
        'PhpCsFixer\DocBlock\Annotation::__construct():PhpCsFixer\Tokenizer\Analyzer\Analysis\NamespaceAnalysis' => true,
        'PhpCsFixer\DocBlock\Annotation::__construct():PhpCsFixer\Tokenizer\Analyzer\Analysis\NamespaceUseAnalysis' => true,
    ];

    private ReflectionProvider $reflectionProvider;

    public function __construct(ReflectionProvider $reflectionProvider)
    {
        $this->reflectionProvider = $reflectionProvider;
    }

    public function getNodeType(): string
    {
        return InClassMethodNode::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $methodReflection = $node->getMethodReflection();
        $classReflection = $node->getClassReflection();

        // Skip if class is internal (check PHPDoc @internal annotation)
        if ($this->isInternal($classReflection)) {
            return [];
        }

        // Skip if method is not public
        if (!$methodReflection->isPublic()) {
            return [];
        }

        // Skip if method is internal (check PHPDoc @internal annotation)
        if ($this->isInternalMethod($methodReflection)) {
            return [];
        }

        $errors = [];

        // Check return type from PHPDoc
        $resolvedPhpDoc = $methodReflection->getDocComment();
        if (null !== $resolvedPhpDoc) {
            $returnTypes = $this->extractTypesFromPhpDoc($resolvedPhpDoc, '@return');
            foreach ($returnTypes as $returnType) {
                $errors = array_merge($errors, $this->checkTypeNameForInternal(
                    $returnType,
                    $classReflection->getName(),
                    $methodReflection->getName(),
                    'return'
                ));
            }
        }

        // Check parameter types from PHPDoc
        if (null !== $resolvedPhpDoc) {
            $paramTypes = $this->extractTypesFromPhpDoc($resolvedPhpDoc, '@param');
            foreach ($paramTypes as $paramType) {
                $errors = array_merge($errors, $this->checkTypeNameForInternal(
                    $paramType,
                    $classReflection->getName(),
                    $methodReflection->getName(),
                    'parameter'
                ));
            }
        }

        return $errors;
    }

    /**
     * Check if a class is marked as @internal in PHPDoc.
     */
    private function isInternal(ClassReflection $classReflection): bool
    {
        $docComment = $classReflection->getNativeReflection()->getDocComment();

        return false !== $docComment && str_contains($docComment, '@internal');
    }

    /**
     * Check if a method is marked as @internal in PHPDoc.
     */
    private function isInternalMethod(MethodReflection $methodReflection): bool
    {
        $docComment = $methodReflection->getDocComment();

        return null !== $docComment && str_contains($docComment, '@internal');
    }

    /**
     * Extract type names from PHPDoc annotation.
     *
     * @return list<string>
     */
    private function extractTypesFromPhpDoc(string $docComment, string $tag): array
    {
        $types = [];
        $lines = explode("\n", $docComment);

        foreach ($lines as $line) {
            // Match @return or @param annotations
            if (1 === preg_match('/'.$tag.'\s+([^\s]+)/', $line, $matches)) {
                $typeString = $matches[1]; // @phpstan-ignore offsetAccess.notFound
                // Parse type string and extract class names
                $types = array_merge($types, $this->parseTypeString($typeString));
            }
        }

        return $types;
    }

    /**
     * Parse a type string and extract class names.
     *
     * @return list<string>
     */
    private function parseTypeString(string $typeString): array
    {
        $classNames = [];

        // Remove array notation
        $typeString = preg_replace('/\[\]$/', '', $typeString);

        // Handle union types (Type1|Type2)
        $parts = preg_split('/[|&]/', $typeString);

        if (false === $parts) {
            return [];
        }

        foreach ($parts as $part) {
            $part = trim($part);

            // Handle generic types like array<Type>, list<Type>
            if (1 === preg_match('/^(?:array|list|iterable)<(.+)>$/', $part, $match)) {
                $classNames = array_merge($classNames, $this->parseTypeString($match[1]));

                continue;
            }

            // Skip built-in types
            if (\in_array($part, [
                'void', 'null', 'mixed', 'never',
                'string', 'int', 'float', 'bool', 'array', 'object', 'callable', 'iterable', 'resource',
                'self', 'static', 'parent', '$this',
                'true', 'false', 'non-empty-string', 'non-empty-array', 'non-empty-list',
                'positive-int', 'negative-int', 'non-positive-int', 'non-negative-int',
            ], true)) {
                continue;
            }

            // Skip variable names (start with $)
            if (str_starts_with($part, '$')) {
                continue;
            }

            // Skip empty parts
            if ('' === $part) {
                continue;
            }

            // Add the class name (remove leading backslash)
            $classNames[] = ltrim($part, '\\');
        }

        return array_values(array_unique($classNames));
    }

    /**
     * Check if a type name refers to an internal class.
     *
     * @return list<\PHPStan\Rules\IdentifierRuleError>
     */
    private function checkTypeNameForInternal(
        string $typeName,
        string $className,
        string $methodName,
        string $context
    ): array {
        // Try to resolve the type name to a full class name
        if (!$this->reflectionProvider->hasClass($typeName)) {
            // Try with namespace prefix
            $namespace = substr($className, 0, (int) strrpos($className, '\\'));
            $fullTypeName = $namespace.'\\'.$typeName;

            if (!$this->reflectionProvider->hasClass($fullTypeName)) {
                return []; // Can't find the class, skip
            }

            $typeName = $fullTypeName;
        }

        $typeClassReflection = $this->reflectionProvider->getClass($typeName);

        // Check if the type class is internal (check PHPDoc)
        if ($this->isInternal($typeClassReflection)) {
            $violationKey = \sprintf('%s::%s():%s', $className, $methodName, $typeName);

            // Skip known violations
            if (isset(self::KNOWN_VIOLATIONS[$violationKey])) {
                return [];
            }

            return [
                RuleErrorBuilder::message(\sprintf(
                    'Public method %s::%s() exposes internal type %s in %s type.',
                    $className,
                    $methodName,
                    $typeName,
                    $context
                ))
                ->identifier('phpCsFixer.internalTypeInPublicApi')
                ->build(),
            ];
        }

        return [];
    }
}
