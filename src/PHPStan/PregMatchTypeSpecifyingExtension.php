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

namespace PhpCsFixer\PHPStan;

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

    public function isStaticMethodSupported(MethodReflection $staticMethodReflection, StaticCall $node, TypeSpecifierContext $context): bool
    {
        return 'match' === $staticMethodReflection->getName() && !$context->null();
    }

    public function specifyTypes(MethodReflection $staticMethodReflection, StaticCall $node, Scope $scope, TypeSpecifierContext $context): SpecifiedTypes
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

        $patternType = $scope->getType($patternArg->value);
        $flagsType = null;
        if (null !== $flagsArg) {
            $flagsType = $scope->getType($flagsArg->value);
        }

        $matchedType = $this->regexShapeMatcher->matchType($patternType, $flagsType, TrinaryLogic::createFromBoolean($context->true()));
        if (null === $matchedType) {
            return new SpecifiedTypes();
        }

        $overwrite = false;
        if ($context->false()) {
            $overwrite = true;
            $context = $context->negate();
        }

        return $this->typeSpecifier->create(
            $matchesArg->value,
            $matchedType,
            $context,
            $overwrite,
            $scope,
            $node,
        );
    }
}
