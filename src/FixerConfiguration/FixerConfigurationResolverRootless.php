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

/**
 * @internal
 *
 * @deprecated will be removed in 3.0
 */
final class FixerConfigurationResolverRootless implements FixerConfigurationResolverInterface
{
    /**
     * @var FixerConfigurationResolverInterface
     */
    private $resolver;

    /**
     * @var string
     */
    private $root;

    /**
     * @param string                         $root
     * @param iterable<FixerOptionInterface> $options
     */
    public function __construct($root, $options)
    {
        $this->resolver = new FixerConfigurationResolver($options);

        $names = array_map(
            function (FixerOptionInterface $option) {
                return $option->getName();
            },
            $this->resolver->getOptions()
        );

        if (!in_array($root, $names, true)) {
            throw new \LogicException(sprintf('The "%s" option is not defined.', $root));
        }

        $this->root = $root;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        return $this->resolver->getOptions();
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(array $options)
    {
        if (!empty($options) && !array_key_exists($this->root, $options)) {
            @trigger_error(sprintf(
                'Passing "%1$s" at the root of the configuration is deprecated and will not be supported in 3.0, use "%1$s" => array(...) option instead.',
                $this->root
            ), E_USER_DEPRECATED);

            $options = array($this->root => $options);
        }

        return $this->resolver->resolve($options);
    }
}
