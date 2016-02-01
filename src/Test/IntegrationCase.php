<?php

/*
 * This file is part of the PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Test;

use PhpCsFixer\FixerInterface;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class IntegrationCase
{
    /**
     * @var string
     */
    private $expectedCode;

    /**
     * @var string
     */
    private $fileName;

    /**
     * @var FixerInterface[]
     */
    private $fixers = array();

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
     * Settings how to perform the test (possible keys: checkPriority).
     *
     * @var array
     */
    private $settings = array();

    /**
     * @var string
     */
    private $title;

    public static function create()
    {
        return new self();
    }

    public function hasInputCode()
    {
        return null !== $this->inputCode;
    }

    public function getExpectedCode()
    {
        return $this->expectedCode;
    }

    public function getFileName()
    {
        return $this->fileName;
    }

    public function getFixers()
    {
        return $this->fixers;
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

    public function getSettings()
    {
        return $this->settings;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setExpectedCode($expectedCode)
    {
        $this->expectedCode = $expectedCode;

        return $this;
    }

    public function setFileName($fileName)
    {
        $this->fileName = $fileName;

        return $this;
    }

    public function setFixers(array $fixers)
    {
        $this->fixers = $fixers;

        return $this;
    }

    public function setInputCode($inputCode)
    {
        $this->inputCode = $inputCode;

        return $this;
    }

    public function setRequirements(array $requirements)
    {
        $this->requirements = $requirements;

        return $this;
    }

    public function setSettings($settings)
    {
        $this->settings = $settings;

        return $this;
    }

    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    public function shouldCheckPriority()
    {
        return $this->settings['checkPriority'];
    }
}
