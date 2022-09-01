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

namespace PhpCsFixer;

use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\ConfigurationException\InvalidForEnvFixerConfigurationException;
use PhpCsFixer\ConfigurationException\RequiredFixerConfigurationException;
use PhpCsFixer\Console\Application;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerConfiguration\DeprecatedFixerOption;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\InvalidOptionsForEnvException;
use PhpCsFixer\Tokenizer\Tokens;
use Symfony\Component\OptionsResolver\Exception\ExceptionInterface;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
abstract class AbstractFixer implements FixerInterface
{
    /**
     * @var null|array<string, mixed>
     */
    protected $configuration;

    /**
     * @var WhitespacesFixerConfig
     */
    protected $whitespacesConfig;

    /**
     * @var null|FixerConfigurationResolverInterface
     */
    private $configurationDefinition;

    public function __construct()
    {
        if ($this instanceof ConfigurableFixerInterface) {
            try {
                $this->configure([]);
            } catch (RequiredFixerConfigurationException $e) {
                // ignore
            }
        }

        if ($this instanceof WhitespacesAwareFixerInterface) {
            $this->whitespacesConfig = $this->getDefaultWhitespacesFixerConfig();
        }
    }

    final public function fix(\SplFileInfo $file, Tokens $tokens): void
    {
        if ($this instanceof ConfigurableFixerInterface && null === $this->configuration) {
            throw new RequiredFixerConfigurationException($this->getName(), 'Configuration is required.');
        }

        if (0 < $tokens->count() && $this->isCandidate($tokens) && $this->supports($file)) {
            $this->applyFix($file, $tokens);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isRisky(): bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        $nameParts = explode('\\', static::class);
        $name = substr(end($nameParts), 0, -\strlen('Fixer'));

        return Utils::camelCaseToUnderscore($name);
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority(): int
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(\SplFileInfo $file): bool
    {
        return true;
    }

    /**
     * @param array<string, mixed> $configuration
     */
    public function configure(array $configuration): void
    {
        if (!$this instanceof ConfigurableFixerInterface) {
            throw new \LogicException('Cannot configure using Abstract parent, child not implementing "PhpCsFixer\Fixer\ConfigurableFixerInterface".');
        }

        foreach ($this->getConfigurationDefinition()->getOptions() as $option) {
            if (!$option instanceof DeprecatedFixerOption) {
                continue;
            }

            $name = $option->getName();
            if (\array_key_exists($name, $configuration)) {
                Utils::triggerDeprecation(new \InvalidArgumentException(sprintf(
                    'Option "%s" for rule "%s" is deprecated and will be removed in version %d.0. %s',
                    $name,
                    $this->getName(),
                    Application::getMajorVersion() + 1,
                    str_replace('`', '"', $option->getDeprecationMessage())
                )));
            }
        }

        try {
            $this->configuration = $this->getConfigurationDefinition()->resolve($configuration);
        } catch (MissingOptionsException $exception) {
            throw new RequiredFixerConfigurationException(
                $this->getName(),
                sprintf('Missing required configuration: %s', $exception->getMessage()),
                $exception
            );
        } catch (InvalidOptionsForEnvException $exception) {
            throw new InvalidForEnvFixerConfigurationException(
                $this->getName(),
                sprintf('Invalid configuration for env: %s', $exception->getMessage()),
                $exception
            );
        } catch (ExceptionInterface $exception) {
            throw new InvalidFixerConfigurationException(
                $this->getName(),
                sprintf('Invalid configuration: %s', $exception->getMessage()),
                $exception
            );
        }
    }

    public function getConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        if (!$this instanceof ConfigurableFixerInterface) {
            throw new \LogicException(sprintf('Cannot get configuration definition using Abstract parent, child "%s" not implementing "PhpCsFixer\Fixer\ConfigurableFixerInterface".', static::class));
        }

        if (null === $this->configurationDefinition) {
            $this->configurationDefinition = $this->createConfigurationDefinition();
        }

        return $this->configurationDefinition;
    }

    public function setWhitespacesConfig(WhitespacesFixerConfig $config): void
    {
        if (!$this instanceof WhitespacesAwareFixerInterface) {
            throw new \LogicException('Cannot run method for class not implementing "PhpCsFixer\Fixer\WhitespacesAwareFixerInterface".');
        }

        $this->whitespacesConfig = $config;
    }

    abstract protected function applyFix(\SplFileInfo $file, Tokens $tokens): void;

    protected function createConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        if (!$this instanceof ConfigurableFixerInterface) {
            throw new \LogicException('Cannot create configuration definition using Abstract parent, child not implementing "PhpCsFixer\Fixer\ConfigurableFixerInterface".');
        }

        throw new \LogicException('Not implemented.');
    }

    private function getDefaultWhitespacesFixerConfig(): WhitespacesFixerConfig
    {
        static $defaultWhitespacesFixerConfig = null;

        if (null === $defaultWhitespacesFixerConfig) {
            $defaultWhitespacesFixerConfig = new WhitespacesFixerConfig('    ', "\n");
        }

        return $defaultWhitespacesFixerConfig;
    }
}
