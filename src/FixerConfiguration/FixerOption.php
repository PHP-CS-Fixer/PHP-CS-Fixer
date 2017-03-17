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

namespace PhpCsFixer\FixerConfiguration;

final class FixerOption implements FixerOptionInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $description;

    /**
     * @var mixed
     */
    private $default;

    /**
     * @var bool
     */
    private $useDefault = false;

    /**
     * @var null|string[]
     */
    private $allowedTypes;

    /**
     * @var null|array
     */
    private $allowedValues;

    /**
     * @var \Closure|null
     */
    private $normalizer;

    /**
     * @param string $name
     * @param string $description
     */
    public function __construct($name, $description)
    {
        $this->name = $name;
        $this->description = $description;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $default
     *
     * @return $this
     */
    public function setDefault($default)
    {
        $this->default = $default;
        $this->useDefault = true;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasDefault()
    {
        return $this->useDefault;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefault()
    {
        if (!$this->hasDefault()) {
            throw new \LogicException('No default value defined.');
        }

        return $this->default;
    }

    /**
     * @param string[] $allowedTypes
     *
     * @return $this
     */
    public function setAllowedTypes(array $allowedTypes)
    {
        $this->allowedTypes = $allowedTypes;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAllowedTypes()
    {
        return $this->allowedTypes;
    }

    /**
     * @param array $allowedValues
     *
     * @return $this
     */
    public function setAllowedValues(array $allowedValues)
    {
        $this->allowedValues = $allowedValues;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAllowedValues()
    {
        return $this->allowedValues;
    }

    /**
     * @param \Closure $normalizer
     *
     * @return $this
     */
    public function setNormalizer(\Closure $normalizer)
    {
        $this->normalizer = $normalizer;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getNormalizer()
    {
        return $this->normalizer;
    }
}
