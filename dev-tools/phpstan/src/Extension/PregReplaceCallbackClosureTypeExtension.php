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
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ParameterReflection;
use PHPStan\Reflection\PassedByReference;
use PHPStan\TrinaryLogic;
use PHPStan\Type\ClosureType;
use PHPStan\Type\Php\RegexArrayShapeMatcher;
use PHPStan\Type\StaticMethodParameterClosureTypeExtension;
use PHPStan\Type\StringType;
use PHPStan\Type\Type;

final class PregReplaceCallbackClosureTypeExtension implements StaticMethodParameterClosureTypeExtension
{
    private RegexArrayShapeMatcher $regexShapeMatcher;

    public function __construct(
        RegexArrayShapeMatcher $regexShapeMatcher
    ) {
        $this->regexShapeMatcher = $regexShapeMatcher;
    }

    public function isStaticMethodSupported(MethodReflection $methodReflection, ParameterReflection $parameter): bool
    {
        return
            Preg::class === $methodReflection->getDeclaringClass()->getName()
            && 'replaceCallback' === $methodReflection->getName()
            && 'callback' === $parameter->getName();
    }

    public function getTypeFromStaticMethodCall(MethodReflection $methodReflection, StaticCall $methodCall, ParameterReflection $parameter, Scope $scope): ?Type
    {
        $args = $methodCall->getArgs();
        $patternArg = $args[0] ?? null;

        if (null === $patternArg) {
            return null;
        }

        $matchesType = $this->regexShapeMatcher->matchExpr($patternArg->value, null, TrinaryLogic::createYes(), $scope);

        if (null === $matchesType) {
            return null;
        }

        $matchesParameter = new class($matchesType) implements ParameterReflection {
            private Type $type;

            public function __construct(Type $type)
            {
                $this->type = $type;
            }

            public function getName(): string
            {
                return 'matches';
            }

            public function isOptional(): bool
            {
                return false;
            }

            public function getType(): Type
            {
                return $this->type;
            }

            public function passedByReference(): PassedByReference
            {
                return PassedByReference::createNo();
            }

            public function isVariadic(): bool
            {
                return false;
            }

            public function getDefaultValue(): ?Type
            {
                return null;
            }
        };

        return new ClosureType(
            [$matchesParameter],
            new StringType(),
        );
    }
}
