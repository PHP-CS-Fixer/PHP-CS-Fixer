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

final class FixerOption implements FixerOptionInterface
{
    private string $name;

    private string $description;

    private bool $isRequired;

    /**
     * @var mixed
     */
    private $default;

    /**
     * @var null|list<string>
     */
    private $allowedTypes;

    /**
     * @var null|list<(callable(mixed): bool)|null|scalar>
     */
    private $allowedValues;

    /**
     * @var null|\Closure
     */
    private $normalizer;

    /**
     * @param mixed             $default
     * @param null|list<string> $allowedTypes
     * @param null|list<(callable(mixed): bool)|null|scalar> $allowedValues
     */
    public function __construct(
        string $name,
        string $description,
        bool $isRequired = true,
        $default = null,
        ?array $allowedTypes = null,
        ?array $allowedValues = null,
        ?\Closure $normalizer = null
    ) {
        if ($isRequired && null !== $default) {
            throw new \LogicException('Required options cannot have a default value.');
        }

        if (null !== $allowedValues) {
            foreach ($allowedValues as &$allowedValue) {
                if ($allowedValue instanceof \Closure) {
                    $allowedValue = $this->unbind($allowedValue);
                }
            }
        }

        $this->name = $name;
        $this->description = $description;
        $this->isRequired = $isRequired;
        $this->default = $default;
        $this->allowedTypes = $allowedTypes;
        $this->allowedValues = $allowedValues;

        if (null !== $normalizer) {
            $this->normalizer = $this->unbind($normalizer);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * {@inheritdoc}
     */
    public function hasDefault(): bool
    {
        return !$this->isRequired;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefault()
    {
        if (!$this->hasDefault()) {
            throw new \LogicException('No default value defined.');
        }

        return $this->default;
    }

    /**
     * {@inheritdoc}
     */
    public function getAllowedTypes(): ?array
    {
        return $this->allowedTypes;
    }

    /**
     * {@inheritdoc}
     */
    public function getAllowedValues(): ?array
    {
        return $this->allowedValues;
    }

    /**
     * {@inheritdoc}
     */
    public function getNormalizer(): ?\Closure
    {
        return $this->normalizer;
    }

    /**
     * Unbinds the given closure to avoid memory leaks.
     *
     * The closures provided to this class were probably defined in a fixer
     * class and thus bound to it by default. The configuration will then be
     * stored in {@see AbstractFixer::$configurationDefinition}, leading to the
     * following cyclic reference:
     *
     *     fixer -> configuration definition -> options -> closures -> fixer
     *
     * This cyclic reference prevent the garbage collector to free memory as
     * all elements are still referenced.
     *
     * See {@see https://bugs.php.net/bug.php?id=69639 Bug #69639} for details.
     */
    private function unbind(\Closure $closure): \Closure
    {
        return $closure->bindTo(null);
    }
}
