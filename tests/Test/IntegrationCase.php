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

namespace PhpCsFixer\Tests\Test;

use PhpCsFixer\RuleSet\RuleSet;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class IntegrationCase
{
    /**
     * @var array{indent: string, lineEnding: string}
     */
    private array $config;

    private string $expectedCode;

    private string $fileName;

    private ?string $inputCode;

    /**
     * Env requirements (possible keys: 'php' or 'php<').
     *
     * @var array<string, int>|array{php: int}
     */
    private array $requirements;

    private RuleSet $ruleset;

    /**
     * Settings how to perform the test (possible keys: none in base class, use as extension point for custom IntegrationTestCase).
     *
     * @var array{checkPriority: bool, deprecations: list<string>, isExplicitPriorityCheck?: bool}
     */
    private array $settings;

    private string $title;

    /**
     * @param array{checkPriority: bool, deprecations: list<string>, isExplicitPriorityCheck?: bool} $settings
     * @param array<string, int>|array{php: int}                                                     $requirements
     * @param array{indent: string, lineEnding: string}                                              $config
     */
    public function __construct(
        string $fileName,
        string $title,
        array $settings,
        array $requirements,
        array $config,
        RuleSet $ruleset,
        string $expectedCode,
        ?string $inputCode
    ) {
        $this->fileName = $fileName;
        $this->title = $title;
        $this->settings = $settings;
        $this->requirements = $requirements;
        $this->config = $config;
        $this->ruleset = $ruleset;
        $this->expectedCode = $expectedCode;
        $this->inputCode = $inputCode;
    }

    public function hasInputCode(): bool
    {
        return null !== $this->inputCode;
    }

    /**
     * @return array{indent: string, lineEnding: string}
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    public function getExpectedCode(): string
    {
        return $this->expectedCode;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function getInputCode(): ?string
    {
        return $this->inputCode;
    }

    public function getRequirement(string $name): int
    {
        if (!\array_key_exists($name, $this->requirements)) {
            throw new \InvalidArgumentException(sprintf(
                'Unknown requirement key "%s", expected any of "%s".',
                $name,
                implode('","', array_keys($this->requirements))
            ));
        }

        return $this->requirements[$name];
    }

    /**
     * @return array<string, int>|array{php: int}
     */
    public function getRequirements(): array
    {
        return $this->requirements;
    }

    public function getRuleset(): RuleSet
    {
        return $this->ruleset;
    }

    /**
     * @return array{checkPriority: bool, deprecations: list<string>, isExplicitPriorityCheck?: bool}
     */
    public function getSettings(): array
    {
        return $this->settings;
    }

    public function getTitle(): string
    {
        return $this->title;
    }
}
