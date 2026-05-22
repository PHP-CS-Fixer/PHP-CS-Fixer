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

namespace PhpCsFixer\PHPStan\Extension;

use PhpCsFixer\Preg;
use PhpParser\Node\Expr\StaticCall;
use PHPStan\Analyser\Scope;
use PHPStan\Analyser\SpecifiedTypes;
use PHPStan\Analyser\TypeSpecifier;
use PHPStan\Analyser\TypeSpecifierAwareExtension;
use PHPStan\Analyser\TypeSpecifierContext;
use PHPStan\Reflection\MethodReflection;
use PHPStan\TrinaryLogic;
use PHPStan\Type\Php\RegexArrayShapeMatcher;
use PHPStan\Type\StaticMethodTypeSpecifyingExtension;

final class PregMatchTypeSpecifyingExtension implements StaticMethodTypeSpecifyingExtension, TypeSpecifierAwareExtension
{
    private RegexArrayShapeMatcher $regexShapeMatcher;

    private TypeSpecifier $typeSpecifier;

    public function __construct(
        RegexArrayShapeMatcher $regexShapeMatcher
    ) {
        $this->regexShapeMatcher = $regexShapeMatcher;
    }

    public function getClass(): string
    {
        return Preg::class;
    }

    public function setTypeSpecifier(TypeSpecifier $typeSpecifier): void
    {
        $this->typeSpecifier = $typeSpecifier;
    }

    public function isStaticMethodSupported(MethodReflection $methodReflection, StaticCall $node, TypeSpecifierContext $context): bool
    {
        return \in_array($methodReflection->getName(), ['match', 'matchAll'], true) && !$context->null();
    }

    public function specifyTypes(MethodReflection $methodReflection, StaticCall $node, Scope $scope, TypeSpecifierContext $context): SpecifiedTypes
    {
        $args = $node->getArgs();
        $patternArg = $args[0] ?? null;
        $matchesArg = $args[2] ?? null;
        $flagsArg = $args[3] ?? null;

        if (
            null === $patternArg || null === $matchesArg
        ) {
            return new SpecifiedTypes();
        }

        $flagsType = null;
        if (null !== $flagsArg) {
            $flagsType = $scope->getType($flagsArg->value);
        }

        $matcherMethod = ('match' === $methodReflection->getName()) ? 'matchExpr' : 'matchAllExpr';

        /** @phpstan-ignore method.dynamicName */
        $matchedType = $this->regexShapeMatcher->{$matcherMethod}(
            $patternArg->value,
            $flagsType,
            TrinaryLogic::createFromBoolean($context->true()),
            $scope
        );

        if (null === $matchedType) {
            return new SpecifiedTypes();
        }

        $overwrite = false;
        if ($context->false()) {
            $overwrite = true;
            $context = $context->negate();
        }

        $types = $this->typeSpecifier->create(
            $matchesArg->value,
            $matchedType,
            $context,
            $scope,
        )->setRootExpr($node);
        if ($overwrite) {
            $types = $types->setAlwaysOverwriteTypes();
        }

        return $types;
    }
}
