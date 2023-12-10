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

namespace PhpCsFixer\Fixer;

use PhpCsFixer\WhitespacesFixerConfig;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
interface WhitespacesAwareFixerInterface extends FixerInterface
{
    public function setWhitespacesConfig(WhitespacesFixerConfig $config): void;
}
