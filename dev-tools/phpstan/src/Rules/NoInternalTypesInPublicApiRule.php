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
use PHPStan\Node\InClassNode;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\Type;

/**
 * Validates that public and protected methods and properties in non-internal classes do not expose internal types.
 *
 * @implements Rule<InClassNode>
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class NoInternalTypesInPublicApiRule implements Rule
{
    public function getNodeType(): string
    {
        return InClassNode::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $classReflection = $node->getClassReflection();

        // Skip if class is internal (check PHPDoc @internal annotation)
        if ($this->isInternal($classReflection)) {
            return [];
        }

        $errors = [];

        // Check methods (public and protected)
        foreach ($classReflection->getNativeReflection()->getMethods() as $method) {
            $methodReflection = $classReflection->getNativeMethod($method->getName());

            // Skip if method is private
            if ($methodReflection->isPrivate()) {
                continue;
            }

            // Skip if method is internal (check PHPDoc @internal annotation)
            if ($this->isInternalMethod($classReflection, $methodReflection)) {
                continue;
            }

            // Check return type using PHPStan's type system
            foreach ($methodReflection->getVariants() as $variant) {
                $returnType = $variant->getReturnType();
                $errors = array_merge(
                    $errors,
                    $this->checkTypeForInternal(
                        $returnType,
                        $classReflection->getName(),
                        'method '.$methodReflection->getName().'()',
                        'return'
                    )
                );

                // Check parameter types using PHPStan's type system
                foreach ($variant->getParameters() as $parameter) {
                    $paramType = $parameter->getType();
                    $errors = array_merge(
                        $errors,
                        $this->checkTypeForInternal(
                            $paramType,
                            $classReflection->getName(),
                            'method '.$methodReflection->getName().'()',
                            'parameter $'.$parameter->getName()
                        )
                    );
                }
            }
        }

        // Check properties (public and protected)
        foreach ($classReflection->getNativeReflection()->getProperties() as $property) {
            // Skip private properties
            if ($property->isPrivate()) {
                continue;
            }

            $propertyReflection = $classReflection->getNativeProperty($property->getName());

            // Skip if property is internal (check PHPDoc @internal annotation)
            $docComment = $propertyReflection->getDocComment();
            if (null !== $docComment && str_contains($docComment, '@internal')) {
                continue;
            }

            // Check property type using PHPStan's type system
            $propertyType = $propertyReflection->getReadableType();
            $errors = array_merge(
                $errors,
                $this->checkTypeForInternal(
                    $propertyType,
                    $classReflection->getName(),
                    'property $'.$propertyReflection->getName(),
                    'type'
                )
            );
        }

        return $errors;
    }

    /**
     * Check if a class is marked as @internal in PHPDoc.
     */
    private function isInternal(ClassReflection $classReflection): bool
    {
        static $cache = [];

        $name = $classReflection->getName();

        if (!isset($cache[$name])) {
            $docComment = $classReflection->getNativeReflection()->getDocComment();
            $cache[$name] = false !== $docComment && str_contains($docComment, '@internal');
        }

        return $cache[$name];
    }

    /**
     * Check if a method is marked as @internal in PHPDoc.
     */
    private function isInternalMethod(ClassReflection $classReflection, MethodReflection $methodReflection): bool
    {
        static $cache = [];

        $name = \sprintf('%s::%s', $classReflection->getName(), $methodReflection->getName());

        if (!isset($cache[$name])) {
            $docComment = $methodReflection->getDocComment();

            $cache[$name] = null !== $docComment && str_contains($docComment, '@internal');
        }

        return $cache[$name];
    }

    /**
     * Check if a PHPStan Type contains references to internal classes.
     * Uses PHPStan's built-in type system to recursively check all contained types.
     *
     * @return list<IdentifierRuleError>
     */
    private function checkTypeForInternal(
        Type $type,
        string $className,
        string $memberName,
        string $context
    ): array {
        $errors = [];

        // Recursively check all class references in the type
        // This handles union types, intersection types, and generic types automatically
        foreach ($type->getObjectClassReflections() as $typeClassReflection) {
            // Check if the type class is internal (check PHPDoc)
            if ($this->isInternal($typeClassReflection)) {
                $errors[] = RuleErrorBuilder::message(\sprintf(
                    '%s %s exposes internal type %s in %s type.',
                    $className,
                    $memberName,
                    $typeClassReflection->getName(),
                    $context
                ))
                    ->identifier('phpCsFixer.internalTypeInPublicApi')
                    ->build()
                ;
            }
        }

        return $errors;
    }
}
