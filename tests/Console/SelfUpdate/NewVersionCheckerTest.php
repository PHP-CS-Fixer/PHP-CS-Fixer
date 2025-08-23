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

namespace PhpCsFixer\Tests\Console\SelfUpdate;

use PhpCsFixer\Console\SelfUpdate\GithubClientInterface;
use PhpCsFixer\Console\SelfUpdate\NewVersionChecker;
use PhpCsFixer\Tests\TestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Console\SelfUpdate\NewVersionChecker
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class NewVersionCheckerTest extends TestCase
{
    public function testGetLatestVersion(): void
    {
        $checker = new NewVersionChecker($this->createGithubClientDouble());

        self::assertSame('v2.4.1', $checker->getLatestVersion());
    }

    /**
     * @dataProvider provideGetLatestVersionOfMajorCases
     */
    public function testGetLatestVersionOfMajor(int $majorVersion, ?string $expectedVersion): void
    {
        $checker = new NewVersionChecker($this->createGithubClientDouble());

        self::assertSame($expectedVersion, $checker->getLatestVersionOfMajor($majorVersion));
    }

    /**
     * @return iterable<int, array{int, null|string}>
     */
    public static function provideGetLatestVersionOfMajorCases(): iterable
    {
        yield [1, 'v1.13.2'];

        yield [2, 'v2.4.1'];

        yield [4, null];
    }

    /**
     * @dataProvider provideCompareVersionsCases
     */
    public function testCompareVersions(string $versionA, string $versionB, int $expectedResult): void
    {
        $checker = new NewVersionChecker($this->createGithubClientDouble());

        self::assertSame(
            $expectedResult,
            $checker->compareVersions($versionA, $versionB)
        );
        self::assertSame(
            -$expectedResult,
            $checker->compareVersions($versionB, $versionA)
        );
    }

    /**
     * @return iterable<int, array{string, string, int}>
     */
    public static function provideCompareVersionsCases(): iterable
    {
        foreach ([
            ['1.0.0-alpha', '1.0.0', -1],
            ['1.0.0-beta', '1.0.0', -1],
            ['1.0.0-RC', '1.0.0', -1],
            ['1.0.0', '1.0.0', 0],
            ['1.0.0', '1.0.1', -1],
            ['1.0.0', '1.1.0', -1],
            ['1.0.0', '2.0.0', -1],
        ] as $case) {
            // X.Y.Z vs. X.Y.Z
            yield $case;

            // vX.Y.Z vs. X.Y.Z
            $case[0] = 'v'.$case[0];

            yield $case;

            // vX.Y.Z vs. vX.Y.Z
            $case[1] = 'v'.$case[1];

            yield $case;

            // X.Y.Z vs. vX.Y.Z
            $case[0] = substr($case[0], 1);

            yield $case;
        }
    }

    private function createGithubClientDouble(): GithubClientInterface
    {
        return new class implements GithubClientInterface {
            public function getTags(): array
            {
                return [
                    'v3.0.0-RC',
                    'v2.4.1',
                    'v2.4.0',
                    'v2.3.3',
                    'v2.3.2',
                    'v2.3.1',
                    'v2.3.0',
                    'v2.2.6',
                    'v2.2.5',
                    'v2.2.4',
                    'v2.2.3',
                    'v2.2.2',
                    'v2.2.1',
                    'v2.2.0',
                    'v2.1.3',
                    'v2.1.2',
                    'v2.1.1',
                    'v2.1.0',
                    'v2.0.1',
                    'v2.0.0',
                    'v2.0.0-beta',
                    'v2.0.0-alpha',
                    'v2.0.0-RC',
                    'v1.14.0-beta',
                    'v1.13.2',
                    'v1.13.1',
                    'v1.13.0',
                    'v1.12.4',
                    'v1.12.3',
                    'v1.12.2',
                    'v1.12.1',
                ];
            }
        };
    }
}
