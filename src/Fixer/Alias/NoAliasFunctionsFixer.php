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
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Vladimir Reznichenko <kalessil@gmail.com>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class NoAliasFunctionsFixer extends AbstractFixer
{
    /** @var string[] stores alias (key) - master (value) functions mapping */
    private static $aliases = array(
        'chop' => 'rtrim',
        'close' => 'closedir',
        'doubleval' => 'floatval',
        'fputs' => 'fwrite',
        'ini_alter' => 'ini_set',
        'is_double' => 'is_float',
        'is_integer' => 'is_int',
        'is_long' => 'is_int',
        'is_real' => 'is_float',
        'is_writeable' => 'is_writable',
        'join' => 'implode',
        'key_exists' => 'array_key_exists',
        'magic_quotes_runtime' => 'set_magic_quotes_runtime',
        'pos' => 'current',
        'show_source' => 'highlight_file',
        'sizeof' => 'count',
        'strchr' => 'strstr',
    );

    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Master functions shall be used instead of aliases.',
            array(
                new CodeSample(
'<?php
$a = chop($b);
close($b);
$a = doubleval($b);
$a = fputs($b, $c);
ini_alter($b, $c);
$a = is_double($b);
$a = is_integer($b);
$a = is_long($b);
$a = is_real($b);
$a = is_writeable($b);
$a = join($glue, $pieces);
$a = key_exists($key, $array);
magic_quotes_runtime($new_setting);
$a = pos($array);
$a = show_source($filename, true);
$a = sizeof($b);
$a = strchr($haystack, $needle);
'
                ),
            ),
            null,
            'Risky when any of the alias functions are overridden.'
        );
    }

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
    public function isRisky()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
    {
        /** @var $token \PhpCsFixer\Tokenizer\Token */
        foreach ($tokens->findGivenKind(T_STRING) as $index => $token) {
            // check mapping hit
            $tokenContent = strtolower($token->getContent());
            if (!isset(self::$aliases[$tokenContent])) {
                continue;
            }

            // skip expressions without parameters list
            $nextToken = $tokens[$tokens->getNextMeaningfulToken($index)];
            if (!$nextToken->equals('(')) {
                continue;
            }

            // skip expressions which are not function reference
            $prevTokenIndex = $tokens->getPrevMeaningfulToken($index);
            $prevToken = $tokens[$prevTokenIndex];
            if ($prevToken->isGivenKind(array(T_DOUBLE_COLON, T_NEW, T_OBJECT_OPERATOR, T_FUNCTION, CT::T_RETURN_REF))) {
                continue;
            }

            // handle function reference with namespaces
            if ($prevToken->isGivenKind(array(T_NS_SEPARATOR))) {
                $twicePrevTokenIndex = $tokens->getPrevMeaningfulToken($prevTokenIndex);
                $twicePrevToken = $tokens[$twicePrevTokenIndex];
                if ($twicePrevToken->isGivenKind(array(T_DOUBLE_COLON, T_NEW, T_OBJECT_OPERATOR, T_FUNCTION, T_STRING, CT::T_NAMESPACE_OPERATOR))) {
                    continue;
                }
            }

            $token->setContent(self::$aliases[$tokenContent]);
        }
    }
}
