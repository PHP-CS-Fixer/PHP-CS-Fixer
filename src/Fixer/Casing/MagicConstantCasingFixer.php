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

namespace PhpCsFixer\Fixer\Casing;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author ntzm
 */
final class MagicConstantCasingFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        $magicConstants = $this->getMagicConstants();

        for ($index = 0, $count = $tokens->count(); $index < $count; ++$index) {
            if (!$tokens[$index]->isGivenKind(array_keys($magicConstants))) {
                continue;
            }

            if (!array_key_exists($tokens[$index]->getId(), $magicConstants)) {
                continue;
            }

            $tokens[$index]->setContent($magicConstants[$tokens[$index]->getId()]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Magic constants should be referred to using the correct casing.',
            array(new CodeSample("<?php\necho __dir__;"))
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isAnyTokenKindsFound(array_keys($this->getMagicConstants()));
    }

    /**
     * @return array<int, string>
     */
    private function getMagicConstants()
    {
        $magicConstants = array(
            T_LINE => '__LINE__',
            T_FILE => '__FILE__',
            T_DIR => '__DIR__',
            T_FUNC_C => '__FUNCTION__',
            T_CLASS_C => '__CLASS__',
            T_METHOD_C => '__METHOD__',
            T_NS_C => '__NAMESPACE__',
        );

        if (PHP_VERSION_ID >= 50400) {
            $magicConstants[T_TRAIT_C] = '__TRAIT__';
        }

        return $magicConstants;
    }
}
