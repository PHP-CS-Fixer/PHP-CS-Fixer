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
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\Type;
use PHPStan\Type\TypeWithClassName;
use PHPStan\Type\UnionType;
use PHPStan\Type\VerbosityLevel;

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

        // Skip if class is internal
        if ($classReflection->isInternal()) {
            return [];
        }

        // Skip if method is not public
        if (!$methodReflection->isPublic()) {
            return [];
        }

        // Skip if method is internal
        if ($methodReflection->isInternal()) {
            return [];
        }

        $errors = [];

        // Check return type
        $returnType = $methodReflection->getVariants()[0]->getReturnType();
        $errors = array_merge($errors, $this->checkTypeForInternalClasses(
            $returnType,
            $classReflection->getName(),
            $methodReflection->getName(),
            'return'
        ));

        // Check parameter types
        foreach ($methodReflection->getVariants()[0]->getParameters() as $parameter) {
            $paramType = $parameter->getType();
            $errors = array_merge($errors, $this->checkTypeForInternalClasses(
                $paramType,
                $classReflection->getName(),
                $methodReflection->getName(),
                'parameter $'.$parameter->getName()
            ));
        }

        return $errors;
    }

    /**
     * @return list<RuleError>
     */
    private function checkTypeForInternalClasses(
        Type $type,
        string $className,
        string $methodName,
        string $context
    ): array {
        $errors = [];

        // Handle union types
        if ($type instanceof UnionType) {
            foreach ($type->getTypes() as $innerType) {
                $errors = array_merge($errors, $this->checkTypeForInternalClasses($innerType, $className, $methodName, $context));
            }

            return $errors;
        }

        // Check if type is a class type
        if ($type instanceof TypeWithClassName) {
            $typeClassName = $type->getClassName();

            // Skip if we can't reflect the class
            if (!$this->reflectionProvider->hasClass($typeClassName)) {
                return [];
            }

            $typeClassReflection = $this->reflectionProvider->getClass($typeClassName);

            // Check if the type class is internal
            if ($typeClassReflection->isInternal()) {
                $violationKey = \sprintf('%s::%s():%s', $className, $methodName, $typeClassName);

                // Skip known violations
                if (isset(self::KNOWN_VIOLATIONS[$violationKey])) {
                    return [];
                }

                $errors[] = RuleErrorBuilder::message(\sprintf(
                    'Public method %s::%s() exposes internal type %s in %s type.',
                    $className,
                    $methodName,
                    $type->describe(VerbosityLevel::typeOnly()),
                    $context
                ))->identifier('phpCsFixer.internalTypeInPublicApi')->build();
            }
        }

        return $errors;
    }
}
