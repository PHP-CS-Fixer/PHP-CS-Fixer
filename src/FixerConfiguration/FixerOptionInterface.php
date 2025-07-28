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

namespace PhpCsFixer\FixerConfiguration;

interface FixerOptionInterface
{
    public function getName(): string;

    public function getDescription(): string;

    public function hasDefault(): bool;

    /**
     * @return mixed
     *
     * @throws \LogicException when no default value is defined
     */
    public function getDefault();

    /**
     * @return null|list<string>
     */
    public function getAllowedTypes(): ?array;

    /**
     * @return null|non-empty-list<null|(callable(mixed): bool)|scalar>
     */
    public function getAllowedValues(): ?array;

    public function getNormalizer(): ?\Closure;
}
