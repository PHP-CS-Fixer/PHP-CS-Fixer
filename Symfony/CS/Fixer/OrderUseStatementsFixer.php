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
 * @author Paweł Zaremba <pawzar@gmail.com>
 */
class OrderUseStatementsFixer implements FixerInterface
{

    public function fix(\SplFileInfo $file, $content)
    {
        preg_match_all('/^use[^;]+;/m', $content, $matches);
        if (count($useStatements = $matches[0]) > 1) {
            $orderedUseStatements = $useStatements;
            sort($orderedUseStatements);
        }
        if (isset($orderedUseStatements) && $useStatements !== $orderedUseStatements) {
            $md5 = array_map(function($str) {
                return md5(time() . $str);
            }, $useStatements);
            $content = str_replace($useStatements, $md5, $content);
            $content = str_replace($md5, $orderedUseStatements, $content);
        }
        return $content;
    }

    public function getLevel()
    {
        return FixerInterface::ALL_LEVEL;
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
        return 'ordered_use';
    }

    public function getDescription()
    {
        return 'Ordering use statements.';
    }

}
