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

namespace PhpCsFixer\Fixer\Naming;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Victor Melnik <melnikvictorl@gmail.com>
 */
final class SnakeCaseToCamelCaseFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Convert `$snake_case` variables to `$camelCase`.',
            array(
                new CodeSample(
                    "<?php\n\$snake_case = '';"
                ),
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(T_VARIABLE);
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(T_VARIABLE)) {
                continue;
            }

            if (preg_match('/_/', $token->getContent())) {
                $token->setContent('$'.$this->camelize(substr($token->getContent(), 1)));
            }
        }
    }

    /**
     * @param string $name variable name to camelize
     *
     * @return string
     */
    private function camelize($name)
    {
        return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $name))));
    }
}
