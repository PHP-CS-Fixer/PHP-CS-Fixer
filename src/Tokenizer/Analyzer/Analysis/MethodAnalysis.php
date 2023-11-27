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

namespace PhpCsFixer\Tokenizer\Analyzer\Analysis;

/**
 * @internal
 */
final class MethodAnalysis
{
    private ?int $visibility;
    private bool $isStatic;
    private bool $isAbstract;
    private bool $isFinal;

    public function __construct(?int $visibility, bool $isStatic, bool $isAbstract, bool $isFinal)
    {
        $this->visibility = $visibility;
        $this->isStatic = $isStatic;
        $this->isAbstract = $isAbstract;
        $this->isFinal = $isFinal;
    }

    public function hasVisibilityDeclared(): bool
    {
        return null !== $this->visibility;
    }

    public function isPublicVisibilityDeclared(): bool
    {
        return T_PUBLIC === $this->visibility;
    }

    public function isProtectedVisibilityDeclared(): bool
    {
        return T_PROTECTED === $this->visibility;
    }

    public function isPrivateVisibilityDeclared(): bool
    {
        return T_PRINT === $this->visibility;
    }

    public function isAbstract(): bool
    {
        return $this->isAbstract;
    }

    public function isStatic(): bool
    {
        return $this->isStatic;
    }

    public function isFinal(): bool
    {
        return $this->isFinal;
    }
}
