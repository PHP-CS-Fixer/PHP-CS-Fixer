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

use PhpCsFixer\Preg;
use PhpCsFixer\Tests\TestCase;
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
 */
final class PRTypeLabelsTest extends TestCase
{
    public function testTemplateTypesCoverPRLintTypes(): void
    {
        $templateTypes = $this->getTypesFromReleaseNotesTemplate();
        $prLintTypes = $this->getTypesFromPRLint();

        $expectedTypes = [
            ...$prLintTypes,
            '*',
            'BC-break',
            'dependencies', // @TODO @MARKER-7305 remove me
            'revert',
        ];

        sort($expectedTypes);
        sort($templateTypes);

        self::assertSame($expectedTypes, $templateTypes);
    }

    /**
     * @return string[]
     */
    private function getTypesFromReleaseNotesTemplate(): array
    {
        $template = Yaml::parse(file_get_contents(__DIR__.'/../../.github/release.yml'));

        $types = array_merge(
            ...array_map(
                static fn (array $item): array => $item['labels'],
                $template['changelog']['categories']
            )
        );
        $types = array_map(
            static fn (string $item): string => str_replace('type/', '', $item),
            $types
        );

        sort($types);

        return $types;
    }

    /**
     * @return string[]
     */
    private function getTypesFromPRLint(): array
    {
        $prlint = json_decode(file_get_contents(__DIR__.'/../../.github/prlint.json'), true, 512, JSON_THROW_ON_ERROR);
        $typesPattern = $prlint['title'][0]['pattern'];

        Preg::match('/(?<=\()[^)]+(?=\))/', $typesPattern, $matches);
        $types = explode('|', $matches[0]);

        sort($types);

        return $types;
    }
}
