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

namespace PhpCsFixer\Config;

use PhpCsFixer\ConfigInterface;

/**
 * EXPERIMENTAL: This feature is experimental and does not fall under the backward compatibility promise.
 *
 * @TODO 4.0 Include support for this in main ConfigInterface
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
interface FixerAnnotationAwareConfigInterface extends ConfigInterface
{
    /**
     * @param FixerAnnotationMode::ALL|FixerAnnotationMode::FORBIDDEN|FixerAnnotationMode::MATCHING $fixerAnnotationMode
     *
     * @return $this
     */
    public function setFixerAnnotationMode(string $fixerAnnotationMode): ConfigInterface;

    /**
     * @return FixerAnnotationMode::ALL|FixerAnnotationMode::FORBIDDEN|FixerAnnotationMode::MATCHING
     */
    public function getFixerAnnotationMode(): string;
}
