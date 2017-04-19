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

namespace PhpCsFixer\Fixer\LanguageConstruct;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\VersionSpecification;
use PhpCsFixer\FixerDefinition\VersionSpecificCodeSample;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;
use PhpCsFixer\Indicator\ClassyExistanceIndicator;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class ClassKeywordFixer extends AbstractFixer
{
    /**
     * @var string[]
     */
    private $imports = array();

    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Converts FQCN strings to `*::class` keywords. Requires PHP >= 5.5.',
            array(
                new VersionSpecificCodeSample(
                    '<?php

use Foo\Bar\Baz;

$className = Baz::class;
',
                    new VersionSpecification(50500)
                ),
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return PHP_VERSION_ID >= 50500;
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
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
                        $substitution = Tokens::fromCode("<?php echo \\$name::class;");
                        $substitution->clearRange(0, 2);
                        $substitution[$substitution->getSize() - 1]->clear();
                        $substitution->clearEmptyTokens();

                        $token->clear();
                        $tokens->insertAt($index, $substitution);
                    } catch (\Error $e) {
                        var_dump("error with parsing class", $name);
                        var_dump($e->getMessage());
                    }
                }
            }
        }
    }
}
