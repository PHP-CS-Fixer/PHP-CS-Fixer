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

namespace PhpCsFixer\Fixer\Casing;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\FCT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 *
 * @author ntzm
 */
final class MagicConstantCasingFixer extends AbstractFixer
{
    private const MAGIC_CONSTANTS = [
        \T_LINE => '__LINE__',
        \T_FILE => '__FILE__',
        \T_DIR => '__DIR__',
        \T_FUNC_C => '__FUNCTION__',
        \T_CLASS_C => '__CLASS__',
        \T_METHOD_C => '__METHOD__',
        \T_NS_C => '__NAMESPACE__',
        CT::T_CLASS_CONSTANT => 'class',
        \T_TRAIT_C => '__TRAIT__',
        FCT::T_PROPERTY_C => '__PROPERTY__',
    ];

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Magic constants should be referred to using the correct casing.',
            [new CodeSample("<?php\necho __dir__;\n")]
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAnyTokenKindsFound($this->getMagicConstantTokens());
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        $magicConstantTokens = $this->getMagicConstantTokens();

        foreach ($tokens as $index => $token) {
            if ($token->isGivenKind($magicConstantTokens)) {
                $tokens[$index] = new Token([$token->getId(), self::MAGIC_CONSTANTS[$token->getId()]]);
            }
        }
    }

    /**
     * @return non-empty-list<int>
     */
    private function getMagicConstantTokens(): array
    {
        static $magicConstantTokens = null;

        if (null === $magicConstantTokens) {
            $magicConstantTokens = array_keys(self::MAGIC_CONSTANTS);
        }

        return $magicConstantTokens;
    }
}
