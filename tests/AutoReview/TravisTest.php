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
final class TravisTest extends TestCase
{
    public function testTestJobsRunOnEachPhp()
    {
        $expectedVersions = [];
        $expectedMinPhp = (float) $this->getMinPhpVersionFromEntryFile();
        $expectedMaxPhp = (float) $this->getMaxPhpVersionFromEntryFile();

        if ($expectedMinPhp < 7) {
            $expectedMinPhp = 7;
            $expectedVersions[] = '5.6';
        }

        for ($version = $expectedMinPhp; $version <= $expectedMaxPhp; $version += 0.1) {
            $expectedVersions[] = sprintf('%.1f', $version);
        }

        $jobs = array_filter($this->getTravisJobs(), function ($job) {
            return false !== strpos($job['stage'], 'Test');
        });
        static::assertGreaterThanOrEqual(1, \count($jobs));

        $versions = array_map(function ($job) {
            return $job['php'];
        }, $jobs);

        foreach ($expectedVersions as $expectedVersion) {
            static::assertContains($expectedVersion, $versions);
        }

        if (!class_exists(TraversableContains::class)) {
            static::markTestSkipped('TraversableContains not available.');
        }

        static::assertThat($versions, static::logicalOr(
            new TraversableContains('nightly'),
            new TraversableContains(sprintf('%.1fsnapshot', end($expectedVersions) + 0.1))
        ));
    }

    public function testDeploymentJobsRunOnLatestPhp()
    {
        $jobs = array_filter($this->getTravisJobs(), function ($job) {
            return 'Deployment' === $job['stage'];
        });
        static::assertGreaterThanOrEqual(1, \count($jobs));

        $expectedPhp = $this->getMaxPhpVersionFromEntryFile();

        foreach ($jobs as $job) {
            $jobPhp = (string) $job['php'];
            static::assertTrue(
                version_compare($expectedPhp, $jobPhp, 'eq'),
                sprintf('Expects %s to be %s', $jobPhp, $expectedPhp)
            );
        }
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

        $phpVerId = end($sequence)->getContent();

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
}
