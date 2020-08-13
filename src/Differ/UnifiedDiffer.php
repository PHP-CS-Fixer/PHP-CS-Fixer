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

namespace PhpCsFixer\Differ;

use PhpCsFixer\Diff\Differ;
use PhpCsFixer\Diff\Output\StrictUnifiedDiffOutputBuilder;

/**
 * @author SpacePossum
 */
final class UnifiedDiffer implements DifferInterface
{
    /**
     * {@inheritdoc}
     */
    public function diff($old, $new, \SplFileInfo $file = null)
    {
        $options = [
            'fromFile' => 'Original',
            'toFile' => 'New',
        ];

        if ($file) {
            $options = [
                'fromFile' => str_replace(getcwd(), '', $file->getPath()),
                'toFile' => str_replace(getcwd(), '', $file->getPath()),
                'fromFileDate' => date('Y-m-d H:i:s.u Z', $file->getMTime()),
                'toFileDate' => date('Y-m-d H:i:s.u Z', $file->getMTime()),
            ];
        }

        $differ = new Differ(new StrictUnifiedDiffOutputBuilder($options));

        return $differ->diff($old, $new);
    }
}
