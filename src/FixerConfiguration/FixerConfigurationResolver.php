<?php

declare(strict_types=1);

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

use PhpCsFixer\Preg;
use PhpCsFixer\Utils;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class FixerConfigurationResolver implements FixerConfigurationResolverInterface
{
    /**
     * @var list<FixerOptionInterface>
     */
    private array $options = [];

    /**
     * @var list<string>
     */
    private array $registeredNames = [];

    /**
     * @param iterable<FixerOptionInterface> $options
     */
    public function __construct(iterable $options)
    {
        $fixerOptionSorter = new FixerOptionSorter();

        foreach ($fixerOptionSorter->sort($options) as $option) {
            $this->addOption($option);
        }

        if (0 === \count($this->registeredNames)) {
            throw new \LogicException('Options cannot be empty.');
        }
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function resolve(array $configuration): array
    {
        $resolver = new OptionsResolver();

        foreach ($this->options as $option) {
            $name = $option->getName();

            if ($option instanceof AliasedFixerOption) {
                $alias = $option->getAlias();

                if (\array_key_exists($alias, $configuration)) {
                    if (\array_key_exists($name, $configuration)) {
                        throw new InvalidOptionsException(sprintf('Aliased option "%s"/"%s" is passed multiple times.', $name, $alias));
                    }

                    Utils::triggerDeprecation(new \RuntimeException(sprintf(
                        'Option "%s" is deprecated, use "%s" instead.',
                        $alias,
                        $name
                    )));

                    $configuration[$name] = $configuration[$alias];
                    unset($configuration[$alias]);
                }
            }

            if ($option->hasDefault()) {
                $resolver->setDefault($name, $option->getDefault());
            } else {
                $resolver->setRequired($name);
            }

            $allowedValues = $option->getAllowedValues();
            if (null !== $allowedValues) {
                foreach ($allowedValues as &$allowedValue) {
                    if (\is_object($allowedValue) && \is_callable($allowedValue)) {
                        $allowedValue = static fn (/* mixed */ $values) => $allowedValue($values);
                    }
                }

                $resolver->setAllowedValues($name, $allowedValues);
            }

            $allowedTypes = $option->getAllowedTypes();
            if (null !== $allowedTypes) {
                // Symfony OptionsResolver doesn't support `array<foo, bar>` natively, let's simplify the type
                $allowedTypesNormalised = array_map(
                    static function (string $type): string {
                        $matches = [];
                        if (true === Preg::match('/array<\w+,\s*(\??[\w\'|]+)>/', $type, $matches)) {
                            if ('?' === $matches[1][0]) {
                                return 'array';
                            }

                            if ("'" === $matches[1][0]) {
                                return 'string[]';
                            }

                            return $matches[1].'[]';
                        }

                        return $type;
                    },
                    $allowedTypes,
                );

                $resolver->setAllowedTypes($name, $allowedTypesNormalised);
            }

            $normalizer = $option->getNormalizer();
            if (null !== $normalizer) {
                $resolver->setNormalizer($name, $normalizer);
            }
        }

        return $resolver->resolve($configuration);
    }

    /**
     * @throws \LogicException when the option is already defined
     */
    private function addOption(FixerOptionInterface $option): void
    {
        $name = $option->getName();

        if (\in_array($name, $this->registeredNames, true)) {
            throw new \LogicException(sprintf('The "%s" option is defined multiple times.', $name));
        }

        $this->options[] = $option;
        $this->registeredNames[] = $name;
    }
}
