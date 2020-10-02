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

namespace PhpCsFixer\Tests\Fixtures;

use PhpCsFixer\Differ\DifferInterface;

/**
 * @internal
 */
final class FakeDiffer implements DifferInterface
{
    /**
     * @var \SplFileInfo|null
     */
    public $passedFile;

    public function diff($old, $new, \SplFileInfo $file = null)
    {
        $this->passedFile = $file;

        return 'some-diff';
    }
}
