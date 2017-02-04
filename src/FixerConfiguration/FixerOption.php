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

use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

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
     * @var array|null
     */
    private $allowedTypes;

    /**
     * @var array|null
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
     * @param string[]|string|null $allowedTypes
     *
     * @return $this
     */
    public function setAllowedTypes($allowedTypes)
    {
        if (null !== $allowedTypes && !is_array($allowedTypes)) {
            $allowedTypes = array($allowedTypes);
        }

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
     * @param string[]|string|null $allowedValues
     *
     * @return $this
     */
    public function setAllowedValues($allowedValues)
    {
        if (!is_array($allowedValues)) {
            $allowedValues = array($allowedValues);
        }

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
     * Sets the given option to only accept an array with a subset of the given values.
     *
     * @param array $allowedArrayValues
     *
     * @return $this
     */
    public function setAllowedValueIsSubsetOf(array $allowedArrayValues)
    {
        $option = $this->name;

        return $this
            ->setAllowedTypes('array')
            ->setAllowedValues($this->unbind(function ($values) use ($option, $allowedArrayValues) {
                foreach ($values as $value) {
                    if (!in_array($value, $allowedArrayValues, true)) {
                        throw new InvalidOptionsException(sprintf(
                            'The option "%s" contains an invalid value.',
                            $option
                        ));
                    }
                }

                return true;
            }))
        ;
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

    /**
     * Unbinds the given closure to avoid memory leaks. See {@see https://bugs.php.net/bug.php?id=69639 Bug #69639} for
     * details.
     *
     * @param \Closure $closure
     *
     * @return \Closure
     */
    private function unbind(\Closure $closure)
    {
        if (PHP_VERSION_ID < 50400) {
            return $closure;
        }

        return $closure->bindTo(null);
    }
}
