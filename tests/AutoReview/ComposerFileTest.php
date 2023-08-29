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

use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 *
 * @group covers-nothing
 * @group auto-review
 */
final class ComposerFileTest extends TestCase
{
    public function testScriptsHaveDescriptions(): void
    {
        $composerJson = self::readComposerJson();

        $scripts = array_keys($composerJson['scripts']);
        $descriptions = array_keys($composerJson['scripts-descriptions']);

        self::assertSame([], array_diff($scripts, $descriptions), 'There should be no scripts with missing description.');
        self::assertSame([], array_diff($descriptions, $scripts), 'There should be no superfluous description for not defined scripts.');
    }

    public function testScriptsAliasesDescriptionsFollowThePattern(): void
    {
        $composerJson = self::readComposerJson();

        $scripts = array_keys($composerJson['scripts']);

        $aliases = array_reduce($scripts, static function (array $carry, string $script) use ($composerJson): array {
            $code = $composerJson['scripts'][$script];

            if (\is_string($code) && '@' === $code[0]) {
                $potentialAlias = substr($code, 1);
                if (isset($composerJson['scripts'][$potentialAlias])) {
                    $carry[$script] = $potentialAlias;
                }
            }

            return $carry;
        }, []);

        foreach ($aliases as $code => $alias) {
            self::assertSame(
                "Alias for '{$alias}'",
                $composerJson['scripts-descriptions'][$code],
                "Script description for '{$code}' alias should be following the pattern.",
            );
        }
    }

    public function testDoctrineSuggestionsContainProperVersionConstraints(): void
    {
        $composerJson = self::readComposerJson();
        $doctrinePackages = ['doctrine/annotations', 'doctrine/lexer'];

        foreach ($doctrinePackages as $package) {
            $suggestion = $composerJson['suggest'][$package];
            $constraint = $composerJson['require-dev'][$package];

            self::assertSame(
                "Required ({$constraint}) if you want to use @DoctrineAnnotation rule set (or single fixers related to Doctrine Annotations)",
                $suggestion
            );
        }
    }

    /**
     * @return array<string, mixed>
     */
    private static function readComposerJson(): array
    {
        $composerJsonContent = file_get_contents(__DIR__.'/../../composer.json');

        return json_decode($composerJsonContent, true, 512, JSON_THROW_ON_ERROR);
    }
}
