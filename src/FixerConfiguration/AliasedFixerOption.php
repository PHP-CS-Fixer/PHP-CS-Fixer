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

/**
 * @author ntzm
 *
 * @internal
 */
final class AliasedFixerOption implements FixerOptionInterface
{
    private FixerOptionInterface $fixerOption;

    private string $alias;

    public function __construct(FixerOptionInterface $fixerOption, string $alias)
    {
        $this->fixerOption = $fixerOption;
        $this->alias = $alias;
    }

    public function getAlias(): string
    {
        return $this->alias;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return $this->fixerOption->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription(): string
    {
        return $this->fixerOption->getDescription();
    }

    /**
     * {@inheritdoc}
     */
    public function hasDefault(): bool
    {
        return $this->fixerOption->hasDefault();
    }

    /**
     * {@inheritdoc}
     */
    public function getDefault()
    {
        return $this->fixerOption->getDefault();
    }

    /**
     * {@inheritdoc}
     */
    public function getAllowedTypes(): ?array
    {
        return $this->fixerOption->getAllowedTypes();
    }

    /**
     * {@inheritdoc}
     */
    public function getAllowedValues(): ?array
    {
        return $this->fixerOption->getAllowedValues();
    }

    /**
     * {@inheritdoc}
     */
    public function getNormalizer(): ?\Closure
    {
        return $this->fixerOption->getNormalizer();
    }
}
