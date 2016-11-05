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
use PhpCsFixer\WhitespacesFixerConfig;
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

            $config = $this->determineConfig($match['config']);

            return new IntegrationCase(
                $file->getRelativePathname(),
                $match['title'],
                $this->determineSettings($match['settings']),
                $this->determineRequirements($match['requirements']),
                $this->determineFixers($match['ruleset'], new WhitespacesFixerConfig($config['indent'], $config['lineEnding'])),
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
        return $this->parseJson($config, array(
            'indent' => '    ',
            'lineEnding' => "\n",
        ));
    }

    /**
     * Parses the '--RULESET--' block of a '.test' file and determines what fixers should be used.
     *
     * @param string                 $config
     * @param WhitespacesFixerConfig $sharedFixerConfig
     *
     * @return FixerInterface[]
     */
    protected function determineFixers($config, WhitespacesFixerConfig $sharedFixerConfig)
    {
        return FixerFactory::create()
            ->registerBuiltInFixers()
            ->useRuleSet(new RuleSet($this->parseJson($config)))
            ->setWhitespacesConfig($sharedFixerConfig)
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
        return $this->parseJson($config, array(
            'hhvm' => true,
            'php' => PHP_VERSION_ID,
        ));
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
        return $this->parseJson($config, array(
            'checkPriority' => true,
        ));
    }

    protected function determineExpectedCode($code, $file)
    {
        $code = $this->determineCode($code, $file, '-out.php');

        if (null === $code) {
            throw new \InvalidArgumentException('Missing expected code.');
        }

        return $code;
    }

    protected function determineInputCode($code, $file)
    {
        return $this->determineCode($code, $file, '-in.php');
    }

    private function determineCode($code, $file, $suffix)
    {
        if (null !== $code) {
            return $code;
        }

        $candidatePath = $file->getPathname().$suffix;

        $candidateFile = new SplFileInfo($candidatePath, '', '');
        if ($candidateFile->isFile()) {
            return $candidateFile->getContents();
        }
    }

    private function parseJson($encoded, array $template = null)
    {
        // content is optional if template is provided
        if (!$encoded && null !== $template) {
            $encoded = '[]';
        }

        $decoded = json_decode($encoded, true);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new \InvalidArgumentException(sprintf('Malformed JSON: "%s".', $encoded));
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
