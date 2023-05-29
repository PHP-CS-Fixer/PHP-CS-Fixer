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
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\FixerDefinition\VersionSpecification;
use PhpCsFixer\FixerDefinition\VersionSpecificCodeSample;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

final class NativeConstTypeDeclarationCasingFixer extends AbstractFixer
{
    /**
     * https://wiki.php.net/rfc/typed_class_constants
     * Supported types
     * Class constant type declarations support all type declarations supported by PHP, with the exception of void, callable, never.
     *
     * array
     * bool
     * float
     * int
     * iterable
     * object
     * parent
     * self
     * string
     * any class or interface name -> not native, so not applicable for this Fixer
     * ?type -> not native, `?` has no casing, so not applicable for this Fixer
     *
     * Not in the list referenced but supported:
     * mixed
     * null
     *
     * @var array<string, true>
     */
    private array $hints;

    public function __construct()
    {
        parent::__construct();

        $this->hints = [
            'array' => true,
            'bool' => true,
            'float' => true,
            'int' => true,
            'iterable' => true,
            'mixed' => true,
            'null' => true,
            'object' => true,
            'parent' => true,
            'self' => true,
            'string' => true,
        ];
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Native type hints for constants should use the correct case.',
            [
                new VersionSpecificCodeSample(
                    "<?php\nclass Foo\n{\n    const INT BAR = 1;\n}\n",
                    new VersionSpecification(8_03_00)
                ),
            ]
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return \PHP_VERSION_ID >= 8_03_00
            && $tokens->isTokenKindFound(T_CONST)
            && $tokens->isAnyTokenKindsFound(Token::getClassyTokenKinds());
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        foreach ($tokens as $index => $token) {
            if ($token->isGivenKind(T_CONST)) {
                $this->fixConstant($tokens, $index);
            }
        }
    }

    private function fixConstant(Tokens $tokens, int $index): void
    {
        $candidateTypeIndex = $tokens->getNextMeaningfulToken($index);
        $candidateNameIndex = $tokens->getNextMeaningfulToken($candidateTypeIndex);

        if (!$tokens[$candidateNameIndex]->isGivenKind(T_STRING)) {
            return; // const without `type`
        }

        $typeContent = $tokens[$candidateTypeIndex]->getContent();
        $typeContentLower = strtolower($typeContent);

        if ($typeContent === $typeContentLower) {
            return; // already lower case or not a native type
        }

        if ('?' === $typeContentLower[0]) { // handle `nullable`
            $typeContentLowerWithoutNullable = substr($typeContentLower, 1);
        } else {
            $typeContentLowerWithoutNullable = $typeContentLower;
        }

        if (!isset($this->hints[$typeContentLowerWithoutNullable])) {
            return; // not a native type to fix
        }

        $tokens[$candidateTypeIndex] = new Token(
            [
                $tokens[$candidateTypeIndex]->getId(),
                $typeContentLower,
            ]
        );
    }
}
