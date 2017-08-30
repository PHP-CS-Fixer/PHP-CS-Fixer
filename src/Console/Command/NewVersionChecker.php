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

namespace PhpCsFixer\Console\Command;

/**
 * @internal
 */
final class NewVersionChecker
{
    /**
     * @var int[]
     */
    private $currentVersion;

    /**
     * @var null|array<int[]>
     */
    private $availableVersions;

    /**
     * @param string $currentVersion
     */
    public function __construct($currentVersion)
    {
        $this->currentVersion = $this->parseVersion($currentVersion);
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

        $url = 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/tags';

        $result = @file_get_contents(
            $url,
            false,
            stream_context_create(array(
                'http' => array(
                    'header' => 'User-Agent: FriendsOfPHP/PHP-CS-Fixer',
                ),
            ))
        );

        if (false === $result) {
            throw new \RuntimeException(sprintf('Failed to load tags at "%s".', $url));
        }

        $result = json_decode($result, true);
        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new \RuntimeException(sprintf(
                'Failed to read response from "%s" as JSON: %s.',
                $url,
                json_last_error_msg()
            ));
        }

        foreach ($result as $tag) {
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
