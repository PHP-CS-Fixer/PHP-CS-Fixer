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

namespace PhpCsFixer\Fixer\Alias;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Sullivan Senechal <soullivaneuh@gmail.com>
 * @author SpacePossum
 */
final class NoMixedEchoPrintFixer extends AbstractFixer implements ConfigurableFixerInterface
{
    /**
     * @var string
     */
    private $callBack;

    /**
     * @var int T_ECHO or T_PRINT
     */
    private $candidateTokenType;

    /**
     * {@inheritdoc}
     */
    public function configure(array $configuration): void
    {
        parent::configure($configuration);

        if ('echo' === $this->configuration['use']) {
            $this->candidateTokenType = T_PRINT;
            $this->callBack = 'fixPrintToEcho';
        } else {
            $this->candidateTokenType = T_ECHO;
            $this->callBack = 'fixEchoToPrint';
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Either language construct `print` or `echo` should be used.',
            [
                new CodeSample("<?php print 'example';\n"),
                new CodeSample("<?php echo('example');\n", ['use' => 'print']),
            ]
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run after EchoTagSyntaxFixer.
     */
    public function getPriority(): int
    {
        return -10;
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound($this->candidateTokenType);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        $callBack = $this->callBack;
        foreach ($tokens as $index => $token) {
            if ($token->isGivenKind($this->candidateTokenType)) {
                $this->{$callBack}($tokens, $index);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('use', 'The desired language construct.'))
                ->setAllowedValues(['print', 'echo'])
                ->setDefault('echo')
                ->getOption(),
        ]);
    }

    private function fixEchoToPrint(Tokens $tokens, int $index): void
    {
        $nextTokenIndex = $tokens->getNextMeaningfulToken($index);
        $endTokenIndex = $tokens->getNextTokenOfKind($index, [';', [T_CLOSE_TAG]]);
        $canBeConverted = true;

        for ($i = $nextTokenIndex; $i < $endTokenIndex; ++$i) {
            if ($tokens[$i]->equalsAny(['(', [CT::T_ARRAY_SQUARE_BRACE_OPEN]])) {
                $blockType = Tokens::detectBlockType($tokens[$i]);
                $i = $tokens->findBlockEnd($blockType['type'], $i);
            }

            if ($tokens[$i]->equals(',')) {
                $canBeConverted = false;

                break;
            }
        }

        if (false === $canBeConverted) {
            return;
        }

        $tokens[$index] = new Token([T_PRINT, 'print']);
    }

    private function fixPrintToEcho(Tokens $tokens, int $index): void
    {
        $prevToken = $tokens[$tokens->getPrevMeaningfulToken($index)];

        if (!$prevToken->equalsAny([';', '{', '}', ')', [T_OPEN_TAG], [T_ELSE]])) {
            return;
        }

        $tokens[$index] = new Token([T_ECHO, 'echo']);
    }
}
