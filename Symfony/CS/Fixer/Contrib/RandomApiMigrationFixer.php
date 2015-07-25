<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer\Contrib;

use Symfony\CS\AbstractFixer;
use Symfony\CS\Tokenizer\Tokens;

/**
 * This fixer in general identical to AliasFunctionsFixer but aims
 * to modernize random api calls, therefore it's separated fixer.
 *
 * @author Vladimir Reznichenko <kalessil@gmail.com>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class RandomApiMigrationFixer extends AbstractFixer
{
    /** @var string[] stores older (key) - newer (value) functions mapping */
    private static $replacements = array(
        'rand' => 'mt_rand',
        'srand' => 'mt_srand',
        'getrandmax' => 'mt_getrandmax',
    );

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(T_STRING);
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        foreach ($tokens->findGivenKind(T_STRING) as $index => $token) {
            // skip expressions without parameters list
            $nextToken = $tokens[$tokens->getNextMeaningfulToken($index)];
            if (!$nextToken->equals('(')) {
                continue;
            }

            // skip expressions which are not function reference
            $prevTokenIndex = $tokens->getPrevMeaningfulToken($index);
            $prevToken = $tokens[$prevTokenIndex];
            if ($prevToken->isGivenKind(array(T_DOUBLE_COLON, T_NEW, T_OBJECT_OPERATOR, T_FUNCTION))) {
                continue;
            }

            // handle function reference with namespaces
            if ($prevToken->isGivenKind(array(T_NS_SEPARATOR))) {
                $twicePrevTokenIndex = $tokens->getPrevMeaningfulToken($prevTokenIndex);
                $twicePrevToken = $tokens[$twicePrevTokenIndex];
                if ($twicePrevToken->isGivenKind(array(T_NEW, T_STRING, CT_NAMESPACE_OPERATOR))) {
                    continue;
                }
            }

            // check mapping hit
            $tokenContent = strtolower($token->getContent());
            if (!isset(self::$replacements[$tokenContent])) {
                continue;
            }

            $token->setContent(self::$replacements[$tokenContent]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Replaces rand, srand, getrandmax functions calls with their mt_* analogs.';
    }
}
