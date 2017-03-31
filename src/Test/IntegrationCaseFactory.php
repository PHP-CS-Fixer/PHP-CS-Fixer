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
use Symfony\Component\Finder\SplFileInfo;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class IntegrationCaseFactory
{
    /**
     * @param SplFileInfo $file
     *
     * @return IntegrationCase
     */
    public function create(SplFileInfo $file)
    {
        try {
            if (!preg_match(
                '/^
                            --TEST--           \r?\n(?<title>          .*?)
                       \s   --RULESET--        \r?\n(?<ruleset>        .*?)
                    (?:\s   --CONFIG--         \r?\n(?<config>         .*?))?
                    (?:\s   --SETTINGS--       \r?\n(?<settings>       .*?))?
                    (?:\s   --REQUIREMENTS--   \r?\n(?<requirements>   .*?))?
                    (?:\s   --EXPECT--         \r?\n(?<expect>         .*?\r?\n*))?
                    (?:\s   --INPUT--          \r?\n(?<input>          .*))?
                $/sx',
                $file->getContents(),
                $match
            )) {
                throw new \InvalidArgumentException('File format is invalid.');
            }

            $match = array_merge(
                array(
                    'config' => null,
                    'settings' => null,
                    'requirements' => null,
                    'expect' => null,
                    'input' => null,
                ),
                $match
            );

            return new IntegrationCase(
                $file->getRelativePathname(),
                $match['title'],
                $this->determineSettings($match['settings']),
                $this->determineRequirements($match['requirements']),
                $this->determineConfig($match['config']),
                $this->determineRuleset($match['ruleset']),
                $this->determineExpectedCode($match['expect'], $file),
                $this->determineInputCode($match['input'], $file)
            );
        } catch (\InvalidArgumentException $e) {
            throw new \InvalidArgumentException(
                sprintf('%s Test file: "%s".', $e->getMessage(), $file->getRelativePathname()),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * Parses the '--CONFIG--' block of a '.test' file.
     *
     * @param string $config
     *
     * @return array
     */
    protected function determineConfig($config)
    {
        $parsed = $this->parseJson($config, array(
            'indent' => '    ',
            'lineEnding' => "\n",
        ));

        if (!is_string($parsed['indent'])) {
            throw new \InvalidArgumentException(sprintf(
                'Expected string value for "indent", got "%s".',
                is_object($parsed['indent']) ? get_class($parsed['indent']) : gettype($parsed['indent']).'#'.$parsed['indent'])
            );
        }

        if (!is_string($parsed['lineEnding'])) {
            throw new \InvalidArgumentException(sprintf(
                'Expected string value for "lineEnding", got "%s".',
                is_object($parsed['lineEnding']) ? get_class($parsed['lineEnding']) : gettype($parsed['lineEnding']).'#'.$parsed['lineEnding'])
            );
        }

        return $parsed;
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
        $parsed = $this->parseJson($config, array(
            'hhvm' => true,
            'php' => PHP_VERSION_ID,
        ));

        if (!is_int($parsed['php']) || $parsed['php'] < 50306) {
            throw new \InvalidArgumentException(sprintf(
                'Expected int >= 50306 value for "php", got "%s".',
                is_object($parsed['php']) ? get_class($parsed['php']) : gettype($parsed['php']).'#'.$parsed['php'])
            );
        }

        if (!is_bool($parsed['hhvm'])) {
            throw new \InvalidArgumentException(sprintf(
                'Expected bool value for "hhvm", got "%s".',
                is_object($parsed['hhvm']) ? get_class($parsed['hhvm']) : gettype($parsed['hhvm']).'#'.$parsed['hhvm'])
            );
        }

        return $parsed;
    }

    /**
     * Parses the '--RULESET--' block of a '.test' file and determines what fixers should be used.
     *
     * @param string $config
     *
     * @return RuleSet
     */
    protected function determineRuleset($config)
    {
        return new RuleSet($this->parseJson($config));
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
        $parsed = $this->parseJson($config, array(
            'checkPriority' => true,
        ));

        if (!is_bool($parsed['checkPriority'])) {
            throw new \InvalidArgumentException(sprintf(
                'Expected bool value for "checkPriority", got "%s".',
                is_object($parsed['checkPriority']) ? get_class($parsed['checkPriority']) : gettype($parsed['checkPriority']).'#'.$parsed['checkPriority'])
            );
        }

        return $parsed;
    }

    /**
     * @param string|null $code
     * @param SplFileInfo $file
     *
     * @return string
     */
    protected function determineExpectedCode($code, SplFileInfo $file)
    {
        $code = $this->determineCode($code, $file, '-out.php');

        if (null === $code) {
            throw new \InvalidArgumentException('Missing expected code.');
        }

        return $code;
    }

    /**
     * @param string|null $code
     * @param SplFileInfo $file
     *
     * @return string|null
     */
    protected function determineInputCode($code, SplFileInfo $file)
    {
        return $this->determineCode($code, $file, '-in.php');
    }

    /**
     * @param string|null $code
     * @param SplFileInfo $file
     * @param string      $suffix
     *
     * @return string|null
     */
    private function determineCode($code, SplFileInfo $file, $suffix)
    {
        if (null !== $code) {
            return $code;
        }

        $candidateFile = new SplFileInfo($file->getPathname().$suffix, '', '');
        if ($candidateFile->isFile()) {
            return $candidateFile->getContents();
        }
    }

    /**
     * @param string|null $encoded
     * @param array|null  $template
     *
     * @return array
     */
    private function parseJson($encoded, array $template = null)
    {
        // content is optional if template is provided
        if (!$encoded && null !== $template) {
            $decoded = array();
        } else {
            $decoded = json_decode($encoded, true);

            if (JSON_ERROR_NONE !== json_last_error()) {
                throw new \InvalidArgumentException(sprintf('Malformed JSON: "%s", error: "%s".', $encoded, json_last_error_msg()));
            }
        }

        if (null !== $template) {
            $decoded = array_merge(
                $template,
                array_intersect_key(
                    $decoded,
                    array_flip(array_keys($template))
                )
            );
        }

        return $decoded;
    }
}
