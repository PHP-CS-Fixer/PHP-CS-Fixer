<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer;

use Symfony\CS\FixerInterface;

/**
 * @author Dariusz Ruminski <dariusz.ruminski@gmail.com>
 */
class LowercaseKeywordsFixer implements FixerInterface
{
    public function fix(\SplFileInfo $file, $content)
    {
        $keywords = $this->getKeywords();

        $codeContent = '';

        foreach (token_get_all($content) as $token) {
            if (is_array($token)) {
                $token = isset($keywords[$token[0]]) ? strtolower($token[1]) : $token[1];
            }

            $codeContent .= $token;
        }

        return $codeContent;
    }

    public function getLevel()
    {
        // defined in PSR2 ¶2.5
        return FixerInterface::PSR2_LEVEL;
    }

    public function getPriority()
    {
        return 0;
    }

    public function supports(\SplFileInfo $file)
    {
        return 'php' == pathinfo($file->getFilename(), PATHINFO_EXTENSION);
    }

    public function getName()
    {
        return 'lowercase_keywords';
    }

    public function getDescription()
    {
        return 'PHP keywords MUST be in lower case.';
    }

    private static function getKeywords()
    {
        static $keywords = null;

        if (null === $keywords) {
            $keywords = array();
            $keywordsStrings = array('T_ABSTRACT', 'T_ARRAY', 'T_AS', 'T_BREAK', 'T_CALLABLE', 'T_CASE',
                'T_CATCH', 'T_CLASS', 'T_CLONE', 'T_CONST', 'T_CONTINUE', 'T_DECLARE', 'T_DEFAULT', 'T_DO',
                'T_ECHO', 'T_ELSE', 'T_ELSEIF', 'T_EMPTY', 'T_ENDDECLARE', 'T_ENDFOR', 'T_ENDFOREACH',
                'T_ENDIF', 'T_ENDSWITCH', 'T_ENDWHILE', 'T_EVAL', 'T_EXIT', 'T_EXTENDS', 'T_FINAL',
                'T_FINALLY', 'T_FOR', 'T_FOREACH', 'T_FUNCTION', 'T_GLOBAL', 'T_GOTO', 'T_HALT_COMPILER',
                'T_IF', 'T_IMPLEMENTS', 'T_INCLUDE', 'T_INCLUDE_ONCE', 'T_INSTANCEOF', 'T_INSTEADOF',
                'T_INTERFACE', 'T_ISSET', 'T_LIST', 'T_LOGICAL_AND', 'T_LOGICAL_OR', 'T_LOGICAL_XOR',
                'T_NAMESPACE', 'T_NEW', 'T_PRINT', 'T_PRIVATE', 'T_PROTECTED', 'T_PUBLIC', 'T_REQUIRE',
                'T_REQUIRE_ONCE', 'T_RETURN', 'T_STATIC', 'T_SWITCH', 'T_THROW', 'T_TRAIT', 'T_TRY',
                'T_UNSET', 'T_USE', 'T_VAR', 'T_WHILE', 'T_YIELD'
            );

            foreach ($keywordsStrings as $keyword) {
                if (defined($keyword)) {
                    $keywords[constant($keyword)] = true;
                }
            }
        }

        return $keywords;
    }
}
