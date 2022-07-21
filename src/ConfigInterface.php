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

namespace PhpCsFixer;

use PhpCsFixer\Fixer\FixerInterface;
use SplFileInfo;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
interface ConfigInterface
{
    /**
     * Returns the path to the cache file.
     *
     * @return null|string Returns null if not using cache
     */
    public function getCacheFile(): ?string;

    /**
     * Returns the custom fixers to use.
     *
     * @return FixerInterface[]
     */
    public function getCustomFixers(): array;

    /**
     * Returns files to scan.
     *
     * @return iterable<SplFileInfo>
     */
    public function getFinder(): iterable;

    public function getFormat(): string;

    /**
     * Returns true if progress should be hidden.
     */
    public function getHideProgress(): bool;

    public function getIndent(): string;

    public function getLineEnding(): string;

    /**
     * Returns the name of the configuration.
     *
     * The name must be all lowercase and without any spaces.
     *
     * @return string The name of the configuration
     */
    public function getName(): string;

    /**
     * Get configured PHP executable, if any.
     */
    public function getPhpExecutable(): ?string;

    /**
     * Check if it is allowed to run risky fixers.
     */
    public function getRiskyAllowed(): bool;

    /**
     * Get rules.
     *
     * Keys of array are names of fixers/sets, values are true/false.
     */
    public function getRules(): array;

    /**
     * Returns true if caching should be enabled.
     */
    public function getUsingCache(): bool;

    /**
     * Adds a suite of custom fixers.
     *
     * Name of custom fixer should follow `VendorName/rule_name` convention.
     *
     * @param FixerInterface[]|iterable|\Traversable $fixers
     */
    public function registerCustomFixers(iterable $fixers): self;

    /**
     * Sets the path to the cache file.
     */
    public function setCacheFile(string $cacheFile): self;

    /**
     * @param iterable<SplFileInfo> $finder
     */
    public function setFinder(iterable $finder): self;

    public function setFormat(string $format): self;

    public function setHideProgress(bool $hideProgress): self;

    public function setIndent(string $indent): self;

    public function setLineEnding(string $lineEnding): self;

    /**
     * Set PHP executable.
     */
    public function setPhpExecutable(?string $phpExecutable): self;

    /**
     * Set if it is allowed to run risky fixers.
     */
    public function setRiskyAllowed(bool $isRiskyAllowed): self;

    /**
     * Set rules.
     *
     * Keys of array are names of fixers or sets.
     * Value for set must be bool (turn it on or off).
     * Value for fixer may be bool (turn it on or off) or array of configuration
     * (turn it on and contains configuration for FixerInterface::configure method).
     */
    public function setRules(array $rules): self;

    public function setUsingCache(bool $usingCache): self;
}
