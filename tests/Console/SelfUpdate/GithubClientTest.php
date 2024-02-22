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
        $path = tempnam(sys_get_temp_dir(), 'tmp_url');
        file_put_contents($path, json_encode(['tag1', 'tag2']));

        $githubClient = new GithubClient();

        try {
            \Closure::bind(static function (GithubClient $githubClient) use ($path): void {
                $githubClient->url = $path;
            }, null, $githubClient)($githubClient);

            self::assertSame(['tag1', 'tag2'], $githubClient->getTags());
        } finally {
            unlink($path);
        }
    }
}
