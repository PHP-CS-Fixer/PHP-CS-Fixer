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

use PhpCsFixer\ConfigInterface;
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
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class CiConfigurationTest extends TestCase
{
    public function testThatPhpVersionEnvsAreSetProperly(): void
    {
        self::assertSame(
            [
                'PHP_MAX' => $this->getMaxPhpVersionFromEntryFile(),
                'PHP_MIN' => $this->getMinPhpVersionFromEntryFile(),
            ],
            self::getGitHubCiEnvs(),
        );
    }

    public function testTestJobsRunOnEachPhp(): void
    {
        $supportedMinPhp = (float) $this->getMinPhpVersionFromEntryFile();
        $supportedMaxPhp = (float) $this->getMaxPhpVersionFromEntryFile();

        $supportedVersions = self::generateMinorVersionsRange($supportedMinPhp, $supportedMaxPhp);

        self::assertTrue(\count($supportedVersions) > 0);

        $ciVersions = self::getAllPhpVersionsUsedByCiForTests();

        self::assertNotEmpty($ciVersions);

        self::assertSupportedPhpVersionsAreCoveredByCiJobs($supportedVersions, $ciVersions);
        self::assertUpcomingPhpVersionIsCoveredByCiJob(end($supportedVersions), $ciVersions);
        self::assertSupportedPhpVersionsAreCoveredByCiJobs($supportedVersions, $this->getPhpVersionsUsedForBuildingOfficialImages());
        self::assertSupportedPhpVersionsAreCoveredByCiJobs($supportedVersions, $this->getPhpVersionsUsedForBuildingLocalImages());
        self::assertPhpCompatibilityRangeIsValid($supportedMinPhp, $supportedMaxPhp);
    }

    public function testDeploymentJobRunOnLatestStablePhpThatIsSupportedByTool(): void
    {
        $ciVersionsForDeployment = self::getPhpVersionUsedByCiForDeployments();
        $ciVersions = self::getAllPhpVersionsUsedByCiForTests();
        $expectedPhp = $this->getMaxPhpVersionFromEntryFile();

        if (\in_array($expectedPhp.'snapshot', $ciVersions, true)) {
            // last version of used PHP is snapshot. we should test against previous one, that is stable
            $expectedPhp = (string) ((float) $expectedPhp - 0.1);
        }

        self::assertTrue(
            version_compare($expectedPhp, $ciVersionsForDeployment, 'eq'),
            \sprintf('Expects %s to be %s', $ciVersionsForDeployment, $expectedPhp)
        );
    }

    public function testDockerCIBuildsComposeServices(): void
    {
        $compose = Yaml::parseFile(__DIR__.'/../../compose.yaml');
        $composeServices = array_keys($compose['services']);
        sort($composeServices);

        $ci = Yaml::parseFile(__DIR__.'/../../.github/workflows/docker.yml');
        $ciServices = array_map(
            static fn ($item) => $item['docker-service'],
            $ci['jobs']['docker-compose-build']['strategy']['matrix']['include']
        );
        sort($ciServices);

        self::assertSame($composeServices, $ciServices);
    }

    public static function testThatAlpineVersionsAreInSync(): void
    {
        $yaml = Yaml::parseFile(__DIR__.'/../../.github/workflows/release.yml');
        $releaseMap = [];
        foreach ($yaml['jobs']['docker-images']['strategy']['matrix']['include'] as $item) {
            $releaseMap[$item['php-version']] = $item['alpine-version'];
        }

        $yaml = Yaml::parseFile(__DIR__.'/../../compose.yaml');
        $dockerMap = [];
        foreach ($yaml['services'] as $item) {
            if (isset($item['build']['args']['PHP_VERSION'], $item['build']['args']['ALPINE_VERSION'])) {
                // PHP 8.5 at this point is only allowed for local development and is not a part of Docker releases
                if (str_starts_with($item['build']['args']['PHP_VERSION'], '8.5')) {
                    continue;
                }

                $dockerMap[$item['build']['args']['PHP_VERSION']] = $item['build']['args']['ALPINE_VERSION'];
            }
        }

        self::assertSame($dockerMap, $releaseMap, 'Expects release.yml and compose.yaml to use same Alpine versions for same PHP versions.');

        Preg::matchAll(
            '/(?:ALPINE_VERSION=|alpine:)(\d+\.\d+)/',
            (string) file_get_contents(__DIR__.'/../../Dockerfile'),
            $dockerVersions
        );

        $dockerVersions = $dockerVersions[1];
        self::assertCount(2, $dockerVersions);
        self::assertSame($dockerVersions[0], $dockerVersions[1], 'Expects both Alpine versions in Dockerfile to be the same.');
        natsort($dockerMap);
        $alpineHighestVersion = end($dockerMap);

        self::assertSame($alpineHighestVersion, $dockerVersions[0], 'Expects Alpine version used in Dockerfile to be highest Alpine version used in compose.yaml.');
    }

    /**
     * @return list<numeric-string>
     */
    private static function generateMinorVersionsRange(float $from, float $to): array
    {
        $range = [];
        $lastMinorVersions = [7.4];
        $version = $from;

        while ($version <= $to) {
            $range[] = \sprintf('%.1f', $version);

            if (\in_array($version, $lastMinorVersions, true)) {
                $version = ceil($version);
            } else {
                $version += 0.1;
            }
        }

        return $range;
    }

    private static function ensureTraversableContainsIdenticalIsAvailable(): void
    {
        if (!class_exists(TraversableContainsIdentical::class)) {
            self::markTestSkipped('TraversableContainsIdentical not available.');
        }
    }

    /**
     * @param numeric-string       $lastSupportedVersion
     * @param list<numeric-string> $ciVersions
     */
    private static function assertUpcomingPhpVersionIsCoveredByCiJob(string $lastSupportedVersion, array $ciVersions): void
    {
        self::ensureTraversableContainsIdenticalIsAvailable();

        self::assertThat($ciVersions, self::logicalOr(
            // if `$lastsupportedVersion` is already a snapshot version
            new TraversableContainsIdentical(\sprintf('%.1fsnapshot', $lastSupportedVersion)),
            // if `$lastsupportedVersion` is not snapshot version, expect CI to run snapshot of next PHP version
            new TraversableContainsIdentical('nightly'),
            new TraversableContainsIdentical(\sprintf('%.1fsnapshot', $lastSupportedVersion + 0.1)),
            // GitHub CI uses just versions, without suffix, e.g. 8.1 for 8.1snapshot as of writing
            new TraversableContainsIdentical(\sprintf('%.1f', $lastSupportedVersion + 0.1)),
            new TraversableContainsIdentical(\sprintf('%.1f', floor($lastSupportedVersion + 1.0)))
        ));
    }

    /**
     * @param list<numeric-string>     $supportedVersions
     * @param array<array-key, string> $ciVersions
     */
    private static function assertSupportedPhpVersionsAreCoveredByCiJobs(array $supportedVersions, array $ciVersions): void
    {
        $lastSupportedVersion = array_pop($supportedVersions);

        foreach ($supportedVersions as $expectedVersion) {
            self::assertContains($expectedVersion, $ciVersions);
        }

        self::ensureTraversableContainsIdenticalIsAvailable();

        self::assertThat($ciVersions, self::logicalOr(
            new TraversableContainsIdentical($lastSupportedVersion),
            new TraversableContainsIdentical(\sprintf('%.1fsnapshot', $lastSupportedVersion))
        ));
    }

    private static function assertPhpCompatibilityRangeIsValid(float $supportedMinPhp, float $supportedMaxPhp): void
    {
        $matchResult = Preg::match(
            '/<config name="testVersion" value="(?<min>\d+\.\d+)-(?<max>\d+\.\d+)"\/>/',
            (string) file_get_contents(__DIR__.'/../../dev-tools/php-compatibility/phpcs-php-compatibility.xml'),
            $capture
        );

        if (!$matchResult) {
            throw new \LogicException('Can\'t parse PHP version range for verifying compatibility.');
        }

        self::assertSame($supportedMinPhp, (float) $capture['min']);
        self::assertSame($supportedMaxPhp, (float) $capture['max']);
    }

    private function convertPhpVerIdToNiceVer(string $verId): string
    {
        $matchResult = Preg::match('/^(?<major>\d{1,2})_?(?<minor>\d{2})_?(?<patch>\d{2})$/', $verId, $capture);
        if (!$matchResult) {
            throw new \LogicException(\sprintf('Can\'t parse version "%s" id.', $verId));
        }

        return \sprintf('%d.%d', $capture['major'], $capture['minor']);
    }

    private function getMaxPhpVersionFromEntryFile(): string
    {
        return ConfigInterface::PHP_VERSION_SYNTAX_SUPPORTED;
    }

    private function getMinPhpVersionFromEntryFile(): string
    {
        $tokens = Tokens::fromCode((string) file_get_contents(__DIR__.'/../../php-cs-fixer'));
        $sequence = $tokens->findSequence([
            [\T_STRING, 'PHP_VERSION_ID'],
            '<',
            [\T_INT_CAST],
            [\T_CONSTANT_ENCAPSED_STRING],
        ]);

        if (null === $sequence) {
            throw new \LogicException("Can't find version - perhaps entry file was modified?");
        }

        $phpVerId = trim(end($sequence)->getContent(), '\'');

        return $this->convertPhpVerIdToNiceVer($phpVerId);
    }

    private static function getPhpVersionUsedByCiForDeployments(): string
    {
        $yaml = Yaml::parseFile(__DIR__.'/../../.github/workflows/ci.yml');

        $version = $yaml['jobs']['deployment']['env']['php-version'];

        return \is_string($version) ? $version : \sprintf('%.1f', $version);
    }

    /**
     * @return array<string, string>
     */
    private static function getGitHubCiEnvs(): array
    {
        $yaml = Yaml::parseFile(__DIR__.'/../../.github/workflows/ci.yml');

        return $yaml['env'];
    }

    /**
     * @return list<numeric-string>
     */
    private static function getAllPhpVersionsUsedByCiForTests(): array
    {
        $yaml = Yaml::parseFile(__DIR__.'/../../.github/workflows/ci.yml');

        $phpVersions = $yaml['jobs']['tests']['strategy']['matrix']['php-version'] ?? [];

        foreach ($yaml['jobs']['tests']['strategy']['matrix']['include'] as $job) {
            $phpVersions[] = $job['php-version'];
        }

        return array_unique($phpVersions); // @phpstan-ignore return.type (we know it's a list of parsed strings)
    }

    /**
     * @return array<array-key, string>
     */
    private function getPhpVersionsUsedForBuildingOfficialImages(): array
    {
        $yaml = Yaml::parseFile(__DIR__.'/../../.github/workflows/release.yml');

        return array_map(
            static fn ($item) => $item['php-version'],
            $yaml['jobs']['docker-images']['strategy']['matrix']['include']
        );
    }

    /**
     * @return array<array-key, string>
     */
    private function getPhpVersionsUsedForBuildingLocalImages(): array
    {
        $yaml = Yaml::parseFile(__DIR__.'/../../.github/workflows/docker.yml');

        return array_map(
            static fn ($item) => substr($item, 4),
            array_filter(
                array_map(
                    static fn ($item) => $item['docker-service'],
                    $yaml['jobs']['docker-compose-build']['strategy']['matrix']['include']
                ),
                static fn ($item) => str_starts_with($item, 'php-')
            )
        );
    }
}
