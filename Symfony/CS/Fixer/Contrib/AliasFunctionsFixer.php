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
 * Fixes AliasFunctionsUsageInspection inspection warnings from Php Inspections (EA Extended).
 * This fixer is based on JoinFunctionFixer code from Dariusz Rumiński.
 *
 * @author Vladimir Reznichenko <kalessil@gmail.com>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class AliasFunctionsFixer extends AbstractFixer
{
    /**
     * @var string[]
     */
    private static $aliases = array(
        'is_double' => 'is_float',
        'is_integer' => 'is_int',
        'is_long' => 'is_int',
        'is_real' => 'is_float',
        'sizeof' => 'count',
        'doubleval' => 'floatval',
        'fputs' => 'fwrite',
        'join' => 'implode',
        'key_exists' => 'array_key_exists',
        'chop' => 'rtrim',
        'close' => 'closedir',
        'ini_alter' => 'ini_set',
        'is_writeable' => 'is_writable',
        'magic_quotes_runtime' => 'set_magic_quotes_runtime',
        'pos' => 'current',
        'rewind' => 'rewinddir',
        'show_source' => 'highlight_file',
        'strchr' => 'strstr',
    );

    /**
     * @return string[]
     */
    public static function getAliases()
    {
        return self::$aliases;
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        foreach ($tokens->findGivenKind(T_STRING) as $index => $token) {
            $tokenContent = strtolower($token->getContent());
            if (!array_key_exists($tokenContent, self::$aliases)) {
                continue;
            }

            $prevToken = $tokens[$tokens->getPrevMeaningfulToken($index)];
            if ($prevToken->isGivenKind(array(T_DOUBLE_COLON, T_NEW, T_NS_SEPARATOR, T_OBJECT_OPERATOR, T_FUNCTION))) {
                continue;
            }

            $nextToken = $tokens[$tokens->getNextMeaningfulToken($index)];
            if (!$nextToken->equals('(')) {
                continue;
            }

            $token->setContent(self::$aliases[$tokenContent]);
        }

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Master functions shall be used instead of aliases.';
    }
}
