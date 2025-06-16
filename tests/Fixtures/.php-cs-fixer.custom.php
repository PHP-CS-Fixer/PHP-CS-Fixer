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
use PhpCsFixer\FailOnUnsupportedVersionConfigInterface;
use PhpCsFixer\UnsupportedPhpVersionAllowedConfigInterface;

/**
 * Custom config class/file for PHPUnit test.
 *
 * This class does NOT represent a good/sane configuration and is therefore NOT an example.
 *
 * @internal
 */
final class CustomConfig implements ConfigInterface, UnsupportedPhpVersionAllowedConfigInterface
{
    /**
     * {@inheritdoc}
     */
    public function getCacheFile(): ?string
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomFixers(): array
    {
        return array();
    }

    /**
     * {@inheritdoc}
     */
    public function getFinder(): iterable
    {
        return array(__FILE__);
    }

    /**
     * {@inheritdoc}
     */
    public function getFormat(): string
    {
        return 'txt';
    }

    /**
     * {@inheritdoc}
     */
    public function getHideProgress(): bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getIndent(): string
    {
        return '  ';
    }

    /**
     * {@inheritdoc}
     */
    public function getLineEnding(): string
    {
        return "\n";
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'custom_config_test';
    }

    /**
     * {@inheritdoc}
     */
    public function getPhpExecutable(): ?string
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getRiskyAllowed(): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getRules(): array
    {
        return array('concat_space' => array('spacing' => 'none'));
    }

    /**
     * {@inheritdoc}
     */
    public function getUsingCache(): bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function registerCustomFixers(iterable $fixers): ConfigInterface
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setCacheFile(string $cacheFile): ConfigInterface
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setFinder(iterable $finder): ConfigInterface
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setFormat(string $format): ConfigInterface
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setHideProgress(bool $hideProgress): ConfigInterface
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setIndent(string $indent): ConfigInterface
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setLineEnding(string $lineEnding): ConfigInterface
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setPhpExecutable(?string $phpExecutable): ConfigInterface
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setRiskyAllowed(bool $isRiskyAllowed): ConfigInterface
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setRules(array $rules): ConfigInterface
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setUsingCache(bool $usingCache): ConfigInterface
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUnsupportedPhpVersionAllowed(): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function setUnsupportedPhpVersionAllowed(bool $isUnsupportedPhpVersionAllowed): ConfigInterface
    {
        return $this;
    }
}

return new CustomConfig();
