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

use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;

/**
 * @author Laurent Laville
 */
interface FixerReportInterface
{
    /**
     * - "helpUri" identify the absolute URI [RFC3986](http://www.rfc-editor.org/info/rfc3986)
     * *             of the primary documentation for the fixer.
     *
     * @return array{"helpUri"?: string, "definition"?: FixerDefinitionInterface, "risky"?: bool}
     */
    public function getExtraInfoFixers(): array;
}
