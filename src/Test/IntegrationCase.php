<?php

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Test;

use PhpCsFixer\RuleSet;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class IntegrationCase
{
    /**
     * @var array
     */
    private $config;

    /**
     * @var string
     */
    private $expectedCode;

    /**
     * @var string
     */
    private $fileName;

    /**
     * @var string|null
     */
    private $inputCode;

    /**
     * Env requirements (possible keys: php, hhvm).
     *
     * @var array
     */
    private $requirements;

    /**
     * @var RuleSet
     */
    private $ruleset;

    /**
     * Settings how to perform the test (possible keys: none in base class, use as extension point for custom IntegrationTestCase).
     *
     * @var array
     */
    private $settings;

    /**
     * @var string
     */
    private $title;

    /**
     * @param string      $fileName
     * @param string      $title
     * @param array       $settings
     * @param array       $requirements
     * @param array       $config
     * @param RuleSet     $ruleset
     * @param string      $expectedCode
     * @param string|null $inputCode
     */
    public function __construct(
        $fileName,
        $title,
        array $settings,
        array $requirements,
        array $config,
        RuleSet $ruleset,
        $expectedCode,
        $inputCode
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

    public function hasInputCode()
    {
        return null !== $this->inputCode;
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function getExpectedCode()
    {
        return $this->expectedCode;
    }

    public function getFileName()
    {
        return $this->fileName;
    }

    public function getInputCode()
    {
        return $this->inputCode;
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function getRequirement($name)
    {
        if (!is_string($name)) {
            throw new \InvalidArgumentException(sprintf(
                'Requirement key must be a string, got "%s".',
                is_object($name) ? get_class($name) : gettype($name).'#'.$name));
        }

        if (!array_key_exists($name, $this->requirements)) {
            throw new \InvalidArgumentException(sprintf(
                'Unknown requirement key "%s", expected any of "%s".',
                $name, implode('","', array_keys($this->requirements)))
            );
        }

        return $this->requirements[$name];
    }

    public function getRequirements()
    {
        return $this->requirements;
    }

    public function getRuleset()
    {
        return $this->ruleset;
    }

    public function getSettings()
    {
        return $this->settings;
    }

    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return bool
     *
     * @deprecated since v2.1, on ~2.1 line IntegrationTest check whether different priorities are required is done automatically, this method will be removed on v3.0
     */
    public function shouldCheckPriority()
    {
        @trigger_error(
            sprintf(
                'The "%s" method is deprecated. You should stop using it, as it will be removed in 3.0 version.',
                __METHOD__
            ),
            E_USER_DEPRECATED
        );

        return isset($this->settings['checkPriority']) ? $this->settings['checkPriority'] : true;
    }
}
