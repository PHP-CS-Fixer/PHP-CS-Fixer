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

use PhpCsFixer\ConfigInterface;

/**
 * Custom config class/file for PHPUnit test.
 *
 * This class does NOT represent a good/sane configuration and is therefor NOT a example.
 *
 * @internal
 *
 * @author SpacePossum
 */
final class CustomConfig implements ConfigInterface
{
    /**
     * {@inheritdoc}
     */
    public function getCacheFile()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomFixers()
    {
        return array();
    }

    /**
     * {@inheritdoc}
     */
    public function getFinder()
    {
        return array(__FILE__);
    }

    /**
     * {@inheritdoc}
     */
    public function getFormat()
    {
        return 'txt';
    }

    /**
     * {@inheritdoc}
     */
    public function getHideProgress()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getIndent()
    {
        return '  ';
    }

    /**
     * {@inheritdoc}
     */
    public function getLineEnding()
    {
        return "\n";
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'custom_config_test';
    }

    /**
     * {@inheritdoc}
     */
    public function getPhpExecutable()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getRiskyAllowed()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getRules()
    {
        return array('concat_space' => array('spacing' => 'none'));
    }

    /**
     * {@inheritdoc}
     */
    public function getUsingCache()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function registerCustomFixers($fixers)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function setCacheFile($cacheFile)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setFinder($finder)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setFormat($format)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setHideProgress($hideProgress)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setIndent($indent)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setLineEnding($lineEnding)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setPhpExecutable($phpExecutable)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setRiskyAllowed($isRiskyAllowed)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setRules(array $rules)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setUsingCache($usingCache)
    {
        return $this;
    }
}

return new CustomConfig();
