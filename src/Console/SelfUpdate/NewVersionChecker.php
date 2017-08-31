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
     * @var int[]
     */
    private $currentVersion;

    /**
     * @var null|array<int[]>
     */
    private $availableVersions;

    /**
     * @param string       $currentVersion
     * @param GithubClient $githubClient
     */
    public function __construct($currentVersion, GithubClient $githubClient = null)
    {
        $this->currentVersion = $this->parseVersion($currentVersion);

        if (null === $githubClient) {
            $githubClient = new GithubClient();
        }
        $this->githubClient = $githubClient;
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

        foreach ($this->availableVersions as $availableVersion) {
            if ($availableVersion[0] === $this->currentVersion[0]) {
                return $this->returnIfNewer($availableVersion);
            }
        }

        return null;
    }

    /**
     * @param int[] $newVersion
     *
     * @return null|string
     */
    private function returnIfNewer($newVersion)
    {
        if (1 === $this->compareVersions($newVersion, $this->currentVersion)) {
            return 'v'.implode('.', $newVersion);
        }

        return null;
    }

    private function retrieveAvailableVersions()
    {
        if (null !== $this->availableVersions) {
            return;
        }

        foreach ($this->githubClient->getTags() as $tag) {
            $version = $this->parseVersion($tag['name']);
            if (null !== $version) {
                $this->availableVersions[] = $version;
            }
        }

        // sort from newest version to oldest
        usort($this->availableVersions, array($this, 'compareVersions'));
        $this->availableVersions = array_reverse($this->availableVersions);
    }

    /**
     * @param int[] $versionA
     * @param int[] $versionB
     *
     * @return int
     */
    private function compareVersions(array $versionA, array $versionB)
    {
        for ($i = 0; $i <= 2; ++$i) {
            if ($versionA[$i] < $versionB[$i]) {
                return -1;
            }

            if ($versionA[$i] > $versionB[$i]) {
                return 1;
            }
        }

        return 0;
    }

    /**
     * @param string $tag
     *
     * @return null|int[]
     */
    private function parseVersion($tag)
    {
        if (preg_match(
            '/^v?(?<major>\d+)\.(?<minor>\d+)\.(?<patch>\d+)$/',
            $tag,
            $matches
        )) {
            return array(
                (int) $matches['major'],
                (int) $matches['minor'],
                (int) $matches['patch'],
            );
        }

        return null;
    }
}
