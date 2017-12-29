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

namespace PhpCsFixer\Tests\Console\SelfUpdate;

use PhpCsFixer\Console\SelfUpdate\GithubClientInterface;
use PhpCsFixer\Console\SelfUpdate\NewVersionChecker;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Console\SelfUpdate\NewVersionChecker
 */
final class NewVersionCheckerTest extends TestCase
{
    public function testGetLatestVersion()
    {
        $checker = new NewVersionChecker($this->createGithubClientStub());

        $this->assertSame('v2.4.1', $checker->getLatestVersion());
    }

    /**
     * @param int         $majorVersion
     * @param null|string $expectedVersion
     *
     * @dataProvider provideLatestVersionOfMajorCases
     */
    public function testGetLatestVersionOfMajor($majorVersion, $expectedVersion)
    {
        $checker = new NewVersionChecker($this->createGithubClientStub());

        $this->assertSame($expectedVersion, $checker->getLatestVersionOfMajor($majorVersion));
    }

    public function provideLatestVersionOfMajorCases()
    {
        return [
            [1, 'v1.13.2'],
            [2, 'v2.4.1'],
            [4, null],
        ];
    }

    /**
     * @param string $versionA
     * @param string $versionB
     * @param int    $expectedResult
     *
     * @dataProvider provideCompareVersionsCases
     */
    public function testCompareVersions($versionA, $versionB, $expectedResult)
    {
        $checker = new NewVersionChecker($this->createGithubClientStub());

        $this->assertSame(
            $expectedResult,
            $checker->compareVersions($versionA, $versionB)
        );
        $this->assertSame(
            -$expectedResult,
            $checker->compareVersions($versionB, $versionA)
        );
    }

    public function provideCompareVersionsCases()
    {
        $cases = [];

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
            $cases[] = $case;

            // vX.Y.Z vs. X.Y.Z
            $case[0] = 'v'.$case[0];
            $cases[] = $case;

            // vX.Y.Z vs. vX.Y.Z
            $case[1] = 'v'.$case[1];
            $cases[] = $case;

            // X.Y.Z vs. vX.Y.Z
            $case[0] = substr($case[0], 1);
            $cases[] = $case;
        }

