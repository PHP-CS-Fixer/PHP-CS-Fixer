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

namespace PhpCsFixer\RuleSet;

use Symfony\Component\Finder\Finder;

/**
 * Set of rule sets to be used by fixer.
 *
 * @author SpacePossum
 *
 * @internal
 */
final class RuleSets
{
    private static $setDefinitions;

    /**
     * @return array<string, RuleSetDescriptionInterface>
     */
    public static function getSetDefinitions()
    {
        if (null === self::$setDefinitions) {
            self::$setDefinitions = [];

            foreach (Finder::create()->files()->in(__DIR__.'/Sets') as $file) {
                $class = 'PhpCsFixer\RuleSet\Sets\\'.$file->getBasename('.php');
                $set = new $class();

                self::$setDefinitions[$set->getName()] = $set;
            }

            ksort(self::$setDefinitions);
        }

        return self::$setDefinitions;
    }

    /**
     * @return string[]
     */
    public static function getSetDefinitionNames()
    {
        return array_keys(self::getSetDefinitions());
    }

    /**
     * @param string $name
     *
     * @return RuleSetDescriptionInterface
     */
    public static function getSetDefinition($name)
    {
        $definitions = self::getSetDefinitions();

        if (!isset($definitions[$name])) {
            throw new \InvalidArgumentException(sprintf('Set "%s" does not exist.', $name));
        }

        return $definitions[$name];
    }
}
