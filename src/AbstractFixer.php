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
use PhpCsFixer\ConfigurationException\RequiredFixerConfigurationException;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use PhpCsFixer\Fixer\DefinedFixerInterface;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
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
     * @var array<string, mixed>|null
     */
    protected $configuration;

    /**
     * @var WhitespacesFixerConfig
     */
    protected $whitespacesConfig;

    public function __construct()
    {
        if ($this instanceof ConfigurableFixerInterface) {
            try {
                $this->configure(array());
            } catch (RequiredFixerConfigurationException $e) {
                // ignore
            }
        }

        if ($this instanceof WhitespacesAwareFixerInterface) {
            $this->whitespacesConfig = $this->getDefaultWhitespacesFixerConfig();
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

    /**
     * {@inheritdoc}
     */
    public function configure(array $configuration = null)
    {
        if (!$this instanceof ConfigurationDefinitionFixerInterface) {
            throw new \LogicException('Cannot run method for class not implementing `ConfigurationDefinitionFixerInterface`.');
        }

        if (null === $configuration) {
            @trigger_error(
                'Passing NULL to set default configuration is deprecated and will not be supported in 3.0, use an empty array instead.',
                E_USER_DEPRECATED
            );

            $configuration = array();
        }

        try {
            $this->configuration = $this->getConfigurationDefinition()->resolve($configuration);
        } catch (MissingOptionsException $exception) {
            throw new RequiredFixerConfigurationException(
                $this->getName(),
                sprintf('Missing required configuration: %s', $exception->getMessage()),
                null,
                $exception
            );
        } catch (ExceptionInterface $exception) {
            throw new InvalidFixerConfigurationException(
                $this->getName(),
                sprintf('Invalid configuration: %s', $exception->getMessage()),
                null,
                $exception
            );
        }
    }

    public function setWhitespacesConfig(WhitespacesFixerConfig $config)
    {
        if (!$this instanceof WhitespacesAwareFixerInterface) {
            throw new \LogicException('Cannot run method for class not implementing `WhitespacesAwareFixerInterface`.');
        }

        $this->whitespacesConfig = $config;
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