        return $cases;
    }

    private function createGithubClientStub()
    {
        $githubClient = $this->prophesize(GithubClientInterface::class);

        $githubClient->getTags()->willReturn([
            [
                'name' => 'v3.0.0-RC',
                'zipball_url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/zipball/v3.0.0-RC',
                'tarball_url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/tarball/v3.0.0-RC',
                'commit' => [
                    'sha' => 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
                    'url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/commits/xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
                ],
            ],
            [
                'name' => 'v2.4.1',
                'zipball_url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/zipball/v2.4.1',
                'tarball_url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/tarball/v2.4.1',
                'commit' => [
                    'sha' => 'b4983586c8e7b1f99ec05dd1e75c8b673315da70',
                    'url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/commits/b4983586c8e7b1f99ec05dd1e75c8b673315da70',
                ],
            ],
            [
                'name' => 'v2.4.0',
                'zipball_url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/zipball/v2.4.0',
                'tarball_url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/tarball/v2.4.0',
                'commit' => [
                    'sha' => '63661f3add3609e90e4ab8115113e189ae547bb4',
                    'url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/commits/63661f3add3609e90e4ab8115113e189ae547bb4',
                ],
            ],
            [
                'name' => 'v2.3.3',
                'zipball_url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/zipball/v2.3.3',
                'tarball_url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/tarball/v2.3.3',
                'commit' => [
                    'sha' => 'cd1e6c47cd692c2deb8f160bb80b8feb3b265d29',
                    'url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/commits/cd1e6c47cd692c2deb8f160bb80b8feb3b265d29',
                ],
            ],
            [
                'name' => 'v2.3.2',
                'zipball_url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/zipball/v2.3.2',
                'tarball_url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/tarball/v2.3.2',
                'commit' => [
                    'sha' => '597745f744bcce1aed59dfd1bb4603de2a06cda9',
                    'url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/commits/597745f744bcce1aed59dfd1bb4603de2a06cda9',
                ],
            ],
            [
                'name' => 'v2.3.1',
                'zipball_url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/zipball/v2.3.1',
                'tarball_url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/tarball/v2.3.1',
                'commit' => [
                    'sha' => 'd5257f7433bb490299c4f300d95598fd911a8ab0',
                    'url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/commits/d5257f7433bb490299c4f300d95598fd911a8ab0',
                ],
            ],
            [
                'name' => 'v2.3.0',
                'zipball_url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/zipball/v2.3.0',
                'tarball_url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/tarball/v2.3.0',
                'commit' => [
                    'sha' => 'ab8c61329ddd896e287a84c7663d06cf1bed3907',
                    'url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/commits/ab8c61329ddd896e287a84c7663d06cf1bed3907',
                ],
            ],
            [
                'name' => 'v2.2.6',
                'zipball_url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/zipball/v2.2.6',
                'tarball_url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/tarball/v2.2.6',
                'commit' => [
                    'sha' => 'c1cc52c242f17c4d52d9601159631da488fac7a4',
                    'url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/commits/c1cc52c242f17c4d52d9601159631da488fac7a4',
                ],
            ],
            [
                'name' => 'v2.2.5',
                'zipball_url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/zipball/v2.2.5',
                'tarball_url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/tarball/v2.2.5',
                'commit' => [
                    'sha' => '27c2cd9d4abd2178b5b585fa2c3cca656d377c69',
                    'url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/commits/27c2cd9d4abd2178b5b585fa2c3cca656d377c69',
                ],
            ],
            [
                'name' => 'v2.2.4',
                'zipball_url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/zipball/v2.2.4',
                'tarball_url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/tarball/v2.2.4',
                'commit' => [
                    'sha' => '5191e01d0fa0f579eb709350306cd11ad6427ca6',
                    'url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/commits/5191e01d0fa0f579eb709350306cd11ad6427ca6',
                ],
            ],
            [
                'name' => 'v2.2.3',
                'zipball_url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/zipball/v2.2.3',
                'tarball_url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/tarball/v2.2.3',
                'commit' => [
                    'sha' => '8f33cf3da0da94b67b9cd696b2b9dda81c928f72',
                    'url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/commits/8f33cf3da0da94b67b9cd696b2b9dda81c928f72',
                ],
            ],
            [
                'name' => 'v2.2.2',
                'zipball_url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/zipball/v2.2.2',
                'tarball_url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/tarball/v2.2.2',
                'commit' => [
                    'sha' => '362d7bd3df3521966ae0fc82bb67c000c5f25059',
                    'url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/commits/362d7bd3df3521966ae0fc82bb67c000c5f25059',
                ],
            ],
            [
                'name' => 'v2.2.1',
                'zipball_url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/zipball/v2.2.1',
                'tarball_url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/tarball/v2.2.1',
                'commit' => [
                    'sha' => 'aff95e090fdaf57c20d32d7728b090f2015bfcef',
                    'url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/commits/aff95e090fdaf57c20d32d7728b090f2015bfcef',
                ],
            ],
            [
                'name' => 'v2.2.0',
                'zipball_url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/zipball/v2.2.0',
                'tarball_url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/tarball/v2.2.0',
                'commit' => [
                    'sha' => 'd6f17423412d33df6b69c9aaf12037b91703533b',
                    'url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/commits/d6f17423412d33df6b69c9aaf12037b91703533b',
                ],
            ],
            [
                'name' => 'v2.1.3',
                'zipball_url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/zipball/v2.1.3',
                'tarball_url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/tarball/v2.1.3',
                'commit' => [
                    'sha' => 'd30ca69f8bed931b5c630407f0a98306e33c2c39',
                    'url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/commits/d30ca69f8bed931b5c630407f0a98306e33c2c39',
                ],
            ],
            [
                'name' => 'v2.1.2',
                'zipball_url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/zipball/v2.1.2',
                'tarball_url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/tarball/v2.1.2',
                'commit' => [
                    'sha' => 'c7de769d7b44f2c9de68e1f678b65efd8126f60b',
                    'url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/commits/c7de769d7b44f2c9de68e1f678b65efd8126f60b',
                ],
            ],
            [
                'name' => 'v2.1.1',
                'zipball_url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/zipball/v2.1.1',
                'tarball_url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/tarball/v2.1.1',
                'commit' => [
                    'sha' => 'e0e33ce4eaf59ba77ead9ce45256692aa29ecb38',
                    'url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/commits/e0e33ce4eaf59ba77ead9ce45256692aa29ecb38',
                ],
            ],
            [
                'name' => 'v2.1.0',
                'zipball_url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/zipball/v2.1.0',
                'tarball_url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/tarball/v2.1.0',
                'commit' => [
                    'sha' => '2c69f4d424f85062fe40f7689797d6d32c76b711',
                    'url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/commits/2c69f4d424f85062fe40f7689797d6d32c76b711',
                ],
            ],
            [
                'name' => 'v2.0.1',
                'zipball_url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/zipball/v2.0.1',
                'tarball_url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/tarball/v2.0.1',
                'commit' => [
                    'sha' => '863ad254da1e44904c8bf8fbcc9f5624834fc71a',
                    'url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/commits/863ad254da1e44904c8bf8fbcc9f5624834fc71a',
                ],
            ],
            [
                'name' => 'v2.0.0',
                'zipball_url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/zipball/v2.0.0',
                'tarball_url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/tarball/v2.0.0',
                'commit' => [
                    'sha' => 'f3baf72eb2f58bf275b372540f5b47d25aed910f',
                    'url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/commits/f3baf72eb2f58bf275b372540f5b47d25aed910f',
                ],
            ],
            [
                'name' => 'v2.0.0-beta',
                'zipball_url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/zipball/v2.0.0-beta',
                'tarball_url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/tarball/v2.0.0-beta',
                'commit' => [
                    'sha' => '962b2c537063b670aca2d6f3fb839d2c103def38',
                    'url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/commits/962b2c537063b670aca2d6f3fb839d2c103def38',
                ],
            ],
            [
                'name' => 'v2.0.0-alpha',
                'zipball_url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/zipball/v2.0.0-alpha',
                'tarball_url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/tarball/v2.0.0-alpha',
                'commit' => [
                    'sha' => 'd0d76b434728fcf522270b67b454ed7e84e850ed',
                    'url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/commits/d0d76b434728fcf522270b67b454ed7e84e850ed',
                ],
            ],
            [
                'name' => 'v2.0.0-RC',
                'zipball_url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/zipball/v2.0.0-RC',
                'tarball_url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/tarball/v2.0.0-RC',
                'commit' => [
                    'sha' => 'f88ef17f44fa442e1dd98deb7da0d943be9c8fa8',
                    'url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/commits/f88ef17f44fa442e1dd98deb7da0d943be9c8fa8',
                ],
            ],
            [
                'name' => 'v1.14.0-beta',
                'zipball_url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/zipball/v1.14.0-beta',
                'tarball_url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/tarball/v1.14.0-beta',
                'commit' => [
                    'sha' => 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
                    'url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/commits/xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
                ],
            ],
            [
                'name' => 'v1.13.2',
                'zipball_url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/zipball/v1.13.2',
                'tarball_url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/tarball/v1.13.2',
                'commit' => [
                    'sha' => '106313aa0d501782260e48ac04a1c671b5d418ea',
                    'url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/commits/106313aa0d501782260e48ac04a1c671b5d418ea',
                ],
            ],
            [
                'name' => 'v1.13.1',
                'zipball_url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/zipball/v1.13.1',
                'tarball_url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/tarball/v1.13.1',
                'commit' => [
                    'sha' => '0ea4f7ed06ca55da1d8fc45da26ff87f261c4088',
                    'url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/commits/0ea4f7ed06ca55da1d8fc45da26ff87f261c4088',
                ],
            ],
            [
                'name' => 'v1.13.0',
                'zipball_url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/zipball/v1.13.0',
                'tarball_url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/tarball/v1.13.0',
                'commit' => [
                    'sha' => 'ac04a510bed5407e91664f8a37b9d58072d96768',
                    'url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/commits/ac04a510bed5407e91664f8a37b9d58072d96768',
                ],
            ],
            [
                'name' => 'v1.12.4',
                'zipball_url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/zipball/v1.12.4',
                'tarball_url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/tarball/v1.12.4',
                'commit' => [
                    'sha' => 'c5a9d66dd27f02a3ffba4ec451ce27702604cdc8',
                    'url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/commits/c5a9d66dd27f02a3ffba4ec451ce27702604cdc8',
                ],
            ],
            [
                'name' => 'v1.12.3',
                'zipball_url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/zipball/v1.12.3',
                'tarball_url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/tarball/v1.12.3',
                'commit' => [
                    'sha' => '78a820c16d13f593303511461eefa939502fb2de',
                    'url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/commits/78a820c16d13f593303511461eefa939502fb2de',
                ],
            ],
            [
                'name' => 'v1.12.2',
                'zipball_url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/zipball/v1.12.2',
                'tarball_url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/tarball/v1.12.2',
                'commit' => [
                    'sha' => 'baa7112bef3b86c65fcfaae9a7a50436e3902b41',
                    'url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/commits/baa7112bef3b86c65fcfaae9a7a50436e3902b41',
                ],
            ],
            [
                'name' => 'v1.12.1',
                'zipball_url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/zipball/v1.12.1',
                'tarball_url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/tarball/v1.12.1',
                'commit' => [
                    'sha' => 'd33ee60f3d3e6152888b7f3a385f49e5c43bf1bf',
                    'url' => 'https://api.github.com/repos/FriendsOfPHP/PHP-CS-Fixer/commits/d33ee60f3d3e6152888b7f3a385f49e5c43bf1bf',
                ],
            ],
        ]);

        return $githubClient->reveal();
    }
}
