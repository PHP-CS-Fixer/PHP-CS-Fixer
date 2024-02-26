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

namespace PhpCsFixer\Console\SelfUpdate;

use Composer\Semver\Comparator;
use Composer\Semver\Semver;
use Composer\Semver\VersionParser;

/**
 * @internal
 */
final class NewVersionChecker implements NewVersionCheckerInterface
{
    private GithubClientInterface $githubClient;

    private VersionParser $versionParser;

    /**
     * @var null|string[]
     */
    private $availableVersions;

    public function __construct(GithubClientInterface $githubClient)
    {
        $this->githubClient = $githubClient;
        $this->versionParser = new VersionParser();
    }

    public function getLatestVersion(): string
    {
        $this->retrieveAvailableVersions();

        return $this->availableVersions[0];
    }

    public function getLatestVersionOfMajor(int $majorVersion): ?string
    {
        $this->retrieveAvailableVersions();

        $semverConstraint = '^'.$majorVersion;

        foreach ($this->availableVersions as $availableVersion) {
            if (Semver::satisfies($availableVersion, $semverConstraint)) {
                return $availableVersion;
            }
        }

        return null;
    }

    public function compareVersions(string $versionA, string $versionB): int
    {
        $versionA = $this->versionParser->normalize($versionA);
        $versionB = $this->versionParser->normalize($versionB);

        if (Comparator::lessThan($versionA, $versionB)) {
            return -1;
        }

        if (Comparator::greaterThan($versionA, $versionB)) {
            return 1;
        }

        return 0;
    }

    private function retrieveAvailableVersions(): void
    {
        if (null !== $this->availableVersions) {
            return;
        }

        foreach ($this->githubClient->getTags() as $version) {
            try {
                $this->versionParser->normalize($version);

                if ('stable' === VersionParser::parseStability($version)) {
                    $this->availableVersions[] = $version;
                }
            } catch (\UnexpectedValueException $exception) {
                // not a valid version tag
            }
        }

        $this->availableVersions = Semver::rsort($this->availableVersions);
    }
}
