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

namespace PhpCsFixer\Tests\AutoReview;

use PhpCsFixer\Preg;
use PhpCsFixer\Tests\TestCase;
use PhpCsFixer\Tokenizer\Tokens;
use PHPUnit\Framework\Constraint\TraversableContains;
use Symfony\Component\Yaml\Yaml;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @coversNothing
 * @group auto-review
 * @group covers-nothing
 */
final class CiConfigurationTest extends TestCase
{
    public function testTestJobsRunOnEachPhp()
    {
        $supportedVersions = [];
        $supportedMinPhp = (float) $this->getMinPhpVersionFromEntryFile();
        $supportedMaxPhp = (float) $this->getMaxPhpVersionFromEntryFile();

        if ($supportedMinPhp < 7) {
            $supportedMinPhp = 7;
            $supportedVersions[] = '5.6';
        }

        for ($version = $supportedMinPhp; $version <= $supportedMaxPhp; $version += 0.1) {
            $supportedVersions[] = sprintf('%.1f', $version);
        }

        $ciVersions = $this->getAllPhpVersionsUsedByCiForTests();

        static::assertGreaterThanOrEqual(1, \count($ciVersions));

        self::assertSupportedPhpVersionsAreCoveredByCiJobs($supportedVersions, $ciVersions);
        self::assertUpcomingPhpVersionIsCoveredByCiJob(end($supportedVersions), $ciVersions);
    }

    public function testDeploymentJobsRunOnLatestStablePhpThatIsSupportedByTool()
    {
        $ciVersionsForDeployments = $this->getAllPhpVersionsUsedByCiForDeployments();
        $ciVersions = $this->getAllPhpVersionsUsedByCiForTests();
        $expectedPhp = $this->getMaxPhpVersionFromEntryFile();

        if (\in_array($expectedPhp.'snapshot', $ciVersions, true)) {
            // last version of used PHP is snapshot. we should test against previous one, that is stable
            $expectedPhp = (string) ((float) $expectedPhp - 0.1);
        }

        static::assertGreaterThanOrEqual(1, \count($ciVersionsForDeployments));
        static::assertGreaterThanOrEqual(1, \count($ciVersions));

        foreach ($ciVersionsForDeployments as $ciVersionsForDeployment) {
            static::assertTrue(
                version_compare($expectedPhp, $ciVersionsForDeployment, 'eq'),
                sprintf('Expects %s to be %s', $ciVersionsForDeployment, $expectedPhp)
            );
        }
    }

    private static function ensureTraversableContainsIsAvailable()
    {
        if (!class_exists(TraversableContains::class)) {
            static::markTestSkipped('TraversableContains not available.');
        }

        try {
            new TraversableContains('');
        } catch (\Error $e) {
            if (false === strpos($e->getMessage(), 'Cannot instantiate abstract class')) {
                throw $e;
            }

            static::markTestSkipped('TraversableContains not available.');
        }
    }

    private static function assertUpcomingPhpVersionIsCoveredByCiJob($lastSupportedVersion, array $ciVersions)
    {
        self::ensureTraversableContainsIsAvailable();

        static::assertThat($ciVersions, static::logicalOr(
            // if `$lastsupportedVersion` is already a snapshot version
            new TraversableContains(sprintf('%.1fsnapshot', $lastSupportedVersion)),
            // if `$lastsupportedVersion` is not snapshot version, expect CI to run snapshot of next PHP version
            new TraversableContains('nightly'),
            new TraversableContains(sprintf('%.1fsnapshot', $lastSupportedVersion + 0.1)),
            // GitHub CI uses just versions, without suffix, e.g. 8.1 for 8.1snapshot as of writing
            new TraversableContains(sprintf('%.1f', $lastSupportedVersion + 0.1)),
            new TraversableContains(sprintf('%.1f', round($lastSupportedVersion + 1)))
        ));
    }

    private static function assertSupportedPhpVersionsAreCoveredByCiJobs(array $supportedVersions, array $ciVersions)
    {
        $lastSupportedVersion = array_pop($supportedVersions);

        foreach ($supportedVersions as $expectedVersion) {
            static::assertContains($expectedVersion, $ciVersions);
        }

        self::ensureTraversableContainsIsAvailable();

        static::assertThat($ciVersions, static::logicalOr(
            new TraversableContains($lastSupportedVersion),
            new TraversableContains(sprintf('%.1fsnapshot', $lastSupportedVersion))
        ));
    }

    private function getAllPhpVersionsUsedByCiForDeployments()
    {
        $jobs = array_filter($this->getTravisJobs(), function ($job) {
            return 'Deployment' === $job['stage'];
        });

        return array_map(function ($job) {
            return \is_string($job['php']) ? $job['php'] : sprintf('%.1f', $job['php']);
        }, $jobs);
    }

    private function getAllPhpVersionsUsedByCiForTests()
    {
        return array_merge(
            $this->getPhpVersionsUsedByTravis(),
            $this->getPhpVersionsUsedByGitHub()
        );
    }

    private function convertPhpVerIdToNiceVer($verId)
    {
        $matchResult = Preg::match('/^(?<major>\d{1,2})(?<minor>\d{2})(?<patch>\d{2})$/', $verId, $capture);
        if (1 !== $matchResult) {
            throw new \LogicException("Can't parse version id.");
        }

        return sprintf('%d.%d', $capture['major'], $capture['minor']);
    }

    private function getMaxPhpVersionFromEntryFile()
    {
        $tokens = Tokens::fromCode(file_get_contents(__DIR__.'/../../php-cs-fixer'));
        $sequence = $tokens->findSequence([
            [T_STRING, 'PHP_VERSION_ID'],
            [T_IS_GREATER_OR_EQUAL],
            [T_LNUMBER],
        ]);

        if (null === $sequence) {
            throw new \LogicException("Can't find version - perhaps entry file was modified?");
        }

        $phpVerId = (int) end($sequence)->getContent();

        return $this->convertPhpVerIdToNiceVer((string) ($phpVerId - 100));
    }

    private function getMinPhpVersionFromEntryFile()
    {
        $tokens = Tokens::fromCode(file_get_contents(__DIR__.'/../../php-cs-fixer'));
        $sequence = $tokens->findSequence([
            [T_STRING, 'PHP_VERSION_ID'],
            '<',
            [T_LNUMBER],
        ]);

        if (null === $sequence) {
            throw new \LogicException("Can't find version - perhaps entry file was modified?");
        }

        $phpVerId = end($sequence)->getContent();

        return $this->convertPhpVerIdToNiceVer($phpVerId);
    }

    private function getTravisJobs()
    {
        $yaml = Yaml::parse(file_get_contents(__DIR__.'/../../.travis.yml'));

        return $yaml['jobs']['include'];
    }

    private function getPhpVersionsUsedByGitHub()
    {
        $yaml = Yaml::parse(file_get_contents(__DIR__.'/../../.github/workflows/ci.yml'));

        $phpVersions = isset($yaml['jobs']['tests']['strategy']['matrix']['php-version']) ? $yaml['jobs']['tests']['strategy']['matrix']['php-version'] : [];

        foreach ($yaml['jobs']['tests']['strategy']['matrix']['include'] as $job) {
            $phpVersions[] = $job['php-version'];
        }

        return $phpVersions;
    }

    private function getPhpVersionsUsedByTravis()
    {
        $jobs = array_filter($this->getTravisJobs(), function ($job) {
            return false !== strpos($job['stage'], 'Test');
        });

        return array_map(function ($job) {
            return (string) $job['php'];
        }, $jobs);
    }
}
