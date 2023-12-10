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

namespace PhpCsFixer;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class WordMatcher
{
    /**
     * @var string[]
     */
    private array $candidates;

    /**
     * @param string[] $candidates
     */
    public function __construct(array $candidates)
    {
        $this->candidates = $candidates;
    }

    public function match(string $needle): ?string
    {
        $word = null;
        $distance = ceil(\strlen($needle) * 0.35);

        foreach ($this->candidates as $candidate) {
            $candidateDistance = levenshtein($needle, $candidate);

            if ($candidateDistance < $distance) {
                $word = $candidate;
                $distance = $candidateDistance;
            }
        }

        return $word;
    }
}
