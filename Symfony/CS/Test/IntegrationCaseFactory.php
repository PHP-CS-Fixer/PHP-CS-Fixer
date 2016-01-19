<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Test;

use Symfony\CS\Fixer;
use Symfony\CS\FixerInterface;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class IntegrationCaseFactory
{
    private static $builtInFixers;

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
        static $levelMap = array(
            'none' => FixerInterface::NONE_LEVEL,
            'psr1' => FixerInterface::PSR1_LEVEL,
            'psr2' => FixerInterface::PSR2_LEVEL,
            'symfony' => FixerInterface::SYMFONY_LEVEL,
        );

        $lines = explode("\n", $config);
        if (empty($lines)) {
            throw new \InvalidArgumentException('No lines found in CONFIG section.');
        }

        $config = array('level' => null, 'fixers' => array(), '--fixers' => array());

        foreach ($lines as $line) {
            $labelValuePair = explode('=', $line);
            if (2 !== count($labelValuePair)) {
                throw new \InvalidArgumentException(sprintf('Invalid CONFIG line: "%d".', $line));
            }

            $label = strtolower(trim($labelValuePair[0]));
            $value = trim($labelValuePair[1]);

            switch ($label) {
                case 'level':
                    if (!array_key_exists($value, $levelMap)) {
                        throw new \InvalidArgumentException(sprintf('Unknown level "%s" set in CONFIG, expected any of "%s".', $value, implode(', ', array_keys($levelMap))));
                    }

                    if (null !== $config['level']) {
                        throw new \InvalidArgumentException('Cannot use multiple levels in configuration.');
                    }

                    $config['level'] = $value;
                    break;
                case 'fixers':
                case '--fixers':
                    foreach (explode(',', $value) as $fixer) {
                        $config[$label][] = strtolower(trim($fixer));
                    }

                    break;
                default:
                    throw new \InvalidArgumentException(sprintf('Unknown CONFIG line: "%d".', $ine));
            }
        }

        if (null === $config['level']) {
            throw new \InvalidArgumentException('Level not set in CONFIG section.');
        }

        if (null === self::$builtInFixers) {
            $fixer = new Fixer();
            $fixer->registerBuiltInFixers();
            self::$builtInFixers = $fixer->getFixers();
        }

        $fixers = array();
        for ($i = 0, $limit = count(self::$builtInFixers); $i < $limit; ++$i) {
            $fixer = self::$builtInFixers[$i];
            $fixerName = $fixer->getName();
            if ('psr0' === $fixer->getName()) {
                // File based fixer won't work
                continue;
            }

            if ($fixer->getLevel() !== ($fixer->getLevel() & $levelMap[$config['level']])) {
                if (false !== $key = array_search($fixerName, $config['fixers'], true)) {
                    $fixers[] = $fixer;
                    unset($config['fixers'][$key]);
                }
                continue;
            }

            if (false !== $key = array_search($fixerName, $config['--fixers'], true)) {
                unset($config['--fixers'][$key]);
                continue;
            }

            if (in_array($fixerName, $config['fixers'], true)) {
                throw new \InvalidArgumentException(sprintf('Additional fixer "%s" configured, but is already part of the level.', $fixerName));
            }

            $fixers[] = $fixer;
        }

        if (!empty($config['fixers']) || !empty($config['--fixers'])) {
            throw new \InvalidArgumentException(sprintf('Unknown fixers in CONFIG section: "%s".', implode(',', empty($config['fixers']) ? $config['--fixers'] : $config['fixers'])));
        }

        return $fixers;
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
