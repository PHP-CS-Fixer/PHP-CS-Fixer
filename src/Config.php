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

/**
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Katsuhiro Ogawa <ko.fivestar@gmail.com>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class Config implements ConfigInterface
{
    private string $cacheFile = '.php-cs-fixer.cache';

    /**
     * @var FixerInterface[]
     */
    private array $customFixers = [];

    /**
     * @var null|iterable
     */
    private $finder;

    private string $format = 'txt';

    private bool $hideProgress = false;

    private string $indent = '    ';

    private bool $isRiskyAllowed = false;

    private string $lineEnding = "\n";

    private string $name;

    /**
     * @var null|string
     */
    private $phpExecutable;

    private array $rules = ['@PSR12' => true];

    private bool $usingCache = true;

    public function __construct(string $name = 'default')
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheFile(): string
    {
        return $this->cacheFile;
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomFixers(): array
    {
        return $this->customFixers;
    }

    /**
     * @return Finder
     */
    public function getFinder(): iterable
    {
        if (null === $this->finder) {
            $this->finder = new Finder();
        }

        return $this->finder;
    }

    /**
     * {@inheritdoc}
     */
    public function getFormat(): string
    {
        return $this->format;
    }

    /**
     * {@inheritdoc}
     */
    public function getHideProgress(): bool
    {
        return $this->hideProgress;
    }

    /**
     * {@inheritdoc}
     */
    public function getIndent(): string
    {
        return $this->indent;
    }

    /**
     * {@inheritdoc}
     */
    public function getLineEnding(): string
    {
        return $this->lineEnding;
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
    public function getPhpExecutable(): ?string
    {
        return $this->phpExecutable;
    }

    /**
     * {@inheritdoc}
     */
    public function getRiskyAllowed(): bool
    {
        return $this->isRiskyAllowed;
    }

    /**
     * {@inheritdoc}
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsingCache(): bool
    {
        return $this->usingCache;
    }

    /**
     * {@inheritdoc}
     */
    public function registerCustomFixers(iterable $fixers): ConfigInterface
    {
        foreach ($fixers as $fixer) {
            $this->addCustomFixer($fixer);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setCacheFile(string $cacheFile): ConfigInterface
    {
        $this->cacheFile = $cacheFile;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setFinder(iterable $finder): ConfigInterface
    {
        $this->finder = $finder;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setFormat(string $format): ConfigInterface
    {
        $this->format = $format;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setHideProgress(bool $hideProgress): ConfigInterface
    {
        $this->hideProgress = $hideProgress;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setIndent(string $indent): ConfigInterface
    {
        $this->indent = $indent;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setLineEnding(string $lineEnding): ConfigInterface
    {
        $this->lineEnding = $lineEnding;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setPhpExecutable(?string $phpExecutable): ConfigInterface
    {
        $this->phpExecutable = $phpExecutable;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setRiskyAllowed(bool $isRiskyAllowed): ConfigInterface
    {
        $this->isRiskyAllowed = $isRiskyAllowed;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setRules(array $rules): ConfigInterface
    {
        $this->rules = $rules;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setUsingCache(bool $usingCache): ConfigInterface
    {
        $this->usingCache = $usingCache;

        return $this;
    }

    private function addCustomFixer(FixerInterface $fixer): void
    {
        $this->customFixers[] = $fixer;
    }
}
