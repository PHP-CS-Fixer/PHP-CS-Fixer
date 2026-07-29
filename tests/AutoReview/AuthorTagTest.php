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

use PhpCsFixer\Finder;
use PhpCsFixer\Preg;
use PhpCsFixer\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Group;

/**
 * @internal
 *
 * @coversNothing
 *
 * @group auto-review
 * @group covers-nothing
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
#[CoversNothing]
#[Group('auto-review')]
#[Group('covers-nothing')]
final class AuthorTagTest extends TestCase
{
    public function testAuthorNameIsConsistentForGivenEmail(): void
    {
        $finder = (new Finder())
            ->in(__DIR__.'/../..')
            ->append([__DIR__.'/../../php-cs-fixer'])
            ->exclude(['dev-tools/phpstan', 'tests/Fixtures'])
            ->ignoreDotFiles(false)
        ;

        /** @var array<string, array<string, int>> $nameCountsByEmail */
        $nameCountsByEmail = [];

        foreach ($finder as $file) {
            $content = file_get_contents($file->getPathname());
            \assert(false !== $content);

            $tokens = token_get_all($content);

            foreach ($tokens as $token) {
                if (!\is_array($token) || \T_DOC_COMMENT !== $token[0]) {
                    continue;
                }

                if (Preg::matchAll('/@author\s+(.+?)\s*<([^>]+)>/', $token[1], $matches, \PREG_SET_ORDER) < 1) {
                    continue;
                }

                foreach ($matches as $match) {
                    $nameCountsByEmail[$match[2]][$match[1]] = ($nameCountsByEmail[$match[2]][$match[1]] ?? 0) + 1;
                }
            }
        }

        $errors = [];
        foreach ($nameCountsByEmail as $email => $nameCounts) {
            if (\count($nameCounts) < 2) {
                continue;
            }

            arsort($nameCounts);

            $names = [];
            foreach ($nameCounts as $name => $count) {
                $names[] = \sprintf('"%s" (%d)', $name, $count);
            }

            $errors[] = \sprintf('<%s>: %s', $email, implode(', ', $names));
        }

        self::assertSame(
            [],
            $errors,
            "Inconsistent @author names for the same email:\n - ".implode("\n - ", $errors),
        );
    }
}
