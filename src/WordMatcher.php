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

namespace PhpCsFixer;

/**
 * Base class for function reference fixers.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author SpacePossum
 *
 * @internal
 */
final class WordMatcher
{
    /**
     * @var string[]
     */
    private $dict;

    public function __construct(array $dict)
    {
        $this->dict = $dict;
    }

    /**
     * @param string $needle
     *
     * @return null|string
     */
    public function match($needle)
    {
        $word = null;
        $distance = 7;

        foreach ($this->dict as $candidate) {
            $candidateDistance = levenshtein($needle, $candidate);

            if ($candidateDistance < $distance) {
                $word = $candidate;
                $distance = $candidateDistance;
            }
        }

        return $word;
    }
}
