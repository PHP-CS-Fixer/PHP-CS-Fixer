<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
abstract class AbstractFixer implements FixerInterface
{
    protected static function camelCaseToUnderscore($string)
    {
        return preg_replace_callback(
            '/(^|[a-z])([A-Z])/',
            function (array $matches) {
                return strtolower(strlen($matches[1]) ? $matches[1].'_'.$matches[2] : $matches[2]);
            },
            $string
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getLevel()
    {
        static $map = array(
            'PSR0' => FixerInterface::PSR0_LEVEL,
            'PSR1' => FixerInterface::PSR1_LEVEL,
            'PSR2' => FixerInterface::PSR2_LEVEL,
            'All' => FixerInterface::ALL_LEVEL,
            'Contrib' => FixerInterface::CONTRIB_LEVEL,
        );

        return $map[current(explode('\\', substr(get_called_class(), strlen(__NAMESPACE__.'\\Fixer\\'))))];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        $nameParts = explode('\\', get_called_class());
        $name = substr(end($nameParts), 0, -strlen('Fixer'));

        return self::camelCaseToUnderscore($name);
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(\SplFileInfo $file)
    {
        return true;
    }
}
