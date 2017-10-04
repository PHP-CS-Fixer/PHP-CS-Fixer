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

namespace PhpCsFixer\Fixer\Operator;

use PhpCsFixer\AbstractProxyFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;

/**
 * @author Gregor Harlan <gharlan@web.de>
 *
 * @deprecated in 2.8, proxy to IncrementStyleFixer
 */
final class PreIncrementFixer extends AbstractProxyFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            sprintf('Pre incrementation/decrementation should be used if possible. DEPRECATED: Use "%s" instead.', $this->proxyFixer->getName()),
            [new CodeSample("<?php\n\$a++;\n\$b--;")]
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function createProxyFixer()
    {
        $fixer = new IncrementStyleFixer();
        $fixer->configure(['style' => 'pre']);

        return $fixer;
    }
}
