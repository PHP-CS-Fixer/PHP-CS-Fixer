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
use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Sullivan Senechal <soullivaneuh@gmail.com>
 * @author SpacePossum
 */
final class NoMixedEchoPrintFixer extends AbstractFixer implements ConfigurableFixerInterface
{
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
        if (null === $configuration) {
            $configuration = self::$defaultConfig;
        } else {
            if (1 !== count($configuration) || !array_key_exists('use', $configuration) || !in_array($configuration['use'], array('print', 'echo'), true)) {
                throw new InvalidFixerConfigurationException($this->getName(), sprintf(
                    'Expected array of element "use" with value "echo" or "print", got "%s".',
                    var_export($configuration, true)
                ));
            }
        }

        $this->resolveConfig($configuration);
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
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
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound($this->candidateTokenType);
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Either language construct `print` or `echo` should be used.';
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

        $tokens->overrideAt($index, array(T_PRINT, 'print'));
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

        $tokens->overrideAt($index, array(T_ECHO, 'echo'));
    }

    private function resolveConfig(array $configuration)
    {
        if ('echo' === $configuration['use']) {
            $this->candidateTokenType = T_PRINT;
            $this->callBack = 'fixPrintToEcho';
        } else {
            $this->candidateTokenType = T_ECHO;
            $this->callBack = 'fixEchoToPrint';
        }
    }
}
