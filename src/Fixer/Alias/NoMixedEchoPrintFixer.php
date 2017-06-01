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

namespace PhpCsFixer\Fixer\Alias;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Sullivan Senechal <soullivaneuh@gmail.com>
 * @author SpacePossum
 */
final class NoMixedEchoPrintFixer extends AbstractFixer implements ConfigurationDefinitionFixerInterface
{
    /**
     * @deprecated will be removed in 3.0
     */
    public static $defaultConfig = array('use' => 'echo');

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
    public function configure(array $configuration = null)
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
    public function getDefinition()
    {
        return new FixerDefinition(
            'Either language construct `print` or `echo` should be used.',
            array(
                new CodeSample('<?php print \'example\';'),
                new CodeSample('<?php echo(\'example\');', array('use' => 'print')),
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // should run after NoShortEchoTagFixer.
        return -10;
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound($this->candidateTokenType);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        $callBack = $this->callBack;
        foreach ($tokens as $index => $token) {
            if ($token->isGivenKind($this->candidateTokenType)) {
                $this->$callBack($tokens, $index);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition()
    {
        $use = new FixerOptionBuilder('use', 'The desired language construct.');
        $use = $use
            ->setAllowedValues(array('print', 'echo'))
            ->setDefault('echo')
            ->getOption()
        ;

        return new FixerConfigurationResolver(array($use));
    }

    /**
     * @param Tokens $tokens
     * @param int    $index
     */
    private function fixEchoToPrint(Tokens $tokens, $index)
    {
        /*
         * HHVM parses '<?=' as T_ECHO instead of T_OPEN_TAG_WITH_ECHO
         *
         * @see https://github.com/facebook/hhvm/issues/4809
         * @see https://github.com/facebook/hhvm/issues/7161
         */
        if (
            defined('HHVM_VERSION')
            && 0 === strpos($tokens[$index]->getContent(), '<?=')
        ) {
            return;
        }

        $nextTokenIndex = $tokens->getNextMeaningfulToken($index);
        $endTokenIndex = $tokens->getNextTokenOfKind($index, array(';', array(T_CLOSE_TAG)));
        $canBeConverted = true;

        for ($i = $nextTokenIndex; $i < $endTokenIndex; ++$i) {
            if ($tokens[$i]->equalsAny(array('(', array(CT::T_ARRAY_SQUARE_BRACE_OPEN)))) {
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

        $tokens[$index] = new Token(array(T_PRINT, 'print'));
    }

    /**
     * @param Tokens $tokens
     * @param int    $index
     */
    private function fixPrintToEcho(Tokens $tokens, $index)
    {
        $prevToken = $tokens[$tokens->getPrevMeaningfulToken($index)];

        if (!$prevToken->equalsAny(array(';', '{', '}', array(T_OPEN_TAG)))) {
            return;
        }

        $tokens[$index] = new Token(array(T_ECHO, 'echo'));
    }
}
