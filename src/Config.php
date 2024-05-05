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
     * @var list<FixerInterface>
     */
    private array $customFixers = [];

    /**
     * @var null|iterable<\SplFileInfo>
     */
    private ?iterable $finder = null;

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

    /**
     * @TODO: 4.0 - update to @PER
     *
     * @var array<string, array<string, mixed>|bool>
     */
    private array $rules;

    private bool $usingCache = true;

    public function __construct(string $name = 'default')
    {
        // @TODO 4.0 cleanup
        if (Utils::isFutureModeEnabled()) {
            $this->name = $name.' (future mode)';
            $this->rules = ['@PER-CS' => true];
        } else {
            $this->name = $name;
            $this->rules = ['@PSR12' => true];
        }
    }

    public function getCacheFile(): string
    {
        return $this->cacheFile;
    }

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

    public function getFormat(): string
    {
        return $this->format;
    }

    public function getHideProgress(): bool
    {
        return $this->hideProgress;
    }

    public function getIndent(): string
    {
        return $this->indent;
    }

    public function getLineEnding(): string
    {
        return $this->lineEnding;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPhpExecutable(): ?string
    {
        return $this->phpExecutable;
    }

    public function getRiskyAllowed(): bool
    {
        return $this->isRiskyAllowed;
    }

    public function getRules(): array
    {
        return $this->rules;
    }

    public function getUsingCache(): bool
    {
        return $this->usingCache;
    }

    public function registerCustomFixers(iterable $fixers): ConfigInterface
    {
        foreach ($fixers as $fixer) {
            $this->addCustomFixer($fixer);
        }

        return $this;
    }

    public function setCacheFile(string $cacheFile): ConfigInterface
    {
        $this->cacheFile = $cacheFile;

        return $this;
    }

    public function setFinder(iterable $finder): ConfigInterface
    {
        $this->finder = $finder;

        return $this;
    }

    public function setFormat(string $format): ConfigInterface
    {
        $this->format = $format;

        return $this;
    }

    public function setHideProgress(bool $hideProgress): ConfigInterface
    {
        $this->hideProgress = $hideProgress;

        return $this;
    }

    public function setIndent(string $indent): ConfigInterface
    {
        $this->indent = $indent;

        return $this;
    }

    public function setLineEnding(string $lineEnding): ConfigInterface
    {
        $this->lineEnding = $lineEnding;

        return $this;
    }

    public function setPhpExecutable(?string $phpExecutable): ConfigInterface
    {
        $this->phpExecutable = $phpExecutable;

        return $this;
    }

    public function setRiskyAllowed(bool $isRiskyAllowed): ConfigInterface
    {
        $this->isRiskyAllowed = $isRiskyAllowed;

        return $this;
    }

    public function setRules(array $rules): ConfigInterface
    {
        $this->rules = $rules;

        return $this;
    }

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
