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

use PhpCsFixer\FixerFactory;
use PhpCsFixer\FixerInterface;
use PhpCsFixer\RuleSet;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class IntegrationCaseFactory
{
    /**
     * @param string $fileName
     * @param string $content
     *
     * @return IntegrationCase
     */
    public function create($fileName, $content)
    {
        try {
            if (!preg_match('/--TEST--\n(?<title>.*?)\s--CONFIG--\n(?<config>.*?)(\s--SETTINGS--\n(?<settings>.*?))?(\s--REQUIREMENTS--\n(?<requirements>.*?))?\s--EXPECT--\n(?<expect>.*?\n*)(?:\n--INPUT--\s(?<input>.*)|$)/s', $content, $match)) {
                throw new \InvalidArgumentException('File format is invalid.');
            }

            return IntegrationCase::create()
                ->setFileName($fileName)
                ->setTitle($match['title'])
                ->setFixers($this->determineFixers($match['config']))
                ->setRequirements($this->determineRequirements($match['requirements']))
                ->setSettings($this->determineSettings($match['settings']))
                ->setExpectedCode($match['expect'])
                ->setInputCode(isset($match['input']) ? $match['input'] : null)
            ;
        } catch (\InvalidArgumentException $e) {
            throw new \InvalidArgumentException(
                sprintf('%s Test file: "%s".', $e->getMessage(), $fileName),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * Parses the '--CONFIG--' block of a '.test' file and determines what fixers should be used.
     *
     * @param string $config
     *
     * @return FixerInterface[]
     */
    protected function determineFixers($config)
    {
        $ruleSet = json_decode($config, true);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new \InvalidArgumentException('Malformed JSON configuration.');
        }

        return FixerFactory::create()
            ->registerBuiltInFixers()
            ->useRuleSet(new RuleSet($ruleSet))
            ->getFixers()
        ;
    }

    /**
     * Parses the '--REQUIREMENTS--' block of a '.test' file and determines requirements.
     *
     * @param string $config
     *
     * @return array
     */
    protected function determineRequirements($config)
    {
        $requirements = array('hhvm' => true, 'php' => PHP_VERSION);

        if ('' === $config) {
            return $requirements;
        }

        $lines = explode("\n", $config);
        if (empty($lines)) {
            return $requirements;
        }

        foreach ($lines as $line) {
            $labelValuePair = explode('=', $line);

            if (2 !== count($labelValuePair)) {
                throw new \InvalidArgumentException(sprintf('Invalid REQUIREMENTS line: "%d".', $line));
            }

            $label = strtolower(trim($labelValuePair[0]));
            $value = trim($labelValuePair[1]);

            switch ($label) {
                case 'hhvm':
                    $requirements['hhvm'] = 'false' !== $value;
                    break;
                case 'php':
                    $requirements['php'] = $value;
                    break;
                default:
                    throw new \InvalidArgumentException(sprintf('Unknown REQUIREMENTS line: "%d".', $line));
            }
        }

        return $requirements;
    }

    /**
     * Parses the '--SETTINGS--' block of a '.test' file and determines settings.
     *
     * @param string $config
     *
     * @return array
     */
    protected function determineSettings($config)
    {
        $settings = array('checkPriority' => true);

        if ('' === $config) {
            return $settings;
        }

        $lines = explode("\n", $config);
        if (empty($lines)) {
            return $settings;
        }

        foreach ($lines as $line) {
            $labelValuePair = explode('=', $line);
            if (2 !== count($labelValuePair)) {
                throw new \InvalidArgumentException(sprintf('Invalid SETTINGS line: "%d".', $line));
            }

            $label = trim($labelValuePair[0]);
            $value = trim($labelValuePair[1]);

            switch ($label) {
                case 'checkPriority':
                    $settings['checkPriority'] = 'false' !== $value;
                    break;
                default:
                    throw new \InvalidArgumentException(sprintf('Unknown SETTINGS line: "%d".', $line));
            }
        }

        return $settings;
    }
}
