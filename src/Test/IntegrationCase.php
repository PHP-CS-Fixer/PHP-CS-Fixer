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
    private $config = array();

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
    private $requirements = array();

    /**
     * @var RuleSet
     */
    private $ruleset;

    /**
     * Settings how to perform the test (possible keys: checkPriority).
     *
     * @var array
     */
    private $settings = array();

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
     * @param RulesSet    $ruleset
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

    public static function create()
    {
        return new self();
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

    public function getRequirement($name)
    {
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

    public function shouldCheckPriority()
    {
        return $this->settings['checkPriority'];
    }
}
