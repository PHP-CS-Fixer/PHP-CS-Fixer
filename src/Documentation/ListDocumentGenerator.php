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

namespace PhpCsFixer\Documentation;

use PhpCsFixer\Console\Command\HelpCommand;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\DeprecatedFixerInterface;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\FixerConfiguration\AliasedFixerOption;
use PhpCsFixer\FixerConfiguration\AllowedValueSubset;
use PhpCsFixer\FixerConfiguration\DeprecatedFixerOptionInterface;
use PhpCsFixer\RuleSet\RuleSet;
use PhpCsFixer\RuleSet\RuleSets;
use PhpCsFixer\Utils;

/**
 * @internal
 */
final class ListDocumentGenerator
{
    private DocumentationLocator $locator;

    public function __construct(DocumentationLocator $locator)
    {
        $this->locator = $locator;
    }

    /**
     * @param FixerInterface[] $fixers
     */
    public function generateListingDocumentation(array $fixers): string
    {
        usort(
            $fixers,
            static function (FixerInterface $fixer1, FixerInterface $fixer2): int {
                return strnatcasecmp($fixer1->getName(), $fixer2->getName());
            }
        );

        $documentation = <<<'RST'
=======================
List of Available Rules
=======================

RST;
        foreach ($fixers as $fixer) {
            $name = $fixer->getName();
            $definition = $fixer->getDefinition();
            $path = './rules/'.$this->locator->getFixerDocumentationFileRelativePath($fixer);

            $documentation .= "\n-  `{$name} <{$path}>`_\n";
            $documentation .= "\n   ".str_replace('`', '``', $definition->getSummary())."\n";

            $description = $definition->getDescription();

            if (null !== $description) {
                $documentation .= "\n   ".RstUtils::toRst($description, 3)."\n";
            }

            if ($fixer instanceof DeprecatedFixerInterface) {
                $documentation .= "\n   *warning deprecated*";
                $alternatives = $fixer->getSuccessorsNames();

                if (0 !== \count($alternatives)) {
                    $documentation .= RstUtils::toRst(sprintf(
                        '   Use %s instead.',
                        Utils::naturalLanguageJoinWithBackticks($alternatives)
                    ), 3);
                }

                $documentation .= "\n";
            }

            if ($fixer->isRisky()) {
                $documentation .= "\n   *warning risky* ".RstUtils::toRst($definition->getRiskyDescription(), 3)."\n";
            }

            if ($fixer instanceof ConfigurableFixerInterface) {
                $documentation .= "\n   Configuration options:\n";
                $configurationDefinition = $fixer->getConfigurationDefinition();

                foreach ($configurationDefinition->getOptions() as $option) {
                    $documentation .= "\n   - | ``{$option->getName()}``";
                    $documentation .= "\n     | {$option->getDescription()}";

                    if ($option instanceof DeprecatedFixerOptionInterface) {
                        $deprecationMessage = RstUtils::toRst($option->getDeprecationMessage(), 3);
                        $documentation .= "\n     | warning:: This option is deprecated and will be removed on next major version. {$deprecationMessage}";
                    }

                    if ($option instanceof AliasedFixerOption) {
                        $documentation .= "\n     | note:: The previous name of this option was ``{$option->getAlias()}`` but it is now deprecated and will be removed on next major version.";
                    }

                    $allowed = HelpCommand::getDisplayableAllowedValues($option);

                    if (null === $allowed) {
                        $allowedKind = 'Allowed types';
                        $allowed = array_map(static function ($value): string {
                            return '``'.$value.'``';
                        }, $option->getAllowedTypes());
                    } else {
                        $allowedKind = 'Allowed values';

                        foreach ($allowed as &$value) {
                            if ($value instanceof AllowedValueSubset) {
                                $value = 'a subset of ``'.HelpCommand::toString($value->getAllowedValues()).'``';
                            } else {
                                $value = '``'.HelpCommand::toString($value).'``';
                            }
                        }
                    }

                    $allowed = implode(', ', $allowed);
                    $documentation .= "\n     | {$allowedKind}: {$allowed}";

                    if ($option->hasDefault()) {
                        $default = HelpCommand::toString($option->getDefault());
                        $documentation .= "\n     | Default value: ``{$default}``";
                    } else {
                        $documentation .= "\n     | This option is required.";
                    }
                }

                $documentation .= "\n\n";
            }

            $ruleSetConfigs = [];

            foreach (RuleSets::getSetDefinitionNames() as $set) {
                $ruleSet = new RuleSet([$set => true]);

                if ($ruleSet->hasRule($name)) {
                    $ruleSetConfigs[$set] = $ruleSet->getRuleConfiguration($name);
                }
            }

            if ([] !== $ruleSetConfigs) {
                $plural = 1 !== \count($ruleSetConfigs) ? 's' : '';

                $documentation .= "\n   Part of rule set{$plural} ";

                foreach ($ruleSetConfigs as $set => $config) {
                    $ruleSetPath = $this->locator->getRuleSetsDocumentationFilePath($set);
                    $ruleSetPath = substr($ruleSetPath, strrpos($ruleSetPath, '/'));

                    $documentation .= "`{$set} <./ruleSets{$ruleSetPath}>`_ ";
                }

                $documentation = rtrim($documentation)."\n";
            }

            $reflectionObject = new \ReflectionObject($fixer);
            $className = str_replace('\\', '\\\\', $reflectionObject->getName());
            $fileName = $reflectionObject->getFileName();
            $fileName = str_replace('\\', '/', $fileName);
            $fileName = substr($fileName, strrpos($fileName, '/src/Fixer/') + 1);
            $fileName = "`Source {$className} <./../{$fileName}>`_";
            $documentation .= "\n   ".$fileName;
        }

        return $documentation."\n";
    }
}
