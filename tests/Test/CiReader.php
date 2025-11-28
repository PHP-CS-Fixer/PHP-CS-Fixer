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

use PhpCsFixer\Preg;
use Symfony\Component\Yaml\Yaml;

/**
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class CiReader
{
    /**
     * @return list<numeric-string>
     */
    public static function getAllPhpVersionsUsedByCiForTests(): array
    {
        $phpVersions = array_filter(
            self::getAllPhpBuildsUsedByCiForTests(),
            static fn ($version) => is_numeric($version)
        );

        return $phpVersions; // @phpstan-ignore return.type (we know it's a list of parsed strings)
    }

    /**
     * @return list<string>
     */
    public static function getAllPhpBuildsUsedByCiForTests(): array
    {
        $yaml = Yaml::parseFile(__DIR__.'/../../.github/workflows/ci.yml');

        $phpVersions = []
            + ($yaml['jobs']['tests']['strategy']['matrix']['php-version'] ?? [])
            + array_map(
                static fn (array $job) => $job['php-version'] ?? null,
                $yaml['jobs']['tests']['strategy']['matrix']['include']
            );

        $phpVersions = array_filter(
            array_unique($phpVersions),
            static fn ($version) => 'nightly' === $version || Preg::match('/^\d+(\.\d+)?(snapshot)?$/', $version)
        );

        return $phpVersions; // @phpstan-ignore return.type (we know it's a list of parsed strings)
    }

    public static function getPhpVersionUsedByCiForDeployments(): string
    {
        $yaml = Yaml::parseFile(__DIR__.'/../../.github/workflows/ci.yml');

        $version = $yaml['jobs']['deployment']['env']['php-version'];

        return \is_string($version) ? $version : \sprintf('%.1f', $version);
    }

    /**
     * @return array<string, string>
     */
    public static function getGitHubCiEnvs(): array
    {
        $yaml = Yaml::parseFile(__DIR__.'/../../.github/workflows/ci.yml');

        return $yaml['env'];
    }
}
