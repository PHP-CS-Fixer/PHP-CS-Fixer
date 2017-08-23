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
use PhpCsFixer\Diff\Output\UnifiedDiffOutputBuilder;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class SebastianBergmannDiffer implements DifferInterface
{
    /**
     * @var Differ
     */
    private $differ;

    public function __construct()
    {
        $this->differ = new Differ(new UnifiedDiffOutputBuilder("--- Original\n+++ New", true));
    }

    /**
     * {@inheritdoc}
     */
    public function diff($old, $new)
    {
        return $this->differ->diff($old, $new);
    }
}
