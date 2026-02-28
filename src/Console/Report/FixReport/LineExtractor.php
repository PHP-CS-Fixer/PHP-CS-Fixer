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

namespace PhpCsFixer\Console\Report\FixReport;

use SebastianBergmann\Diff\Chunk;
use SebastianBergmann\Diff\Diff;
use SebastianBergmann\Diff\Line;

/**
 * Extracts line ranges from diffs.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class LineExtractor
{
    /**
     * @param list<Diff> $diffs
     *
     * @return array{begin: int, end: int}
     */
    public static function getLines(array $diffs): array
    {
        if (isset($diffs[0])) {
            $firstDiff = $diffs[0];

            $firstChunk = \Closure::bind(static fn (Diff $diff) => array_shift($diff->chunks), null, $firstDiff)($firstDiff);

            if ($firstChunk instanceof Chunk) {
                return self::getBeginEndForDiffChunk($firstChunk);
            }
        }

        return ['begin' => 0, 'end' => 0];
    }

    /**
     * @return array{begin: int, end: int}
     */
    private static function getBeginEndForDiffChunk(Chunk $chunk): array
    {
        $start = \Closure::bind(static fn (Chunk $chunk): int => $chunk->start, null, $chunk)($chunk);
        $startRange = \Closure::bind(static fn (Chunk $chunk): int => $chunk->startRange, null, $chunk)($chunk);
        $lines = \Closure::bind(static fn (Chunk $chunk): array => $chunk->lines, null, $chunk)($chunk);

        \assert(\count($lines) > 0);

        $firstModifiedLineOffset = array_find_key($lines, static function (Line $line): bool {
            $type = \Closure::bind(static fn (Line $line): int => $line->type, null, $line)($line);

            return Line::UNCHANGED !== $type;
        });
        \assert(\is_int($firstModifiedLineOffset));

        return [
            // offset the start by where the first line is actually modified
            'begin' => $start + $firstModifiedLineOffset,
            // it's not where last modification takes place, only where diff (with --context) ends
            'end' => $start + $startRange,
        ];
    }
}
