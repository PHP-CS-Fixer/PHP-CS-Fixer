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

namespace PhpCsFixer\Fixer\ReturnNotation;

use PhpCsFixer\AbstractProxyFixer;
use PhpCsFixer\Fixer\Whitespace\BlankLineBeforeStatementFixer;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\WhitespacesFixerConfig;

/**
 * @deprecated since 2.4, replaced by BlankLineBeforeStatementFixer
 *
 * @todo To be removed at 3.0
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author Andreas Möller <am@localheinz.com>
 */
final class BlankLineBeforeReturnFixer extends AbstractProxyFixer implements WhitespacesAwareFixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'An empty line feed should precede a return statement (deprecated, use `blank_line_before_statement` instead).',
            [new CodeSample("<?php\nfunction A()\n{\n    echo 1;\n    return 1;\n}")]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function setWhitespacesConfig(WhitespacesFixerConfig $config)
    {
        $this->proxyFixer->setWhitespacesConfig($config);
    }

    /**
     * {@inheritdoc}
     */
    protected function createProxyFixer()
    {
        $fixer = new BlankLineBeforeStatementFixer();
        $fixer->configure(['statements' => ['return']]);

        return $fixer;
    }
}
