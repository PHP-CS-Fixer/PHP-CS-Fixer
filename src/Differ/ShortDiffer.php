<?php

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Phyks (Lucas Verney) <phyks@phyks.me>
 *
 * Based on SebastianBergmann Differ class.
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Differ;

use SebastianBergmann\Diff\Differ;

/**
 * @author Phyks (Lucas Verney) <phyks@phyks.me>
 */
final class ShortDiffer implements DifferInterface
{
    /**
     * @var Differ
     */
    private $differ;

    public function __construct()
    {
        $this->differ = new Differ();
    }

    /**
     * {@inheritdoc}
     */
    public function diff($old, $new)
    {
        $full_diff = $this->differ->diff($old, $new);
        $output_diff = array();
        foreach (explode("\n", $full_diff) as $line) {
            if (startswith($line, '+') || startswith($line, '-')) {
                $output_diff[] = $line;
            } else {
                if (end($output_diff) != '...') {
                    $output_diff[] = '...';
                }
            }
        }

        return implode("\n", $output_diff);
    }

    /**
     * Private method to check if a string starts with another one.
     */
    private function _startswith($haystack, $needle)
    {
        // search backwards starting from haystack length characters from the end
        return $needle === '' || strrpos($haystack, $needle, -strlen($haystack)) !== false;
    }
}
