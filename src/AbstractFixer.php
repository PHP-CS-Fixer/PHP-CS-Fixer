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

namespace PhpCsFixer;

use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\ConfigurationException\InvalidForEnvFixerConfigurationException;
use PhpCsFixer\ConfigurationException\RequiredFixerConfigurationException;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use PhpCsFixer\Fixer\DefinedFixerInterface;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
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
abstract class AbstractFixer implements FixerInterface, DefinedFixerInterface
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

    final public function fix(\SplFileInfo $file, Tokens $tokens)
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
    public function isRisky()
    {
        return false;
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

    public function configure(array $configuration = null)
    {
        if (!$this instanceof ConfigurationDefinitionFixerInterface) {
            throw new \LogicException('Cannot configure using Abstact parent, child not implementing "PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface".');
        }

        if (null === $configuration) {
            @trigger_error(
                'Passing NULL to set default configuration is deprecated and will not be supported in 3.0, use an empty array instead.',
                E_USER_DEPRECATED
            );

            $configuration = [];
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

    /**
     * {@inheritdoc}
     */
    public function getConfigurationDefinition()
    {
        if (!$this instanceof ConfigurationDefinitionFixerInterface) {
            throw new \LogicException('Cannot get configuration definition using Abstact parent, child not implementing "PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface".');
        }

        if (null === $this->configurationDefinition) {
            $this->configurationDefinition = $this->createConfigurationDefinition();
        }

        return $this->configurationDefinition;
    }

    public function setWhitespacesConfig(WhitespacesFixerConfig $config)
    {
        if (!$this instanceof WhitespacesAwareFixerInterface) {
            throw new \LogicException('Cannot run method for class not implementing `WhitespacesAwareFixerInterface`.');
        }

        $this->whitespacesConfig = $config;
    }

    abstract protected function applyFix(\SplFileInfo $file, Tokens $tokens);

    /**
     * @return FixerConfigurationResolverInterface
     */
    protected function createConfigurationDefinition()
    {
        if (!$this instanceof ConfigurationDefinitionFixerInterface) {
            throw new \LogicException('Cannot create configuration definition using Abstact parent, child not implementing "PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface".');
        }

        throw new \LogicException('Not implemented.');
    }

    private function getDefaultWhitespacesFixerConfig()
    {
        static $defaultWhitespacesFixerConfig = null;

        if (null === $defaultWhitespacesFixerConfig) {
            $defaultWhitespacesFixerConfig = new WhitespacesFixerConfig('    ', "\n");
        }

        return $defaultWhitespacesFixerConfig;
    }
}
