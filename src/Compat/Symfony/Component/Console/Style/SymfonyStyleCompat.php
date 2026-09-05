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

namespace PhpCsFixer\Compat\Symfony\Component\Console\Style;

use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * This file is a polyfill for SymfonyStyle @ Symfony 8.2 provide methods `outline*()` that are not available in older Symfony.
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class SymfonyStyleCompat extends SymfonyStyle
{
    public function outlineSuccess($message): void
    {
        $this->success($message);
    }

    public function outlineError($message): void
    {
        $this->error($message);
    }

    public function outlineWarning($message): void
    {
        $this->warning($message);
    }

    public function outlineNote($message): void
    {
        $this->note($message);
    }

    public function outlineInfo($message): void
    {
        $this->info($message);
    }

    public function outlineCaution($message): void
    {
        $this->caution($message);
    }
}
