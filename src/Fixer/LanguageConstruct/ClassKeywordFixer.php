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

namespace PhpCsFixer\Fixer\LanguageConstruct;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Indicator\ClassyExistanceIndicator;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class ClassKeywordFixer extends AbstractFixer
{
    /**
     * @var string[]
     */
    private $imports = [];

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'EXPERIMENTAL: Converts FQCN strings to `*::class` keywords.',
            [
                new CodeSample(
                    '<?php

$foo = \'PhpCsFixer\Tokenizer\Tokens\';
$bar = "\PhpCsFixer\Tokenizer\Tokens";
'
                ),
            ]
        );
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return true;
    }

    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        $indicator = new ClassyExistanceIndicator();

        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            $token = $tokens[$index];

            if ($token->isGivenKind(T_CONSTANT_ENCAPSED_STRING)) {
                $name = substr($token->getContent(), 1, -1);
                $name = ltrim($name, '\\');
                $name = str_replace('\\\\', '\\', $name);

                if ($indicator->exists($name)) {
                    try {
                        $substitution = Tokens::fromCode("<?php echo \\{$name}::class;");
                        $substitution->clearRange(0, 2);
                        $substitution->clearAt($substitution->getSize() - 1);
                        $substitution->clearEmptyTokens();

                        $tokens->clearAt($index);
                        $tokens->insertAt($index, $substitution);
                    } catch (\Error $e) {
                        var_dump('error with parsing class', $name);
                        var_dump($e->getMessage());
                    }
                }
            }
        }
    }
}
