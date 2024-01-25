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
use Symfony\Component\Finder\SplFileInfo;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
abstract class AbstractIntegrationCaseFactory implements IntegrationCaseFactoryInterface
{
    public function create(SplFileInfo $file): IntegrationCase
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
                [
                    'config' => null,
                    'settings' => null,
                    'requirements' => null,
                    'expect' => null,
                    'input' => null,
                ],
                $match
            );

            return new IntegrationCase(
                $file->getRelativePathname(),
                $this->determineTitle($file, $match['title']),
                $this->determineSettings($file, $match['settings']),
                $this->determineRequirements($file, $match['requirements']),
                $this->determineConfig($file, $match['config']),
                $this->determineRuleset($file, $match['ruleset']),
                $this->determineExpectedCode($file, $match['expect']),
                $this->determineInputCode($file, $match['input'])
            );
        } catch (\InvalidArgumentException $e) {
            throw new \InvalidArgumentException(
                sprintf('%s Test file: "%s".', $e->getMessage(), $file->getPathname()),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * Parses the '--CONFIG--' block of a '.test' file.
     *
     * @return array{indent: string, lineEnding: string}
     */
    protected function determineConfig(SplFileInfo $file, ?string $config): array
    {
        $parsed = $this->parseJson($config, [
            'indent' => '    ',
            'lineEnding' => "\n",
        ]);

        if (!\is_string($parsed['indent'])) {
            throw new \InvalidArgumentException(sprintf(
                'Expected string value for "indent", got "%s".',
                \is_object($parsed['indent']) ? \get_class($parsed['indent']) : \gettype($parsed['indent']).'#'.$parsed['indent']
            ));
        }

        if (!\is_string($parsed['lineEnding'])) {
            throw new \InvalidArgumentException(sprintf(
                'Expected string value for "lineEnding", got "%s".',
                \is_object($parsed['lineEnding']) ? \get_class($parsed['lineEnding']) : \gettype($parsed['lineEnding']).'#'.$parsed['lineEnding']
            ));
        }

        return $parsed;
    }

    /**
     * Parses the '--REQUIREMENTS--' block of a '.test' file and determines requirements.
     *
     * @return array{php: int, "php<": int, os: list<string>}
     */
    protected function determineRequirements(SplFileInfo $file, ?string $config): array
    {
        $parsed = $this->parseJson($config, [
            'php' => \PHP_VERSION_ID,
            'php<' => PHP_INT_MAX,
            'os' => ['Linux', 'Darwin', 'Windows'],
        ]);

        if (!\is_int($parsed['php'])) {
            throw new \InvalidArgumentException(sprintf(
                'Expected int value like 50509 for "php", got "%s".',
                get_debug_type($parsed['php']).'#'.$parsed['php'],
            ));
        }

        if (!\is_int($parsed['php<'])) {
            throw new \InvalidArgumentException(sprintf(
                'Expected int value like 80301 for "php<", got "%s".',
                get_debug_type($parsed['php<']).'#'.$parsed['php<'],
            ));
        }

        if (!\is_array($parsed['os'])) {
            throw new \InvalidArgumentException(sprintf(
                'Expected array of OS names for "os", got "%s".',
                get_debug_type($parsed['os']).' ('.$parsed['os'].')',
            ));
        }

        return $parsed;
    }

    /**
     * Parses the '--RULESET--' block of a '.test' file and determines what fixers should be used.
     */
    protected function determineRuleset(SplFileInfo $file, string $config): RuleSet
    {
        return new RuleSet($this->parseJson($config));
    }

    /**
     * Parses the '--TEST--' block of a '.test' file and determines title.
     */
    protected function determineTitle(SplFileInfo $file, string $config): string
    {
        return $config;
    }

    /**
     * Parses the '--SETTINGS--' block of a '.test' file and determines settings.
     *
     * @return array{checkPriority: bool, deprecations: list<string>}
     */
    protected function determineSettings(SplFileInfo $file, ?string $config): array
    {
        $parsed = $this->parseJson($config, [
            'checkPriority' => true,
            'deprecations' => [],
        ]);

        if (!\is_bool($parsed['checkPriority'])) {
            throw new \InvalidArgumentException(sprintf(
                'Expected bool value for "checkPriority", got "%s".',
                \is_object($parsed['checkPriority']) ? \get_class($parsed['checkPriority']) : \gettype($parsed['checkPriority']).'#'.$parsed['checkPriority']
            ));
        }

        if (!\is_array($parsed['deprecations'])) {
            throw new \InvalidArgumentException(sprintf(
                'Expected array value for "deprecations", got "%s".',
                \is_object($parsed['deprecations']) ? \get_class($parsed['deprecations']) : \gettype($parsed['deprecations']).'#'.$parsed['deprecations']
            ));
        }

        foreach ($parsed['deprecations'] as $index => $deprecation) {
            if (!\is_string($deprecation)) {
                throw new \InvalidArgumentException(sprintf(
                    'Expected only string value for "deprecations", got "%s" @ index %d.',
                    \is_object($deprecation) ? \get_class($deprecation) : \gettype($deprecation).'#'.$deprecation,
                    $index
                ));
            }
        }

        return $parsed;
    }

    protected function determineExpectedCode(SplFileInfo $file, ?string $code): string
    {
        $code = $this->determineCode($file, $code, '-out.php');

        if (null === $code) {
            throw new \InvalidArgumentException('Missing expected code.');
        }

        return $code;
    }

    protected function determineInputCode(SplFileInfo $file, ?string $code): ?string
    {
        return $this->determineCode($file, $code, '-in.php');
    }

    private function determineCode(SplFileInfo $file, ?string $code, string $suffix): ?string
    {
        if (null !== $code) {
            return $code;
        }

        $candidateFile = new SplFileInfo($file->getPathname().$suffix, '', '');
        if ($candidateFile->isFile()) {
            return $candidateFile->getContents();
        }

        return null;
    }

    /**
     * @param null|array<string, mixed> $template
     *
     * @return array<string, mixed>
     */
    private function parseJson(?string $encoded, array $template = null): array
    {
        // content is optional if template is provided
        if ((null === $encoded || '' === $encoded) && null !== $template) {
            $decoded = [];
        } else {
            $decoded = json_decode($encoded, true, 512, JSON_THROW_ON_ERROR);
        }

        if (null !== $template) {
            foreach ($template as $index => $value) {
                if (!\array_key_exists($index, $decoded)) {
                    $decoded[$index] = $value;
                }
            }
        }

        return $decoded;
    }
}
