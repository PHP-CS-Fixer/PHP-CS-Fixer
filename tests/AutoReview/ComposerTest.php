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

namespace PhpCsFixer\tests\AutoReview;

use PhpCsFixer\Tests\TestCase;
use PhpCsFixer\Tokenizer\TransformerInterface;
use PhpCsFixer\Tokenizer\Transformers;
use PhpCsFixer\Console\Application;
use Composer\Semver\VersionParser;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @coversNothing
 * @group auto-review
 */
final class ComposerTest extends TestCase
{
    public function testBranchAlias()
    {
        $composerJson = json_decode(file_get_contents(__DIR__.'/../../composer.json'), true);

        if (!isset($composerJson['extra']['branch-alias'])) {
            $this->assertTrue(true); // composer.json doesn't contain branch alias, all good!
            return;
        }

        $aliases = $composerJson['extra']['branch-alias'];
        $this->assertInternalType('array', $aliases);
        $this->assertCount(1, $aliases, 'Only one branch alias is allowed per branch.');
        $this->assertTrue(isset($aliases['dev-master']), 'The only branch that could be aliased is "dev-master".');

        $this->assertSame(
            $this->convertAppVersionToAliasedVersion(Application::VERSION),
            $aliases['dev-master'],
            'Version from branch alias must match application version.'
        );
    }

    /**
     * @param string $version
     * @return strting
     */
    private function convertAppVersionToAliasedVersion($version) {
        $parts = explode('.', $version, 3);
        return sprintf('%d.%d-dev', $parts[0], $parts[1]);
    }
}
