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

use Symfony\Component\OptionsResolver\OptionsResolver;

final class FixerConfigurationResolver implements FixerConfigurationResolverInterface
{
    /**
     * @var FixerOptionInterface[]
     */
    private $options = array();

    /**
     * @var string[]
     */
    private $registeredNames = array();

    /**
     * @param iterable<FixerOptionInterface> $options
     */
    public function __construct($options)
    {
        foreach ($options as $option) {
            $this->addOption($option);
        }

        if (empty($this->registeredNames)) {
            throw new \LogicException('Options cannot be empty.');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(array $options)
    {
        $resolver = new OptionsResolver();

        foreach ($this->options as $option) {
            $name = $option->getName();

            if ($option->hasDefault()) {
                $resolver->setDefault($name, $option->getDefault());
            } else {
                $resolver->setRequired($name);
            }

            $allowedValues = $option->getAllowedValues();
            if (null !== $allowedValues) {
                $resolver->setAllowedValues($name, $allowedValues);
            }

            $allowedTypes = $option->getAllowedTypes();
            if (null !== $allowedTypes) {
                $resolver->setAllowedTypes($name, $allowedTypes);
            }

            $normalizer = $option->getNormalizer();
            if (null !== $normalizer) {
                $resolver->setNormalizer($name, $normalizer);
            }
        }

        return $resolver->resolve($options);
    }

    /**
     * @param FixerOptionInterface $option
     *
     * @throws \LogicException when the option is already defined
     *
     * @return $this
     */
    private function addOption(FixerOptionInterface $option)
    {
        $name = $option->getName();

        if (in_array($name, $this->registeredNames, true)) {
            throw new \LogicException(sprintf('The "%s" option is defined multiple times.', $name));
        }

        $this->options[] = $option;
        $this->registeredNames[] = $name;

        return $this;
    }
}
