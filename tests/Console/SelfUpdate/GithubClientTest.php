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
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class GithubClientTest extends TestCase
{
    public function testGettingTags(): void
    {
        $githubClient = new GithubClient(__DIR__.'/../../Fixtures/api_github_com_tags.json');

        self::assertSame(
            [
                'v3.48.0',
                'v3.47.1',
                'v3.47.0',
            ],
            $githubClient->getTags()
        );
    }
}
