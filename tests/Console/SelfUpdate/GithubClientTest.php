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

use PhpCsFixer\Console\SelfUpdate\GithubClient;
use PhpCsFixer\Tests\TestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Console\SelfUpdate\GithubClient
 */
final class GithubClientTest extends TestCase
{
    public function testGettingTags(): void
    {
        $githubClient = new GithubClient();

        \Closure::bind(static function (GithubClient $githubClient): void {
            $githubClient->url = __DIR__.'/../../Fixtures/api_github_com_tags.json';
        }, null, $githubClient)($githubClient);

        self::assertSame(
            [
                [
                    'name' => 'v3.48.0',
                    'zipball_url' => 'https://api.github.com/repos/PHP-CS-Fixer/PHP-CS-Fixer/zipball/refs/tags/v3.48.0',
                    'tarball_url' => 'https://api.github.com/repos/PHP-CS-Fixer/PHP-CS-Fixer/tarball/refs/tags/v3.48.0',
                    'commit' => [
                        'sha' => 'a92472c6fb66349de25211f31c77eceae3df024e',
                        'url' => 'https://api.github.com/repos/PHP-CS-Fixer/PHP-CS-Fixer/commits/a92472c6fb66349de25211f31c77eceae3df024e',
                    ],
                    'node_id' => 'MDM6UmVmNDM0NDI1NzpyZWZzL3RhZ3MvdjMuNDguMA==',
                ],
                [
                    'name' => 'v3.47.1',
                    'zipball_url' => 'https://api.github.com/repos/PHP-CS-Fixer/PHP-CS-Fixer/zipball/refs/tags/v3.47.1',
                    'tarball_url' => 'https://api.github.com/repos/PHP-CS-Fixer/PHP-CS-Fixer/tarball/refs/tags/v3.47.1',
                    'commit' => [
                        'sha' => '173c60d1eff911c9c54322704623a45561d3241d',
                        'url' => 'https://api.github.com/repos/PHP-CS-Fixer/PHP-CS-Fixer/commits/173c60d1eff911c9c54322704623a45561d3241d',
                    ],
                    'node_id' => 'MDM6UmVmNDM0NDI1NzpyZWZzL3RhZ3MvdjMuNDcuMQ==',
                ],
                [
                    'name' => 'v3.47.0',
                    'zipball_url' => 'https://api.github.com/repos/PHP-CS-Fixer/PHP-CS-Fixer/zipball/refs/tags/v3.47.0',
                    'tarball_url' => 'https://api.github.com/repos/PHP-CS-Fixer/PHP-CS-Fixer/tarball/refs/tags/v3.47.0',
                    'commit' => [
                        'sha' => '184dd992fe49169a18300dba4435212db55220f7',
                        'url' => 'https://api.github.com/repos/PHP-CS-Fixer/PHP-CS-Fixer/commits/184dd992fe49169a18300dba4435212db55220f7',
                    ],
                    'node_id' => 'MDM6UmVmNDM0NDI1NzpyZWZzL3RhZ3MvdjMuNDcuMA==',
                ],
            ],
            $githubClient->getTags()
        );
    }
}
