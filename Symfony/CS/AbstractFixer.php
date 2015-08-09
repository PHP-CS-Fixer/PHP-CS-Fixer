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
 *
 * @internal
 */
abstract class AbstractFixer implements FixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getLevel()
    {
        static $map = array(
            'PSR1' => FixerInterface::PSR1_LEVEL,
            'PSR2' => FixerInterface::PSR2_LEVEL,
            'Symfony' => FixerInterface::SYMFONY_LEVEL,
            'Contrib' => FixerInterface::CONTRIB_LEVEL,
        );

        $level = current(explode('\\', substr(get_called_class(), strlen(__NAMESPACE__.'\\Fixer\\'))));

        if (!isset($map[$level])) {
            throw new \LogicException(sprintf('Can not determine Fixer level: "%s".', $level));
        }

        return $map[$level];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        $nameParts = explode('\\', get_called_class());
        $name = substr(end($nameParts), 0, -strlen('Fixer'));

        return Utils::camelCaseToUnderscore($name);
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
