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
use PhpCsFixer\RuleSet\RuleSetDescriptionInterface;
use PhpCsFixer\Runner\Parallel\ParallelConfig;
use PhpCsFixer\Runner\Parallel\ParallelConfigFactory;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Katsuhiro Ogawa <ko.fivestar@gmail.com>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
class Config implements ConfigInterface, ParallelAwareConfigInterface, UnsupportedPhpVersionAllowedConfigInterface, CustomRulesetsAwareConfigInterface
{
    /**
     * @var non-empty-string
     */
    private string $cacheFile = '.php-cs-fixer.cache';

    /**
     * @var list<FixerInterface>
     */
    private array $customFixers = [];

    /**
     * @var list<class-string<RuleSetDescriptionInterface>>
     */
    private array $customRuleSets = [];

    /**
     * @var null|iterable<\SplFileInfo>
     */
    private ?iterable $finder = null;

    private string $format;

    private bool $hideProgress = false;

    /**
     * @var non-empty-string
     */
    private string $indent = '    ';

    private bool $isRiskyAllowed = false;

    /**
     * @var non-empty-string
     */
    private string $lineEnding = "\n";

    private string $name;

    private ParallelConfig $parallelConfig;

    private ?string $phpExecutable = null;

    /**
     * @TODO: 4.0 - update to @PER
     *
     * @var array<string, array<string, mixed>|bool>
     */
    private array $rules;

    private bool $usingCache = true;

    private bool $isUnsupportedPhpVersionAllowed = false;

    public function __construct(string $name = 'default')
    {
        // @TODO 4.0 cleanup
        if (Future::isFutureModeEnabled()) {
            $this->name = $name.' (future mode)';
            $this->rules = ['@PER-CS' => true];
            $this->format = '@auto';
        } else {
            $this->name = $name;
            $this->rules = ['@PSR12' => true];
            $this->format = 'txt';
        }

        // @TODO 4.0 cleanup
        if (Future::isFutureModeEnabled() || filter_var(getenv('PHP_CS_FIXER_PARALLEL'), \FILTER_VALIDATE_BOOL)) {
            $this->parallelConfig = ParallelConfigFactory::detect();
        } else {
            $this->parallelConfig = ParallelConfigFactory::sequential();
        }

        // @TODO 4.0 cleanup
        if (false !== getenv('PHP_CS_FIXER_IGNORE_ENV')) {
            $this->isUnsupportedPhpVersionAllowed = filter_var(getenv('PHP_CS_FIXER_IGNORE_ENV'), \FILTER_VALIDATE_BOOL);
        }
    }

    /**
     * @return non-empty-string
     */
    public function getCacheFile(): string
    {
        return $this->cacheFile;
    }

    public function getCustomFixers(): array
    {
        return $this->customFixers;
    }

    public function getCustomRuleSets(): array
    {
        return $this->customRuleSets;
    }

    /**
     * @return Finder
     */
    public function getFinder(): iterable
    {
        $this->finder ??= new Finder();

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

    public function getParallelConfig(): ParallelConfig
    {
        return $this->parallelConfig;
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

    public function getUnsupportedPhpVersionAllowed(): bool
    {
        return $this->isUnsupportedPhpVersionAllowed;
    }

    public function registerCustomFixers(iterable $fixers): ConfigInterface
    {
        foreach ($fixers as $fixer) {
            $this->addCustomFixer($fixer);
        }

        return $this;
    }

    /**
     * @param list<class-string<RuleSetDescriptionInterface>> $ruleSets
     */
    public function registerCustomRuleSets(array $ruleSets): ConfigInterface
    {
        foreach ($ruleSets as $class) {
            if (!class_exists($class)) {
                throw new \UnexpectedValueException(\sprintf('Rule set "%s" does not exist.', $class));
            }

            if (!\in_array(RuleSetDescriptionInterface::class, class_implements($class), true)) {
                throw new \UnexpectedValueException(\sprintf('Rule set "%s" does not implement "%s".', $class, RuleSetDescriptionInterface::class));
            }
        }

        $this->customRuleSets = array_values(array_unique(array_merge($this->customRuleSets, $ruleSets)));

        return $this;
    }

    /**
     * @param non-empty-string $cacheFile
     */
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

    /**
     * @param non-empty-string $indent
     */
    public function setIndent(string $indent): ConfigInterface
    {
        $this->indent = $indent;

        return $this;
    }

    /**
     * @param non-empty-string $lineEnding
     */
    public function setLineEnding(string $lineEnding): ConfigInterface
    {
        $this->lineEnding = $lineEnding;

        return $this;
    }

    public function setParallelConfig(ParallelConfig $config): ConfigInterface
    {
        $this->parallelConfig = $config;

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

    public function setUnsupportedPhpVersionAllowed(bool $isUnsupportedPhpVersionAllowed): ConfigInterface
    {
        $this->isUnsupportedPhpVersionAllowed = $isUnsupportedPhpVersionAllowed;

        return $this;
    }

    private function addCustomFixer(FixerInterface $fixer): void
    {
        $this->customFixers[] = $fixer;
    }
}
