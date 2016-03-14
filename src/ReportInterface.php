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
 * @author Boris Gorbylev <ekho@ekho.name>
 */
interface ReportInterface
{
    /**
     * @return string
     */
    public function getFormat();

    /**
     * @param array $changed
     */
    public function setChanged(array $changed);

    /**
     * Process changed files array. Returns generated report.
     *
     * @return string
     */
    public function generate();
}
