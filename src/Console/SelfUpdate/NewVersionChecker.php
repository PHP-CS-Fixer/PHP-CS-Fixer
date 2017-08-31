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

namespace PhpCsFixer\Console\SelfUpdate;

use Composer\Semver\Comparator;
use Composer\Semver\Semver;
use Composer\Semver\VersionParser;

/**
 * @internal
 */
final class NewVersionChecker
{
    /**
     * @var GithubClient
     */
    private $githubClient;

    /**
     * @var VersionParser
     */
    private $versionParser;

    /**
     * @var string
     */
    private $currentVersion;

    /**
     * @var null|string[]
     */
    private $availableVersions;

    /**
     * @param string       $currentVersion
     * @param GithubClient $githubClient
     */
    public function __construct($currentVersion, GithubClient $githubClient = null)
    {
        $this->currentVersion = $currentVersion;

        if (null === $githubClient) {
            $githubClient = new GithubClient();
        }
        $this->githubClient = $githubClient;
        $this->versionParser = new VersionParser();
    }

    /**
     * Returns the tag of the latest version if newer than the current one.
     *
     * @return null|string
     */
    public function getLatestVersion()
    {
        $this->retrieveAvailableVersions();

        return $this->returnIfNewer($this->availableVersions[0]);
    }

    /**
     * Returns the tag of the latest minor/patch version if newer than the current one.
     *
     * @return null|string
     */
    public function getLatestVersionOfCurrentMajor()
    {
        $this->retrieveAvailableVersions();

        $currentMajorVersion = (int) $this->versionParser->normalize($this->currentVersion);
        $semverConstraint = '^'.$currentMajorVersion;

        foreach ($this->availableVersions as $availableVersion) {
            if (Semver::satisfies($availableVersion, $semverConstraint)) {
                return $this->returnIfNewer($availableVersion);
            }
        }

        return null;
    }

    /**
     * @param string $newVersion
     *
     * @return null|string
     */
    private function returnIfNewer($newVersion)
    {
        return Comparator::greaterThan($newVersion, $this->currentVersion) ? $newVersion : null;
    }

    private function retrieveAvailableVersions()
    {
        if (null !== $this->availableVersions) {
            return;
        }

        foreach ($this->githubClient->getTags() as $tag) {
            $version = $tag['name'];

            try {
                $this->versionParser->normalize($version);
                $this->availableVersions[] = $version;
            } catch (\UnexpectedValueException $exception) {
                // not a valid version tag
            }
        }

        $this->availableVersions = Semver::rsort($this->availableVersions);
    }
}
