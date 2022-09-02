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

namespace PhpCsFixer\Tests\AutoReview;

use PhpCsFixer\Preg;
use PhpCsFixer\Tests\TestCase;
use PhpCsFixer\Tokenizer\Tokens;
use PHPUnit\Framework\Constraint\TraversableContainsIdentical;
use Symfony\Component\Yaml\Yaml;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @coversNothing
 *
 * @group auto-review
 * @group covers-nothing
 */
final class CiConfigurationTest extends TestCase
{
    public function testTestJobsRunOnEachPhp(): void
    {
        $supportedVersions = [];
        $supportedMinPhp = (float) $this->getMinPhpVersionFromEntryFile();
        $supportedMaxPhp = (float) $this->getMaxPhpVersionFromEntryFile();

        if ($supportedMaxPhp >= 8) {
            $supportedVersions = array_merge(
                $supportedVersions,
                self::generateMinorVersionsRange($supportedMinPhp, 7.4)
            );

            $supportedMinPhp = 8;
        }

        $supportedVersions = array_merge(
            $supportedVersions,
            self::generateMinorVersionsRange($supportedMinPhp, $supportedMaxPhp)
        );

        static::assertTrue(\count($supportedVersions) > 0);

        $ciVersions = $this->getAllPhpVersionsUsedByCiForTests();

        static::assertNotEmpty($ciVersions);

        self::assertSupportedPhpVersionsAreCoveredByCiJobs($supportedVersions, $ciVersions);
        self::assertUpcomingPhpVersionIsCoveredByCiJob(end($supportedVersions), $ciVersions);
    }

    public function testDeploymentJobsRunOnLatestStablePhpThatIsSupportedByTool(): void
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

    /**
     * @return list<numeric-string>
     */
    private static function generateMinorVersionsRange(float $from, float $to): array
    {
        $range = [];

        for ($version = $from; $version <= $to; $version += 0.1) {
            $range[] = sprintf('%.1f', $version);
        }

        return $range;
    }

    private static function ensureTraversableContainsIdenticalIsAvailable(): void
    {
        if (!class_exists(TraversableContainsIdentical::class)) {
            static::markTestSkipped('TraversableContainsIdentical not available.');
        }
    }

    /**
     * @param numeric-string       $lastSupportedVersion
     * @param list<numeric-string> $ciVersions
     */
    private static function assertUpcomingPhpVersionIsCoveredByCiJob(string $lastSupportedVersion, array $ciVersions): void
    {
        if ('8.1' === $lastSupportedVersion) {
            return; // no further releases available yet
        }

        self::ensureTraversableContainsIdenticalIsAvailable();

        static::assertThat($ciVersions, static::logicalOr(
            // if `$lastsupportedVersion` is already a snapshot version
            new TraversableContainsIdentical(sprintf('%.1fsnapshot', $lastSupportedVersion)),
            // if `$lastsupportedVersion` is not snapshot version, expect CI to run snapshot of next PHP version
            new TraversableContainsIdentical('nightly'),
            new TraversableContainsIdentical(sprintf('%.1fsnapshot', $lastSupportedVersion + 0.1)),
            // GitHub CI uses just versions, without suffix, e.g. 8.1 for 8.1snapshot as of writing
            new TraversableContainsIdentical(sprintf('%.1f', $lastSupportedVersion + 0.1)),
            new TraversableContainsIdentical(sprintf('%.1f', round($lastSupportedVersion + 1.0)))
        ));
    }

    /**
     * @param list<numeric-string> $supportedVersions
     * @param list<numeric-string> $ciVersions
     */
    private static function assertSupportedPhpVersionsAreCoveredByCiJobs(array $supportedVersions, array $ciVersions): void
    {
        $lastSupportedVersion = array_pop($supportedVersions);

        foreach ($supportedVersions as $expectedVersion) {
            static::assertContains($expectedVersion, $ciVersions);
        }

        self::ensureTraversableContainsIdenticalIsAvailable();

        static::assertThat($ciVersions, static::logicalOr(
            new TraversableContainsIdentical($lastSupportedVersion),
            new TraversableContainsIdentical(sprintf('%.1fsnapshot', $lastSupportedVersion))
        ));
    }

    /**
     * @return array<int, string>
     */
    private function getAllPhpVersionsUsedByCiForDeployments(): array
    {
        $jobs = array_filter($this->getGitHubJobs(), static function (array $job): bool {
            return isset($job['execute-deployment']) && 'yes' === $job['execute-deployment'];
        });

        return array_map(static function ($job): string {
            return \is_string($job['php-version']) ? $job['php-version'] : sprintf('%.1f', $job['php-version']);
        }, $jobs);
    }

    /**
     * @return list<numeric-string>
     */
    private function getAllPhpVersionsUsedByCiForTests(): array
    {
        return $this->getPhpVersionsUsedByGitHub();
    }

    private function convertPhpVerIdToNiceVer(string $verId): string
    {
        $matchResult = Preg::match('/^(?<major>\d{1,2})(?<minor>\d{2})(?<patch>\d{2})$/', $verId, $capture);
        if (1 !== $matchResult) {
            throw new \LogicException(sprintf('Can\'t parse version "%s" id.', $verId));
        }

        return sprintf('%d.%d', $capture['major'], $capture['minor']);
    }

    private function getMaxPhpVersionFromEntryFile(): string
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

    private function getMinPhpVersionFromEntryFile(): string
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

    /**
     * @return list<array<string, scalar>>
     */
    private function getGitHubJobs(): array
    {
        $yaml = Yaml::parse(file_get_contents(__DIR__.'/../../.github/workflows/ci.yml'));

        return $yaml['jobs']['tests']['strategy']['matrix']['include'];
    }

    /**
     * @return list<numeric-string>
     */
    private function getPhpVersionsUsedByGitHub(): array
    {
        $yaml = Yaml::parse(file_get_contents(__DIR__.'/../../.github/workflows/ci.yml'));

        $phpVersions = $yaml['jobs']['tests']['strategy']['matrix']['php-version'] ?? [];

        foreach ($yaml['jobs']['tests']['strategy']['matrix']['include'] as $job) {
            $phpVersions[] = $job['php-version'];
        }

        return $phpVersions;
    }
}
